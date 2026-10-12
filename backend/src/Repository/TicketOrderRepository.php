<?php

namespace App\Repository;

use App\Entity\TicketOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TicketOrder>
 */
class TicketOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketOrder::class);
    }

    public function countBetween(\DateTimeInterface $from, \DateTimeInterface $to): int
    {
        return (int) $this->createQueryBuilder('ticketOrder')
            ->select('COUNT(ticketOrder.id)')
            ->andWhere('ticketOrder.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $from, Types::DATETIME_IMMUTABLE)
            ->setParameter('to', $to, Types::DATETIME_IMMUTABLE)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countPaidBetween(\DateTimeInterface $from, \DateTimeInterface $to): int
    {
        return (int) $this->createQueryBuilder('ticketOrder')
            ->select('COUNT(ticketOrder.id)')
            ->andWhere('ticketOrder.createdAt BETWEEN :from AND :to')
            ->andWhere('ticketOrder.paid = true')
            ->setParameter('from', $from, Types::DATETIME_IMMUTABLE)
            ->setParameter('to', $to, Types::DATETIME_IMMUTABLE)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function sumTotalBetween(\DateTimeInterface $from, \DateTimeInterface $to): float
    {
        return (float) $this->createQueryBuilder('ticketOrder')
            ->select('COALESCE(SUM(ticketOrder.totalPrice), 0)')
            ->andWhere('ticketOrder.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $from, Types::DATETIME_IMMUTABLE)
            ->setParameter('to', $to, Types::DATETIME_IMMUTABLE)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function sumQuantityBetween(\DateTimeInterface $from, \DateTimeInterface $to): int
    {
        return (int) $this->createQueryBuilder('ticketOrder')
            ->select('COALESCE(SUM(ticketOrder.quantity), 0)')
            ->andWhere('ticketOrder.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $from, Types::DATETIME_IMMUTABLE)
            ->setParameter('to', $to, Types::DATETIME_IMMUTABLE)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return array<int, array{day: string, total: int, revenue: float}>
     */
    public function countByDay(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $rows = $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT DATE(created_at) AS day, COUNT(*) AS total, COALESCE(SUM(total_price), 0) AS revenue
             FROM ticket_order
             WHERE created_at BETWEEN :from AND :to
             GROUP BY DATE(created_at)
             ORDER BY day ASC',
            [
                'from' => $from,
                'to' => $to,
            ],
            [
                'from' => Types::DATETIME_IMMUTABLE,
                'to' => Types::DATETIME_IMMUTABLE,
            ]
        );

        return array_map(static fn (array $row): array => [
            'day' => (string) ($row['day'] ?? ''),
            'total' => (int) ($row['total'] ?? 0),
            'revenue' => (float) ($row['revenue'] ?? 0),
        ], $rows);
    }

    /**
     * @return TicketOrder[]
     */
    public function findRecent(int $limit = 5): array
    {
        return $this->findBy([], ['createdAt' => 'DESC'], $limit);
    }

    /**
     * @return array<int, array{title: string, quantity: int, revenue: float}>
     */
    public function findTopMatches(\DateTimeInterface $from, \DateTimeInterface $to, int $limit = 5): array
    {
        return $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT t.title AS title, COALESCE(SUM(o.quantity), 0) AS quantity, COALESCE(SUM(o.total_price), 0) AS revenue
             FROM ticket_order o
             INNER JOIN ticket t ON t.id = o.ticket_id
             WHERE o.created_at BETWEEN :from AND :to
             GROUP BY t.id, t.title
             ORDER BY revenue DESC, quantity DESC, t.title ASC
             LIMIT :limit',
            [
                'from' => $from,
                'to' => $to,
                'limit' => $limit,
            ],
            [
                'from' => Types::DATETIME_IMMUTABLE,
                'to' => Types::DATETIME_IMMUTABLE,
                'limit' => Types::INTEGER,
            ]
        );
    }

    /**
     * @return array<int, array{title: string, opponent: string, match_date: string, quantity: int, orders: int, revenue: float}>
     */
    public function findMatchBreakdown(\DateTimeInterface $from, \DateTimeInterface $to, int $limit = 12): array
    {
        return $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT t.title AS title,
                    t.opponent AS opponent,
                    DATE_FORMAT(t.match_date, "%d/%m/%Y") AS match_date,
                    COALESCE(SUM(o.quantity), 0) AS quantity,
                    COUNT(o.id) AS orders,
                    COALESCE(SUM(o.total_price), 0) AS revenue
             FROM ticket_order o
             INNER JOIN ticket t ON t.id = o.ticket_id
             WHERE o.created_at BETWEEN :from AND :to
             GROUP BY t.id, t.title, t.opponent, t.match_date
             ORDER BY t.match_date DESC, revenue DESC, quantity DESC, t.title ASC
             LIMIT :limit',
            [
                'from' => $from,
                'to' => $to,
                'limit' => $limit,
            ],
            [
                'from' => Types::DATETIME_IMMUTABLE,
                'to' => Types::DATETIME_IMMUTABLE,
                'limit' => Types::INTEGER,
            ]
        );
    }
}
