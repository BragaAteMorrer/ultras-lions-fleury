<?php

namespace App\Repository;

use App\Entity\Ticket;
use App\Entity\TicketCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ticket>
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    /**
     * @return Ticket[]
     */
    public function findPublicTickets(?TicketCategory $category, bool $isMember): array
    {
        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.category', 'cat')
            ->addSelect('cat')
            ->orderBy('t.matchDate', 'ASC');

        if ($category) {
            $qb->andWhere('t.category = :category')
                ->setParameter('category', $category);
        }

        if (!$isMember) {
            $now = new \DateTime();
            $j10 = (clone $now)->modify('+10 days');

            $qb->andWhere(
                '(t.matchLocation IS NULL) OR ' .
                '(t.matchLocation = :domicile) OR ' .
                '(t.matchLocation = :exterieur AND t.matchDate <= :j10)'
            )
                ->setParameter('domicile', 'domicile')
                ->setParameter('exterieur', 'exterieur')
                ->setParameter('j10', $j10);
        }

        return $qb->getQuery()->getResult();
    }

    public function isVisibleForUser(Ticket $ticket, bool $isMember): bool
    {
        if ($isMember) {
            return true;
        }

        $location = $ticket->getMatchLocation();
        if ($location === null || $location === 'domicile') {
            return true;
        }

        if ($location !== 'exterieur') {
            return true;
        }

        $matchDate = $ticket->getMatchDate();
        if (!$matchDate) {
            return false;
        }

        $now = new \DateTime();
        $j10 = (clone $now)->modify('+10 days');

        return $matchDate <= $j10;
    }
}
