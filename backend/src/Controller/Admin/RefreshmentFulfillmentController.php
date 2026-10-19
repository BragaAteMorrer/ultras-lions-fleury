<?php

namespace App\Controller\Admin;

use App\Entity\RefreshmentOrder;
use App\Entity\Ticket;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_BUVETTE')]
#[Route('/admin/buvette/preparation')]
final class RefreshmentFulfillmentController extends AbstractController
{
    #[Route('', name: 'admin_refreshment_fulfillment', methods: ['GET'])]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $matchId = max(0, $request->query->getInt('match'));
        $matches = $em->getRepository(Ticket::class)->findBy([], ['matchDate' => 'DESC'], 50);
        $criteria = ['status' => 'confirmed'];
        if ($matchId > 0) {
            $match = $em->getRepository(Ticket::class)->find($matchId);
            if (!$match) { throw $this->createNotFoundException('Match introuvable.'); }
            $criteria['ticket'] = $match;
        }
        $orders = $em->getRepository(RefreshmentOrder::class)
            ->findBy($criteria, ['createdAt' => 'ASC'], 500);
        $summary = [];
        foreach ($orders as $order) {
            $match = $order->getTicket();
            $id = $match?->getId() ?? 0;
            $summary[$id] ??= ['label' => $match?->getMatchLabel() ?? 'Match inconnu', 'total' => 0, 'prepared' => 0, 'collected' => 0, 'products' => []];
            $quantity = $order->getQuantity();
            $summary[$id]['total'] += $quantity;
            if ($order->getPreparedAt() !== null) { $summary[$id]['prepared'] += $quantity; }
            if ($order->getCollectedAt() !== null) { $summary[$id]['collected'] += $quantity; }
            $product = $order->getProduct();
            $productId = $product?->getId() ?? 0;
            $summary[$id]['products'][$productId] ??= ['name' => $product?->getName() ?? 'Produit supprimé', 'quantity' => 0];
            $summary[$id]['products'][$productId]['quantity'] += $quantity;
        }
        $groups = [];
        foreach ($orders as $order) {
            if ($order->getCollectedAt() !== null) { continue; }
            $checkoutId = $order->getPaymentCheckout()?->getId();
            // Do not merge independent cash orders sharing an email address.
            $groupKey = $checkoutId !== null ? 'checkout-'.$checkoutId : 'line-'.$order->getId();
            $groups[$groupKey] ??= [
                'reference' => $order->getPaymentCheckout()?->getCheckoutReference() ?: '#'.$order->getId(),
                'email' => $order->getCustomerEmail(),
                'match' => $order->getTicket()?->getMatchLabel() ?? 'Match inconnu',
                'lines' => [],
            ];
            $groups[$groupKey]['lines'][] = $order;
        }
        return $this->render('admin/refreshment_fulfillment.html.twig', [
            'orders' => $orders, 'groups' => $groups, 'matches' => $matches, 'selectedMatch' => $matchId, 'summary' => $summary,
        ]);
    }

    #[Route('/{id<\\d+>}/{action}', name: 'admin_refreshment_fulfillment_action', methods: ['POST'], requirements: ['action' => 'prepare|collect'])]
    public function transition(int $id, string $action, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('buvette-'.$id.'-'.$action, (string) $request->request->get('_token', ''))) {
            throw $this->createAccessDeniedException('CSRF invalide.');
        }
        try {
            $em->wrapInTransaction(function () use ($em, $id, $action): void {
                $order = $em->find(RefreshmentOrder::class, $id, LockMode::PESSIMISTIC_WRITE);
                if (!$order) { throw $this->createNotFoundException('Commande introuvable.'); }
                if ($action === 'prepare') { $order->markPrepared(); }
                else { $order->markCollected(); }
                $em->flush();
            });
            $this->addFlash('success', 'Commande mise à jour.');
        } catch (\LogicException $e) {
            $this->addFlash('danger', 'Transition impossible : vérifier le paiement et l’état de la commande.');
        }
        return $this->redirectToRoute('admin_refreshment_fulfillment');
    }
}
