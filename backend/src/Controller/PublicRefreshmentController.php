<?php

namespace App\Controller;

use App\Entity\PaymentCheckout;
use App\Entity\RefreshmentProduct;
use App\Entity\RefreshmentReport;
use App\Entity\Ticket;
use App\Service\SumupService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/buvette')]
final class PublicRefreshmentController extends AbstractController
{
    #[Route('/match/{id<\d+>}', name: 'public_refreshments', methods: ['GET'])]
    public function catalog(Ticket $ticket, EntityManagerInterface $em): Response
    {
        $report = $em->getRepository(RefreshmentReport::class)->findOneBy(['ticket' => $ticket]);
        $closes = $report?->getClosesAt();
        $open = $closes !== null && new \DateTimeImmutable() < $closes
            && $ticket->getMatchDate() > new \DateTimeImmutable();
        $products = $em->getRepository(RefreshmentProduct::class)->findBy(['active' => true]);
        $products = array_values(array_filter($products, static fn (RefreshmentProduct $p): bool => $p->getCategory()?->isActive() === true));
        return $this->render('refreshment/public.html.twig', [
            'ticket' => $ticket, 'products' => $products, 'open' => $open, 'closesAt' => $closes,
        ]);
    }

    #[Route('/match/{id<\d+>}/commander', name: 'public_refreshments_buy', methods: ['POST'])]
    public function buy(Ticket $ticket, Request $request, EntityManagerInterface $em, SumupService $sumup): Response
    {
        if (!$this->isCsrfTokenValid('refreshment_'.$ticket->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Formulaire invalide.');
        }
        $report = $em->getRepository(RefreshmentReport::class)->findOneBy(['ticket' => $ticket]);
        $now = new \DateTimeImmutable();
        if (!$report?->getClosesAt() || $now >= $report->getClosesAt() || $ticket->getMatchDate() <= $now) {
            $this->addFlash('danger', 'Les precommandes de ce match sont fermees.');
            return $this->redirectToRoute('public_refreshments', ['id' => $ticket->getId()]);
        }
        $email = trim((string) $request->request->get('email', ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('danger', 'Indique une adresse email valide.');
            return $this->redirectToRoute('public_refreshments', ['id' => $ticket->getId()]);
        }
        $quantities = $request->request->all('quantities');
        $lines = [];
        $totalCents = 0;
        foreach ($quantities as $id => $raw) {
            if (!ctype_digit((string) $id) || !ctype_digit((string) $raw)) {
                throw $this->createNotFoundException('Quantite invalide.');
            }
            $qty = (int) $raw;
            if ($qty === 0) { continue; }
            if ($qty > 50) { throw $this->createNotFoundException('Quantite trop elevee.'); }
            $product = $em->getRepository(RefreshmentProduct::class)->find((int) $id);
            if (!$product || !$product->isActive() || !$product->getCategory()?->isActive() || $product->getPriceCents() <= 0) {
                throw $this->createNotFoundException('Produit indisponible.');
            }
            $unit = $product->getPriceCents();
            $totalCents += $unit * $qty;
            $lines[] = [
                'type' => 'refreshment', 'ticket_id' => $ticket->getId(), 'product_id' => $product->getId(),
                'title' => $product->getName(), 'quantity' => $qty, 'unit_price_cents' => $unit,
                'unit_price' => $unit / 100, 'total' => $unit * $qty / 100,
            ];
        }
        if (!$lines || $totalCents < 1) {
            $this->addFlash('danger', 'Selectionne au moins un produit.');
            return $this->redirectToRoute('public_refreshments', ['id' => $ticket->getId()]);
        }
        $reference = strtoupper(bin2hex(random_bytes(8)));
        $checkout = new PaymentCheckout();
        $checkout->setType('refreshment')->setStatus('pending')->setCheckoutReference($reference)
            ->setAmount($totalCents / 100)->setCurrency('EUR')->setEmail($email)
            ->setCart($lines)->setCreatedAt(new \DateTime());
        $em->persist($checkout);
        $em->flush();
        $response = $sumup->createHostedCheckout(
            $totalCents / 100, 'EUR', $reference,
            'Buvette - '.$ticket->getMatchLabel(),
            $this->generateUrl('checkout_thankyou', [], UrlGeneratorInterface::ABSOLUTE_URL)
        );
        if (!empty($response['_error']) || empty($response['id']) || empty($response['hosted_checkout_url'])) {
            $checkout->setStatus('failed');
            $em->flush();
            $this->addFlash('danger', 'Le paiement est momentanement indisponible.');
            return $this->redirectToRoute('public_refreshments', ['id' => $ticket->getId()]);
        }
        $checkout->setSumupCheckoutId((string) $response['id']);
        $em->flush();
        return $this->redirect((string) $response['hosted_checkout_url']);
    }
}
