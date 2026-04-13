<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\PaymentCheckoutRepository;
use App\Service\CartService;
use App\Service\MailService;
use App\Service\SumupService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class CheckoutController extends AbstractController
{
    #[Route('/merci', name: 'checkout_thankyou')]
    public function thankYou(
        Request $request,
        PaymentCheckoutRepository $checkoutRepository,
        SumupService $sumupService,
        EntityManagerInterface $em,
        SessionInterface $session,
        CartService $cartService,
        MailService $mailService,
        LoggerInterface $logger
    ): Response
    {
        $ref = (string) $request->query->get('ref', '');
        $checkout = null;
        $items = [];
        $ticketItems = [];
        $merchItems = [];

        if ($ref !== '') {
            $checkout = $checkoutRepository->findOneBy(['checkoutReference' => $ref]);
            if ($checkout) {
                if ($checkout->getStatus() !== 'paid' && $checkout->getSumupCheckoutId()) {
                    $details = $sumupService->retrieveCheckout($checkout->getSumupCheckoutId());
                    $status = strtoupper((string) ($details['status'] ?? ''));
                    if ($status === 'PAID' || $status === 'SUCCESSFUL' || $status === 'COMPLETED') {
                        $checkout->setStatus('paid');
                        $checkout->setPaidAt(new \DateTime());
                        $em->flush();
                    }
                }

                if ($checkout->getStatus() === 'paid') {
                    $cartService->clearMerch($session);
                    $cartService->clearTicket($session);
                    $session->save();
                }

                $lines = $checkout->getCart();
                foreach ($lines as $row) {
                    $lineType = (string) ($row['type'] ?? $checkout->getType());
                    if ($lineType === 'ticket') {
                        $ticketItems[] = [
                            'title' => $row['title'] ?? 'Match',
                            'opponent' => $row['opponent'] ?? '',
                            'match_date' => $row['match_date'] ?? null,
                            'quantity' => (int) ($row['quantity'] ?? 1),
                            'unit_price' => (float) ($row['unit_price'] ?? 0),
                            'total' => (float) ($row['total'] ?? 0),
                            'totalPrice' => (float) ($row['total'] ?? 0),
                        ];
                    } else {
                        $merchItems[] = [
                            'title' => $row['title'] ?? 'Produit',
                            'size' => $row['size'] ?? 'TU',
                            'quantity' => (int) ($row['quantity'] ?? 1),
                            'unit_price' => (float) ($row['unit_price'] ?? 0),
                            'total' => (float) ($row['total'] ?? 0),
                            'totalPrice' => (float) ($row['total'] ?? 0),
                        ];
                    }
                }
                $items = array_merge($ticketItems, $merchItems);

                if ($checkout->getStatus() === 'paid' && $checkout->getEmailSentAt() === null) {
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
                                    'ticketLines' => $ticketItems,
                                    'merchLines' => $merchItems,
                                ]
                            );
                            $checkout->setEmailSentAt(new \DateTime());
                            $em->flush();
                        } catch (\Throwable $e) {
                            $logger->error('Mail checkout recap failed (thankyou)', [
                                'error' => $e->getMessage(),
                                'checkout' => $checkout->getCheckoutReference(),
                            ]);
                        }
                    }
                }
            }
        }

        return $this->render('checkout/thankyou.html.twig', [
            'checkout' => $checkout,
            'ticketItems' => $ticketItems,
            'merchItems' => $merchItems,
        ]);
    }

    #[Route('/merci/pdf', name: 'checkout_receipt_pdf')]
    public function receiptPdf(Request $request, PaymentCheckoutRepository $checkoutRepository): Response
    {
        $ref = (string) $request->query->get('ref', '');
        $checkout = $ref !== '' ? $checkoutRepository->findOneBy(['checkoutReference' => $ref]) : null;

        if (!$checkout) {
            throw $this->createNotFoundException();
        }

        $lines = $checkout->getCart();
        $ticketItems = [];
        $merchItems = [];
        foreach ($lines as $row) {
            $lineType = (string) ($row['type'] ?? $checkout->getType());
            if ($lineType === 'ticket') {
                $ticketItems[] = [
                    'title' => $row['title'] ?? 'Match',
                    'opponent' => $row['opponent'] ?? '',
                    'match_date' => $row['match_date'] ?? null,
                    'quantity' => (int) ($row['quantity'] ?? 1),
                    'unit_price' => (float) ($row['unit_price'] ?? 0),
                    'total' => (float) ($row['total'] ?? 0),
                ];
            } else {
                $merchItems[] = [
                    'title' => $row['title'] ?? 'Produit',
                    'size' => $row['size'] ?? 'TU',
                    'quantity' => (int) ($row['quantity'] ?? 1),
                    'unit_price' => (float) ($row['unit_price'] ?? 0),
                    'total' => (float) ($row['total'] ?? 0),
                ];
            }
        }

        if (!class_exists(\Dompdf\Dompdf::class)) {
            throw new \RuntimeException('Installe dompdf/dompdf pour generer le PDF.');
        }

        $projectDir = (string) $this->getParameter('kernel.project_dir');
        $publicDir = $projectDir . '/public';
        $logoPath = realpath($publicDir . '/assets/img/logo/Logo Final - Ultras Lions - Autre Présentation-lIhYrhj.png');
        $watermarkPath = realpath($publicDir . '/assets/img/logo/Idée 2a-monochrome-2tSiFCz.png');

        $html = $this->renderView('checkout/receipt.html.twig', [
            'checkout' => $checkout,
            'ticketItems' => $ticketItems,
            'merchItems' => $merchItems,
            'logoPath' => $logoPath ? 'file://' . $logoPath : null,
            'watermarkPath' => $watermarkPath ? 'file://' . $watermarkPath : null,
        ]);

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('chroot', $publicDir);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename=commande_' . $checkout->getCheckoutReference() . '.pdf',
        ]);
    }

    #[Route('/merci-liquide', name: 'checkout_cash_thankyou')]
    public function cashThankYou(Request $request, PaymentCheckoutRepository $checkoutRepository): Response
    {
        $ref = (string) $request->query->get('ref', '');
        $checkout = $ref !== '' ? $checkoutRepository->findOneBy(['checkoutReference' => $ref]) : null;

        if (!$checkout) {
            throw $this->createNotFoundException();
        }

        $lines = $checkout->getCart();
        $ticketItems = [];
        $merchItems = [];
        foreach ($lines as $row) {
            $lineType = (string) ($row['type'] ?? $checkout->getType());
            if ($lineType === 'ticket') {
                $ticketItems[] = [
                    'title' => $row['title'] ?? 'Match',
                    'opponent' => $row['opponent'] ?? '',
                    'match_date' => $row['match_date'] ?? null,
                    'quantity' => (int) ($row['quantity'] ?? 1),
                    'unit_price' => (float) ($row['unit_price'] ?? 0),
                    'total' => (float) ($row['total'] ?? 0),
                ];
            } else {
                $merchItems[] = [
                    'title' => $row['title'] ?? 'Produit',
                    'size' => $row['size'] ?? 'TU',
                    'quantity' => (int) ($row['quantity'] ?? 1),
                    'unit_price' => (float) ($row['unit_price'] ?? 0),
                    'total' => (float) ($row['total'] ?? 0),
                ];
            }
        }

        return $this->render('checkout/cash_thankyou.html.twig', [
            'checkout' => $checkout,
            'ticketItems' => $ticketItems,
            'merchItems' => $merchItems,
        ]);
    }
}
