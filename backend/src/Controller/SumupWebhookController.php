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
        if ( (string) ($details['id'] ?? '') !== $checkoutId
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

        // Serialize notifications for this checkout to avoid duplicate order writes.
        $mailPayload = null;
        $response = $em->wrapInTransaction(function () use (
            $em, $checkout, $donationRepository, $donationPaymentService,
            $cartageRegistrationRepository, $cartageProfileCreator,
            $merchRepository, $ticketRepository, $userRepository,
            $mailService, $logger, $details, $status, &$mailPayload
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

            $alreadyProcessed = $checkout->getProcessedAt() !== null;
            $result = $donationPaymentService->synchronizeFromGateway($donation, $checkout, $details);
            if (!$alreadyProcessed && ($result['confirmed'] ?? false) === true) {
                $donationPaymentService->sendConfirmationMail($donation, $checkout);
            }

            return new Response('ok', 200);
        }

        if ($status === 'PAID' || $status === 'SUCCESSFUL' || $status === 'COMPLETED') {
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

            // Lock every stock record in deterministic order, including across
            // separate paid checkouts competing for the same inventory.
            $required = [];
            $resources = [];
            foreach ($cart as $line) {
                $quantity = (int) ($line['quantity'] ?? 1);
                if ($quantity < 1) {
                    $logger->error('Invalid paid checkout quantity', ['checkout' => $checkout->getCheckoutReference()]);
                    $checkout->setStatus('paid_stock_review');
                    $checkout->setProcessedAt(new \DateTime());
                    $em->flush();
                    return new Response('ok', 200);
                }
                $kind = (string) ($line['type'] ?? $checkout->getType());
                if ($kind === 'merch') {
                    $item = $merchRepository->find((int) ($line['merch_id'] ?? 0));
                    $stock = $item?->getStockForSize((string) ($line['size'] ?? 'TU'));
                    $key = $stock?->getId() ? 'M'.$stock->getId() : null;
                    $resource = $stock;
                } else {
                    $resource = $ticketRepository->find((int) ($line['ticket_id'] ?? 0));
                    $key = $resource?->getId() ? 'T'.$resource->getId() : null;
                }
                if ($key === null) {
                    $logger->critical('Paid checkout item missing', ['checkout' => $checkout->getCheckoutReference()]);
                    $checkout->setStatus('paid_stock_review');
                    $checkout->setProcessedAt(new \DateTime());
                    $em->flush();
                    return new Response('ok', 200);
                }
                $required[$key] = ($required[$key] ?? 0) + $quantity;
                $resources[$key] = $resource;
            }
            ksort($resources, SORT_STRING);
            foreach ($resources as $key => $resource) {
                $em->lock($resource, LockMode::PESSIMISTIC_WRITE);
                $em->refresh($resource);
                $available = str_starts_with($key, 'M') ? $resource->getQuantity() : $resource->getStock();
                if ($available < $required[$key]) {
                    $logger->critical('Paid checkout stock shortage: manual reconciliation required', [
                        'checkout' => $checkout->getCheckoutReference(),
                        'stock_key' => $key,
                        'required' => $required[$key],
                        'available' => $available,
                    ]);
                    $checkout->setStatus('paid_stock_review');
                    $checkout->setProcessedAt(new \DateTime());
                    $em->flush();
                    return new Response('ok', 200);
                }
            }

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
                        $stock->setQuantity($stock->getQuantity() - $qty);
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
                $ticket->setStock($ticket->getStock() - $qty);
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
            if ($to && $checkout->getEmailSentAt() === null) {
                $mailPayload = ['to' => $to, 'ticketLines' => $ticketLines, 'merchLines' => $merchLines];
            }
        } elseif ($status === 'FAILED' || $status === 'CANCELLED' || $status === 'EXPIRED') {
            $checkout->setStatus('failed');
            $em->flush();
        }

        return new Response('ok', 200);
        });
        if ($mailPayload !== null) {
            try {
                $mailService->send(
                    to: $mailPayload['to'],
                    subject: 'Recapitulatif de ta commande',
                    template: 'email/checkout_recap.html.twig',
                    context: [
                        'checkout' => $checkout,
                        'ticketLines' => $mailPayload['ticketLines'],
                        'merchLines' => $mailPayload['merchLines'],
                    ]
                );
                $checkout->setEmailSentAt(new \DateTime());
                $em->flush();
            } catch (\Throwable $error) {
                $logger->error('Checkout recap email failed', ['checkout' => $checkout->getCheckoutReference(), 'error' => $error->getMessage()]);
            }
        }
        return $response;
    }
}
