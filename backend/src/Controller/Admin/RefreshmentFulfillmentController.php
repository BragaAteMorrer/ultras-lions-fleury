<?php

namespace App\Controller\Admin;

use App\Entity\RefreshmentOrder;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/buvette/preparation')]
final class RefreshmentFulfillmentController extends AbstractController
{
    #[Route('', name: 'admin_refreshment_fulfillment', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $orders = $em->getRepository(RefreshmentOrder::class)
            ->findBy(['status' => 'confirmed', 'collectedAt' => null], ['createdAt' => 'ASC'], 200);
        return $this->render('admin/refreshment_fulfillment.html.twig', ['orders' => $orders]);
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
