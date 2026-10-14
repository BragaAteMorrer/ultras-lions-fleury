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
    #[Route('/commande/merci', name: 'checkout_thankyou')]
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
        $sumupStatus = '';
        $paymentStatusLabel = 'Paiement en attente';
        $paymentStatusMessage = 'Nous verifions ton paiement avec SumUp.';

        if ($ref !== '') {
            $checkout = $checkoutRepository->findOneBy(['checkoutReference' => $ref]);
            if ($checkout && !$this->canReadReceipt($checkout, $request)) {
                $checkout = null;
            }
            if ($checkout) {
                if ($checkout->getStatus() !== 'paid' && $checkout->getSumupCheckoutId()) {
                    $details = $sumupService->retrieveCheckout($checkout->getSumupCheckoutId());
                    $sumupStatus = strtoupper((string) ($details['status'] ?? ''));
                    // Payment settlement belongs to the validated SumUp webhook only.
                    // Never grant paid status from a browser-accessible return URL.
                    if (in_array($sumupStatus, ['FAILED', 'EXPIRED'], true)) {
                        $checkout->setStatus('failed');
                        $em->flush();
                    }
                }

                [$paymentStatusLabel, $paymentStatusMessage] = $this->getPaymentStatusView($checkout->getStatus(), $sumupStatus);

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

        if (!$checkout) {
            $paymentStatusLabel = 'Commande introuvable';
            $paymentStatusMessage = 'Impossible de retrouver cette commande. Contacte le groupe avec ta preuve de paiement.';
        }

        return $this->render('checkout/thankyou.html.twig', [
            'checkout' => $checkout,
            'ticketItems' => $ticketItems,
            'merchItems' => $merchItems,
            'paymentStatusLabel' => $paymentStatusLabel,
            'paymentStatusMessage' => $paymentStatusMessage,
            'paymentStatus' => $sumupStatus !== '' ? $sumupStatus : ($checkout?->getStatus() ?? 'UNKNOWN'),
        ]);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function getPaymentStatusView(string $checkoutStatus, string $sumupStatus): array
    {
        if ($checkoutStatus === 'paid') {
            return ['Paiement confirme', 'Ton paiement a bien ete pris en compte.'];
        }

        if ($checkoutStatus === 'failed' || in_array($sumupStatus, ['FAILED', 'DECLINED', 'CANCELLED', 'CANCELED', 'EXPIRED'], true)) {
            return ['Paiement refuse', 'SumUp a refuse ou expire le paiement. Tu peux reessayer ou contacter le groupe si le montant a ete debite.'];
        }

        if (in_array($sumupStatus, ['PENDING', 'WAITING'], true) || $checkoutStatus === 'pending') {
            return ['Paiement en attente', 'Nous verifions ton paiement avec SumUp. La commande sera confirmee automatiquement apres validation.'];
        }

        if ($sumupStatus !== '') {
            return ['Statut SumUp: '.$sumupStatus, 'Le retour SumUp a ete recu, mais le statut exact est inhabituel.'];
        }

        return ['Paiement non verifie', 'Aucun statut SumUp definitif n a encore ete recu pour cette commande.'];
    }

    private function canReadReceipt(\App\Entity\PaymentCheckout $checkout, Request $request): bool
    {
        $user = $this->getUser();
        if ($user !== null && ($this->isGranted('ROLE_ADMIN')
            || ($checkout->getUser() !== null && $checkout->getUser()->getId() === $user->getId()))) {
            return true;
        }

        // A checkout reference by itself is not an authorization token.
        // Guest access requires a checkout reference saved in the current session.
        $session = $request->getSession();
        return $session->has('checkout_receipt_reference')
            && hash_equals((string) $session->get('checkout_receipt_reference'), (string) $checkout->getCheckoutReference());
    }

    #[Route('/merci/pdf', name: 'checkout_receipt_pdf')]
    public function receiptPdf(Request $request, PaymentCheckoutRepository $checkoutRepository): Response
    {
        $ref = (string) $request->query->get('ref', '');
        $checkout = $ref !== '' ? $checkoutRepository->findOneBy(['checkoutReference' => $ref]) : null;

        if (!$checkout || !$this->canReadReceipt($checkout, $request)) {
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
        $logoPath = realpath($projectDir . '/assets/img/logo/Logo Final - Ultras Lions - Autre Présentation.png');
        $watermarkPath = realpath($projectDir . '/assets/img/logo/Idée 2a-monochrome.png');

        $html = $this->renderView('checkout/receipt.html.twig', [
            'checkout' => $checkout,
            'ticketItems' => $ticketItems,
            'merchItems' => $merchItems,
            'logoPath' => $logoPath ? 'file://' . $logoPath : null,
            'watermarkPath' => $watermarkPath ? 'file://' . $watermarkPath : null,
        ]);

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('chroot', $projectDir);

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

        if (!$checkout || !$this->canReadReceipt($checkout, $request)) {
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
