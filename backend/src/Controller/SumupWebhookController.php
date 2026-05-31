<?php

namespace App\Controller;

use App\Entity\MerchOrder;
use App\Entity\TicketOrder;
use App\Repository\MerchRepository;
use App\Repository\TicketRepository;
use App\Repository\PaymentCheckoutRepository;
use App\Service\MailService;
use App\Service\SumupService;
use Doctrine\ORM\EntityManagerInterface;
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
        MerchRepository $merchRepository,
        TicketRepository $ticketRepository,
        EntityManagerInterface $em
    ): Response {
        $payload = $request->getContent();
        $signature = $request->headers->get('x-payload-signature');

        if (!$sumupService->verifyWebhookSignature($payload, $signature)) {
            return new Response('invalid signature', 401);
        }

        $data = json_decode($payload, true) ?: [];
        $checkoutId = $data['checkout_id'] ?? $data['id'] ?? null;

        if (!$checkoutId) {
            return new Response('missing checkout_id', 400);
        }

        $checkout = $checkoutRepository->findOneBy(['sumupCheckoutId' => $checkoutId]);
        if (!$checkout) {
            return new Response('unknown checkout', 404);
        }

        $details = $sumupService->retrieveCheckout($checkoutId);
        $status = strtoupper((string) ($details['status'] ?? ''));

        if ($status === 'PAID' || $status === 'SUCCESSFUL' || $status === 'COMPLETED') {
            if ($checkout->getProcessedAt()) {
                return new Response('ok', 200);
            }

            $checkout->setStatus('paid');
            $checkout->setPaidAt(new \DateTime());

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
                    $size = (string) ($line['size'] ?? 'TU');
                    $qty = (int) ($line['quantity'] ?? 1);
                    $stock = $merch->getStockForSize($size);
                    if ($stock) {
                        $stock->setQuantity(max(0, $stock->getQuantity() - $qty));
                        $em->persist($stock);
                    }

                    $order = new MerchOrder();
                    $order->setUser($checkout->getUser());
                    $order->setMerch($merch);
                    $order->setEmail($checkout->getEmail());
                    $order->setCustomerFirstName($checkout->getCustomerFirstName());
                    $order->setCustomerLastName($checkout->getCustomerLastName());
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
        } elseif ($status === 'FAILED' || $status === 'CANCELLED' || $status === 'EXPIRED') {
            $checkout->setStatus('failed');
            $em->flush();
        }

        return new Response('ok', 200);
    }
}
