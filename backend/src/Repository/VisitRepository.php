<?php

namespace App\Repository;

use App\Entity\Visit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Visit>
 */
class VisitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Visit::class);
    }

    public function countBetween(\DateTimeInterface $from, \DateTimeInterface $to): int
    {
        return (int) $this->createQueryBuilder('visit')
            ->select('COUNT(visit.id)')
            ->andWhere('visit.visitedAt BETWEEN :from AND :to')
            ->setParameter('from', $from, Types::DATETIME_IMMUTABLE)
            ->setParameter('to', $to, Types::DATETIME_IMMUTABLE)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return array<int, array{day: string, total: int}>
     */
    public function countByDay(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $rows = $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT DATE(visited_at) AS day, COUNT(*) AS total
             FROM visit
             WHERE visited_at BETWEEN :from AND :to
             GROUP BY DATE(visited_at)
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
        ], $rows);
    }

    /**
     * @return Visit[]
     */
    public function findRecent(int $limit = 5): array
    {
        return $this->findBy([], ['visitedAt' => 'DESC'], $limit);
    }
}
