<?php

namespace App\Controller\Admin;

use App\Entity\PaymentCheckout;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/payment-reviews')]
final class PaymentReviewController extends AbstractController
{
    #[Route('', name: 'admin_payment_reviews', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $pending = $em->getRepository(PaymentCheckout::class)->findBy(
            ['status' => 'paid_stock_review', 'reviewResolvedAt' => null],
            ['createdAt' => 'ASC'],
            100
        );

        return $this->render('admin/payment_reviews.html.twig', ['pending' => $pending]);
    }

    #[Route('/{id<\\d+>}/resolve', name: 'admin_payment_review_resolve', methods: ['POST'])]
    public function resolve(int $id, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('payment-review-'.$id, (string) $request->request->get('_token', ''))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $resolution = (string) $request->request->get('resolution', '');
        $reference = trim((string) $request->request->get('reference', ''));
        if (!in_array($resolution, ['refunded', 'fulfilled'], true) || mb_strlen($reference) < 5 || mb_strlen($reference) > 180) {
            $this->addFlash('danger', 'Action ou preuve de régularisation invalide.');
            return $this->redirectToRoute('admin_payment_reviews');
        }

        try {
            $em->wrapInTransaction(function () use ($em, $id, $resolution, $reference): void {
                $checkout = $em->find(PaymentCheckout::class, $id, LockMode::PESSIMISTIC_WRITE);
                if (!$checkout || $checkout->getStatus() !== 'paid_stock_review') {
                    throw $this->createNotFoundException('Paiement non concerné.');
                }
                $checkout->recordManualResolution($resolution, $reference);
                $em->flush();
            });
            $this->addFlash('success', 'Preuve enregistrée. Aucun remboursement SumUp n’a été déclenché.');
        } catch (\LogicException $exception) {
            $this->addFlash('danger', 'Ce paiement a déjà été traité.');
        }

        return $this->redirectToRoute('admin_payment_reviews');
    }
}
