<?php

namespace App\Controller;

use App\Entity\RefreshmentProduct;
use App\Entity\Ticket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Public, read-only catalogue. Checkout must not be enabled before SumUp
 * webhook reconciliation and match closing checks are implemented.
 */
final class PublicRefreshmentController extends AbstractController
{
    #[Route('/buvette/match/{id<\\d+>}', name: 'public_match_refreshments', methods: ['GET'])]
    public function index(Ticket $match, EntityManagerInterface $entityManager): Response
    {
        $matchDate = $match->getMatchDate();
        if ($match->isAwayMatch() || !$matchDate || $matchDate <= new \DateTimeImmutable()) {
            throw $this->createNotFoundException();
        }

        $products = $entityManager->getRepository(RefreshmentProduct::class)
            ->findBy(['active' => true], ['name' => 'ASC']);

        return $this->render('refreshments/public_catalog.html.twig', [
            'match' => $match,
            'products' => $products,
        ]);
    }
}
