<?php

namespace App\Repository;

use App\Entity\Donation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Donation>
 */
class DonationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Donation::class);
    }

    public function countPaidBetween(\DateTimeInterface $from, \DateTimeInterface $to): int
    {
        return (int) $this->createQueryBuilder('donation')
            ->select('COUNT(donation.id)')
            ->andWhere('donation.status = :status')
            ->andWhere('donation.paidAt BETWEEN :from AND :to')
            ->setParameter('status', Donation::STATUS_PAID)
            ->setParameter('from', $from, Types::DATETIME_IMMUTABLE)
            ->setParameter('to', $to, Types::DATETIME_IMMUTABLE)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function sumPaidBetween(\DateTimeInterface $from, \DateTimeInterface $to): float
    {
        return (float) $this->createQueryBuilder('donation')
            ->select('COALESCE(SUM(donation.amount), 0)')
            ->andWhere('donation.status = :status')
            ->andWhere('donation.paidAt BETWEEN :from AND :to')
            ->setParameter('status', Donation::STATUS_PAID)
            ->setParameter('from', $from, Types::DATETIME_IMMUTABLE)
            ->setParameter('to', $to, Types::DATETIME_IMMUTABLE)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function averagePaidBetween(\DateTimeInterface $from, \DateTimeInterface $to): float
    {
        $count = $this->countPaidBetween($from, $to);
        if ($count <= 0) {
            return 0.0;
        }

        return round($this->sumPaidBetween($from, $to) / $count, 2);
    }

    public function countByStatus(string $status): int
    {
        return (int) $this->createQueryBuilder('donation')
            ->select('COUNT(donation.id)')
            ->andWhere('donation.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countToday(): int
    {
        $today = new \DateTimeImmutable('today');

        return $this->countPaidBetween($today, $today->modify('+1 day -1 second'));
    }

    public function countThisWeek(): int
    {
        $from = new \DateTimeImmutable('monday this week');
        $to = new \DateTimeImmutable('sunday this week 23:59:59');

        return $this->countPaidBetween($from, $to);
    }

    public function countThisMonth(): int
    {
        $from = new \DateTimeImmutable('first day of this month');
        $to = new \DateTimeImmutable('last day of this month 23:59:59');

        return $this->countPaidBetween($from, $to);
    }

    public function countThisYear(): int
    {
        $from = new \DateTimeImmutable('first day of january this year');
        $to = new \DateTimeImmutable('last day of december this year 23:59:59');

        return $this->countPaidBetween($from, $to);
    }

    public function sumToday(): float
    {
        $today = new \DateTimeImmutable('today');

        return $this->sumPaidBetween($today, $today->modify('+1 day -1 second'));
    }

    public function sumThisWeek(): float
    {
        $from = new \DateTimeImmutable('monday this week');
        $to = new \DateTimeImmutable('sunday this week 23:59:59');

        return $this->sumPaidBetween($from, $to);
    }

    public function sumThisMonth(): float
    {
        $from = new \DateTimeImmutable('first day of this month');
        $to = new \DateTimeImmutable('last day of this month 23:59:59');

        return $this->sumPaidBetween($from, $to);
    }

    public function sumThisYear(): float
    {
        $from = new \DateTimeImmutable('first day of january this year');
        $to = new \DateTimeImmutable('last day of december this year 23:59:59');

        return $this->sumPaidBetween($from, $to);
    }

    /**
     * @return Donation[]
     */
    public function findRecentPaid(int $limit = 5): array
    {
        return $this->createQueryBuilder('donation')
            ->andWhere('donation.status = :status')
            ->setParameter('status', Donation::STATUS_PAID)
            ->orderBy('donation.paidAt', 'DESC')
            ->addOrderBy('donation.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Donation[]
     */
    public function findTopPaid(int $limit = 5): array
    {
        return $this->createQueryBuilder('donation')
            ->andWhere('donation.status = :status')
            ->setParameter('status', Donation::STATUS_PAID)
            ->orderBy('donation.amount', 'DESC')
            ->addOrderBy('donation.paidAt', 'DESC')
            ->addOrderBy('donation.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<int, array{day: string, total: int, revenue: float}>
     */
    public function countByDay(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $rows = $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT DATE(paid_at) AS day, COUNT(*) AS total, COALESCE(SUM(amount), 0) AS revenue
             FROM donation
             WHERE status = :status
               AND paid_at BETWEEN :from AND :to
             GROUP BY DATE(paid_at)
             ORDER BY day ASC',
            [
                'status' => Donation::STATUS_PAID,
                'from' => $from,
                'to' => $to,
            ],
            [
                'status' => Types::STRING,
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
}
