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
    public function findPublicTickets(?TicketCategory $category): array
    {
        $now = new \DateTimeImmutable();

        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.category', 'cat')
            ->leftJoin('t.image', 'image')
            ->addSelect('cat')
            ->addSelect('image')
            ->andWhere('t.matchDate > :now')
            ->setParameter('now', $now)
            ->orderBy('t.matchDate', 'ASC');

        if ($category) {
            $qb->andWhere('t.category = :category')
                ->setParameter('category', $category);
        }

        return $qb->getQuery()->getResult();
    }

    public function isVisibleForUser(Ticket $ticket, bool $isMember): bool
    {
        if (!$ticket->isAvailableForUser($isMember)) { return false; }

        $report = $this->getEntityManager()->getRepository(\App\Entity\RefreshmentReport::class)->findOneBy(['ticket' => $ticket]);
        $closesAt = $report?->getClosesAt();
        return $closesAt === null || new \DateTimeImmutable() < $closesAt;
    }

}
