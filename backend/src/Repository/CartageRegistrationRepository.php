<?php

namespace App\Repository;

use App\Entity\CartageRegistration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CartageRegistration>
 */
class CartageRegistrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartageRegistration::class);
    }

    /**
     * @return string[]
     */
    public function findSeasons(): array
    {
        try {
            $seasons = $this->createQueryBuilder('c')
                ->select('DISTINCT c.season AS season')
                ->andWhere('c.season IS NOT NULL')
                ->andWhere("c.season <> ''")
                ->orderBy('c.season', 'DESC')
                ->getQuery()
                ->getSingleColumnResult();

            return array_values(array_filter(array_map('strval', $seasons)));
        } catch (\Throwable) {
            return [];
        }
    }

    public function findLatestForSeasonByEmail(string $email, string $season): ?CartageRegistration
    {
        $email = strtolower(trim($email));
        if ($email === '' || $season === '') {
            return null;
        }

        try {
            return $this->createQueryBuilder('c')
                ->leftJoin('c.qrToken', 'q')
                ->andWhere('LOWER(c.email) = :email')
                ->andWhere('(c.season = :season OR q.label IN (:seasonLabels))')
                ->andWhere('c.status != :cancelled')
                ->setParameter('email', $email)
                ->setParameter('season', $season)
                ->setParameter('seasonLabels', $this->getSeasonLabels($season))
                ->setParameter('cancelled', CartageRegistration::STATUS_CANCELLED)
                ->orderBy('c.createdAt', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (\Throwable) {
            return null;
        }
    }

    public function findCompletedForSeasonByEmail(string $email, string $season): ?CartageRegistration
    {
        $email = strtolower(trim($email));
        if ($email === '' || $season === '') {
            return null;
        }

        try {
            return $this->createQueryBuilder('c')
                ->leftJoin('c.qrToken', 'q')
                ->andWhere('LOWER(c.email) = :email')
                ->andWhere('(c.season = :season OR q.label IN (:seasonLabels))')
                ->andWhere('c.status IN (:completedStatuses)')
                ->setParameter('email', $email)
                ->setParameter('season', $season)
                ->setParameter('seasonLabels', $this->getSeasonLabels($season))
                ->setParameter('completedStatuses', CartageRegistration::COMPLETED_STATUSES)
                ->orderBy('c.createdAt', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return string[]
     */
    private function getSeasonLabels(string $season): array
    {
        $season = trim($season);
        if ($season === '') {
            return [];
        }

        return array_values(array_unique([
            $season,
            str_replace('-', '/', $season),
        ]));
    }
}
