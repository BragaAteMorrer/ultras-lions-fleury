<?php

namespace App\Repository;

use App\Entity\InviteCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class InviteCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InviteCode::class);
    }

    /**
     * Vérifie si un code est valide
     */
    public function findValidCode(string $code): ?InviteCode
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.code = :code')
            ->andWhere('c.used = false')
            ->andWhere('c.expiresAt > :now')
            ->setParameter('code', $code)
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getOneOrNullResult();
    }
}
