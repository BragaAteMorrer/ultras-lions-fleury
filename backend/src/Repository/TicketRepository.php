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
        $now = new \DateTimeImmutable();
        $availabilityField = $isMember ? 't.memberAvailabilityDate' : 't.guestAvailabilityDate';

        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.category', 'cat')
            ->addSelect('cat')
            ->andWhere('t.matchDate > :now')
            ->andWhere(sprintf('(%s IS NULL OR %s <= :now)', $availabilityField, $availabilityField))
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
        return $ticket->isAvailableForUser($isMember);
    }

}
