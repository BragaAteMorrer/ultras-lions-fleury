<?php

namespace App\Repository;

use App\Entity\MerchOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MerchOrder>
 */
class MerchOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MerchOrder::class);
    }

    public function countBetween(\DateTimeInterface $from, \DateTimeInterface $to): int
    {
        return (int) $this->createQueryBuilder('merchOrder')
            ->select('COUNT(merchOrder.id)')
            ->andWhere('merchOrder.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $from, Types::DATETIME_IMMUTABLE)
            ->setParameter('to', $to, Types::DATETIME_IMMUTABLE)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countPendingConfirmation(): int
    {
        return (int) $this->createQueryBuilder('merchOrder')
            ->select('COUNT(merchOrder.id)')
            ->andWhere('merchOrder.confirmedAt IS NULL')
            ->andWhere('merchOrder.executed = false')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countConfirmedAwaitingExecution(): int
    {
        return (int) $this->createQueryBuilder('merchOrder')
            ->select('COUNT(merchOrder.id)')
            ->andWhere('merchOrder.confirmedAt IS NOT NULL')
            ->andWhere('merchOrder.executed = false')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function sumTotalBetween(\DateTimeInterface $from, \DateTimeInterface $to): float
    {
        return (float) $this->createQueryBuilder('merchOrder')
            ->select('COALESCE(SUM(merchOrder.totalPrice), 0)')
            ->andWhere('merchOrder.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $from, Types::DATETIME_IMMUTABLE)
            ->setParameter('to', $to, Types::DATETIME_IMMUTABLE)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function sumQuantityBetween(\DateTimeInterface $from, \DateTimeInterface $to): int
    {
        return (int) $this->createQueryBuilder('merchOrder')
            ->select('COALESCE(SUM(merchOrder.quantity), 0)')
            ->andWhere('merchOrder.createdAt BETWEEN :from AND :to')
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
             FROM merch_order
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
     * @return MerchOrder[]
     */
    public function findRecent(int $limit = 5): array
    {
        return $this->findBy([], ['createdAt' => 'DESC'], $limit);
    }

    /**
     * @return MerchOrder[]
     */
    public function findPendingConfirmation(int $limit = 5): array
    {
        return $this->createQueryBuilder('merchOrder')
            ->andWhere('merchOrder.confirmedAt IS NULL')
            ->andWhere('merchOrder.executed = false')
            ->orderBy('merchOrder.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<int, array{title: string, quantity: int, revenue: float}>
     */
    public function findTopProducts(\DateTimeInterface $from, \DateTimeInterface $to, int $limit = 5): array
    {
        return $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT m.title AS title, COALESCE(SUM(o.quantity), 0) AS quantity, COALESCE(SUM(o.total_price), 0) AS revenue
             FROM merch_order o
             INNER JOIN merch m ON m.id = o.merch_id
             WHERE o.created_at BETWEEN :from AND :to
             GROUP BY m.id, m.title
             ORDER BY revenue DESC, quantity DESC, m.title ASC
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
