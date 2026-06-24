<?php

namespace App\Repository;

use App\Entity\LinktreeLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LinktreeLink>
 */
class LinktreeLinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LinktreeLink::class);
    }

    /**
     * @return LinktreeLink[]
     */
    public function findEnabledLinks(): array
    {
        return $this->createQueryBuilder('link')
            ->andWhere('link.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('link.position', 'ASC')
            ->addOrderBy('link.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
