<?php

namespace App\Repository;

use App\Entity\CartageQrToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CartageQrToken>
 */
class CartageQrTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartageQrToken::class);
    }

    public function findLatestUsable(): ?CartageQrToken
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.enabled = :enabled')
            ->andWhere('q.expiresAt IS NULL OR q.expiresAt >= :now')
            ->setParameter('enabled', true)
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('q.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
