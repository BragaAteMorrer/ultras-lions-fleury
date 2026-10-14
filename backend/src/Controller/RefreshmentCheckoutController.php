<?php

namespace App\Controller;

use App\Entity\PaymentCheckout;
use App\Entity\RefreshmentProduct;
use App\Entity\Ticket;
use App\Entity\User;
use App\Service\SumupService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RefreshmentCheckoutController extends AbstractController
{
    #[Route('/buvette/match/{id<\\d+>}/commander', name: 'public_refreshment_checkout', methods: ['POST'])]
    public function checkout(Ticket $match, Request $request, EntityManagerInterface $em, SumupService $sumup): Response
    {
        if (!$this->isCsrfTokenValid('refreshment_checkout_'.$match->getId(), (string) $request->request->get('_token', ''))) {
            throw $this->createAccessDeniedException('Session de commande invalide.');
        }
        $date = $match->getMatchDate();
        if (!$date || new \DateTimeImmutable() >= \DateTimeImmutable::createFromInterface($date)->modify('-24 hours')) {
            $this->addFlash('danger', 'Les précommandes sont closes 24 heures avant le match.');
            return $this->redirectToRoute('public_match_refreshments', ['id' => $match->getId()]);
        }

        $email = $this->getUser() instanceof User ? $this->getUser()->getUserIdentifier() : trim((string) $request->request->get('email', ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('danger', 'Adresse email valide obligatoire.');
            return $this->redirectToRoute('public_match_refreshments', ['id' => $match->getId()]);
        }

        $requested = $request->request->all('quantities');
        $lines = [];
        $totalCents = 0;
        foreach ($requested as $id => $rawQuantity) {
            if (!ctype_digit((string) $id) || !ctype_digit((string) $rawQuantity)) {
                throw $this->createNotFoundException('Produit ou quantité invalide.');
            }
            $quantity = (int) $rawQuantity;
            if ($quantity === 0) { continue; }
            if ($quantity > 30) {
                $this->addFlash('danger', 'Maximum 30 unités par produit.');
                return $this->redirectToRoute('public_match_refreshments', ['id' => $match->getId()]);
            }
            $product = $em->getRepository(RefreshmentProduct::class)->find((int) $id);
            if (!$product || !$product->isActive() || !$product->getCategory()?->isActive() || $product->getPriceCents() <= 0) {
                throw $this->createNotFoundException('Produit indisponible.');
            }
            $subtotal = $quantity * $product->getPriceCents();
            $totalCents += $subtotal;
            $lines[] = [
                'type' => 'refreshment', 'ticket_id' => $match->getId(),
                'refreshment_product_id' => $product->getId(),
                'title' => $product->getName(), 'quantity' => $quantity,
                'unit_price' => $product->getPriceCents() / 100, 'total' => $subtotal / 100,
                'unit_price_cents' => $product->getPriceCents(),
            ];
        }
        if (!$lines || $totalCents <= 0 || $totalCents > 100000) {
            $this->addFlash('danger', 'Choisis au moins une boisson (maximum 1 000 €).');
            return $this->redirectToRoute('public_match_refreshments', ['id' => $match->getId()]);
        }

        $reference = strtoupper(bin2hex(random_bytes(12)));
        $checkout = (new PaymentCheckout())->setType('refreshment')->setStatus('pending')
            ->setCheckoutReference($reference)->setAmount($totalCents / 100)->setCurrency('EUR')
            ->setUser($this->getUser() instanceof User ? $this->getUser() : null)
            ->setEmail($email)->setCart($lines)->setCreatedAt(new \DateTime());
        $em->persist($checkout);
        $em->flush();

        $response = $sumup->createHostedCheckout($totalCents / 100, 'EUR', $reference,
            'Buvette - '.$match->getMatchLabel(),
            $this->generateUrl('checkout_thankyou', ['ref' => $reference], UrlGeneratorInterface::ABSOLUTE_URL));
        if (!empty($response['_error']) || empty($response['id']) || empty($response['hosted_checkout_url'])) {
            $checkout->setStatus('failed');
            $em->flush();
            $this->addFlash('danger', 'Impossible de créer le paiement.');
            return $this->redirectToRoute('public_match_refreshments', ['id' => $match->getId()]);
        }
        $checkout->setSumupCheckoutId((string) $response['id']);
        $em->flush();
        $references = $request->getSession()->get('checkout_receipt_references', []);
        $references = is_array($references) ? $references : [];
        $references[] = $reference;
        $request->getSession()->set('checkout_receipt_references', array_slice(array_unique($references), -20));
        return $this->redirect((string) $response['hosted_checkout_url']);
    }
}
