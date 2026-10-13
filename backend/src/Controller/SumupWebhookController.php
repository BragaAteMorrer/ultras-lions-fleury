<?php

namespace App\Controller;

use App\Entity\CartageRegistration;
use App\Entity\Donation;
use App\Entity\MerchOrder;
use App\Entity\TicketOrder;
use App\Repository\CartageRegistrationRepository;
use App\Repository\DonationRepository;
use App\Repository\MerchRepository;
use App\Repository\TicketRepository;
use App\Repository\PaymentCheckoutRepository;
use App\Repository\UserRepository;
use App\Service\CartageProfileCreator;
use App\Service\DonationPaymentService;
use App\Service\MailService;
use App\Service\SumupService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\LockMode;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SumupWebhookController extends AbstractController
{
    #[Route('/sumup/webhook', name: 'sumup_webhook', methods: ['POST'])]
    public function webhook(
        Request $request,
        SumupService $sumupService,
        MailService $mailService,
        LoggerInterface $logger,
        PaymentCheckoutRepository $checkoutRepository,
        DonationRepository $donationRepository,
        CartageRegistrationRepository $cartageRegistrationRepository,
        MerchRepository $merchRepository,
        TicketRepository $ticketRepository,
        UserRepository $userRepository,
        CartageProfileCreator $cartageProfileCreator,
        DonationPaymentService $donationPaymentService,
        EntityManagerInterface $em
    ): Response {
        // SumUp checkout notifications are untrusted hints, not payment proof.
        // Official checkout webhooks are verified by fetching the checkout from SumUp.
        $data = json_decode($request->getContent(), true);
        if (!is_array($data) || ($data['event_type'] ?? '') !== 'CHECKOUT_STATUS_CHANGED') {
            return new Response('', 204);
        }

        $checkoutId = $data['id'] ?? null;
        if (!is_string($checkoutId) || $checkoutId === '' || strlen($checkoutId) > 128) {
            return new Response('invalid checkout id', 400);
        }

        $checkout = $checkoutRepository->findOneBy(['sumupCheckoutId' => $checkoutId]);
        if (!$checkout) {
            return new Response('unknown checkout', 404);
        }

        try {
            $details = $sumupService->retrieveCheckout($checkoutId);
        } catch (\Throwable $exception) {
            $logger->error('SumUp checkout verification unavailable', ['checkout_id' => $checkoutId, 'exception' => $exception->getMessage()]);
            return new Response('verification unavailable', 503);
        }
        if (isset($details['_error'])) {
            $logger->error('SumUp checkout API rejected verification', ['checkout_id' => $checkoutId, 'status' => $details['_status'] ?? null]);
            return new Response('verification unavailable', 503);
        }
        if ((string) ($details['id'] ?? '') !== $checkoutId
            || (string) ($details['checkout_reference'] ?? '') !== (string) $checkout->getCheckoutReference()
            || !$sumupService->matchesMerchantCode((string) ($details['merchant_code'] ?? ''))
            || strtoupper((string) ($details['currency'] ?? '')) !== strtoupper($checkout->getCurrency())
            || !isset($details['amount'])
            || !is_numeric($details['amount'])
            || abs((float) $details['amount'] - $checkout->getAmount()) > 0.009) {
            $logger->warning('SumUp webhook checkout identity mismatch', ['checkout_id' => $checkoutId]);
            return new Response('checkout verification failed', 422);
        }
        $status = strtoupper((string) ($details['status'] ?? ''));

        $logger->info('SumUp webhook received.', [
            'checkout_id' => $checkoutId,
            'status' => $status,
        ]);

        // Serialize deliveries for the same checkout. Refresh after taking the row lock:
        // a concurrent worker may have completed while this request waited.
        return $em->wrapInTransaction(function () use (
            $em, $checkout, $donationRepository, $donationPaymentService,
            $cartageRegistrationRepository, $cartageProfileCreator, $merchRepository,
            $userRepository, $ticketRepository, $mailService, $logger, $details, $status
        ): Response {
            $em->lock($checkout, LockMode::PESSIMISTIC_WRITE);
            $em->refresh($checkout);

            if ($checkout->getType() !== 'donation' && $checkout->getProcessedAt() !== null) {
                return new Response('ok', 200);
            }

            if ($checkout->getType() === 'donation') {
            $donation = $donationRepository->findOneBy(['paymentCheckout' => $checkout]);
            if (!$donation instanceof Donation) {
                return new Response('ok', 200);
            }

            $wasProcessed = $checkout->getProcessedAt() !== null;
            $result = $donationPaymentService->synchronizeFromGateway($donation, $checkout, $details);
            if (!$wasProcessed && ($result['confirmed'] ?? false) === true) {
                $donationPaymentService->sendConfirmationMail($donation, $checkout);
            }

            return new Response('ok', 200);
        }

        if ($status === 'PAID') {
            if ($checkout->getProcessedAt()) {
                return new Response('ok', 200);
            }

            $checkout->setStatus('paid');
            $checkout->setPaidAt(new \DateTime());

            if ($checkout->getType() === 'cartage') {
                $reference = (string) $checkout->getCheckoutReference();
                $registration = $reference !== ''
                    ? $cartageRegistrationRepository->findOneBy(['checkoutReference' => $reference])
                    : null;

                if (!$registration instanceof CartageRegistration) {
                    foreach ($checkout->getCart() as $line) {
                        $registrationId = (int) ($line['cartage_registration_id'] ?? 0);
                        if ($registrationId > 0) {
                            $registration = $cartageRegistrationRepository->find($registrationId);
                            break;
                        }
                    }
                }

                if ($registration instanceof CartageRegistration) {
                    $registration->markPaidOnline();
                    $user = $cartageProfileCreator->createProfileIfNeeded($registration);
                    if ($user !== null && $checkout->getUser() === null) {
                        $checkout->setUser($user);
                    }
                }

                $checkout->setProcessedAt(new \DateTime());
                $em->flush();

                return new Response('ok', 200);
            }

            $cart = $checkout->getCart();
            $ticketLines = [];
            $merchLines = [];

            foreach ($cart as $line) {
                $lineType = (string) ($line['type'] ?? $checkout->getType());

                if ($lineType === 'merch') {
                    $merch = $merchRepository->find($line['merch_id'] ?? 0);
                    if (!$merch) {
                        continue;
                    }
                    $beneficiaryUser = $checkout->getUser();
                    $beneficiaryUserId = (int) ($line['beneficiary_user_id'] ?? 0);
                    if ($beneficiaryUserId > 0) {
                        $beneficiaryUser = $userRepository->find($beneficiaryUserId) ?? $beneficiaryUser;
                    }
                    $size = (string) ($line['size'] ?? 'TU');
                    $qty = (int) ($line['quantity'] ?? 1);
                    $stock = $merch->getStockForSize($size);
                    if ($stock) {
                        $stock->setQuantity(max(0, $stock->getQuantity() - $qty));
                        $em->persist($stock);
                    }

                    $order = new MerchOrder();
                    $order->setUser($beneficiaryUser);
                    $order->setMerch($merch);
                    $order->setEmail((string) ($line['beneficiary_email'] ?? '') ?: $checkout->getEmail());
                    $order->setCustomerFirstName((string) ($line['beneficiary_first_name'] ?? '') ?: $checkout->getCustomerFirstName());
                    $order->setCustomerLastName((string) ($line['beneficiary_last_name'] ?? '') ?: $checkout->getCustomerLastName());
                    $order->setSize($size);
                    $order->setQuantity($qty);
                    $order->setUnitPrice((float) ($line['unit_price'] ?? 0));
                    $order->setTotalPrice((float) ($line['total'] ?? 0));
                    $order->setPaymentMethod('sumup');
                    $order->setCreatedAt(new \DateTime());
                    $em->persist($order);
                    $merchLines[] = [
                        'title' => (string) ($line['title'] ?? $merch->getTitle()),
                        'size' => $size,
                        'quantity' => $qty,
                        'unitPrice' => (float) ($line['unit_price'] ?? 0),
                        'totalPrice' => (float) ($line['total'] ?? 0),
                    ];
                    continue;
                }

                $ticket = $ticketRepository->find($line['ticket_id'] ?? 0);
                if (!$ticket) {
                    continue;
                }
                $qty = (int) ($line['quantity'] ?? 1);
                $ticket->setStock(max(0, $ticket->getStock() - $qty));
                $em->persist($ticket);

                $order = new TicketOrder();
                $order->setUser($checkout->getUser());
                $order->setTicket($ticket);
                $order->setEmail($checkout->getEmail());
                $order->setCustomerFirstName($checkout->getCustomerFirstName());
                $order->setCustomerLastName($checkout->getCustomerLastName());
                $order->setQuantity($qty);
                $order->setUnitPrice((float) ($line['unit_price'] ?? 0));
                $order->setTotalPrice((float) ($line['total'] ?? 0));
                $order->setPaymentMethod('sumup');
                $order->setPaid(true);
                $order->setCreatedAt(new \DateTime());
                $em->persist($order);
                $ticketLines[] = [
                    'title' => (string) ($line['title'] ?? $ticket->getTitle()),
                    'opponent' => (string) ($line['opponent'] ?? $ticket->getOpponent()),
                    'matchDate' => $ticket->getMatchDate(),
                    'quantity' => $qty,
                    'unitPrice' => (float) ($line['unit_price'] ?? 0),
                    'totalPrice' => (float) ($line['total'] ?? 0),
                ];
            }

            $checkout->setProcessedAt(new \DateTime());
            $em->flush();

            $to = $checkout->getEmail();
            if (!$to && $checkout->getUser()) {
                $to = $checkout->getUser()->getUserIdentifier();
            }
            if ($to) {
                try {
                    $mailService->send(
                        to: $to,
                        subject: 'Recapitulatif de ta commande',
                        template: 'email/checkout_recap.html.twig',
                        context: [
                            'checkout' => $checkout,
                            'ticketLines' => $ticketLines,
                            'merchLines' => $merchLines,
                        ]
                    );
                    $checkout->setEmailSentAt(new \DateTime());
                    $em->flush();
                } catch (\Throwable $e) {
                    $logger->error('Mail checkout recap failed', [
                        'error' => $e->getMessage(),
                        'checkout' => $checkout->getCheckoutReference(),
                    ]);
                }
            }
        } elseif (in_array($status, ['FAILED', 'EXPIRED'], true)) {
            $checkout->setStatus('failed');
            $em->flush();
        }

        return new Response('ok', 200);
        });
    }
}
