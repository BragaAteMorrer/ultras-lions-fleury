<?php

namespace App\Repository;

use App\Entity\Merch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Merch>
 */
class MerchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Merch::class);
    }

    /**
     * @return Merch[]
     */
    public function findVisibleForUser(?MerchCategory $category, bool $isMember): array
    {
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.category', 'cat')
            ->addSelect('cat')
            ->orderBy('m.id', 'DESC');

        if ($category) {
            $qb->andWhere('m.category = :category')
                ->setParameter('category', $category);
        }

        if (!$isMember) {
            $qb->andWhere('m.audience = :audiencePublic')
                ->setParameter('audiencePublic', 'public');
        }

        return $qb->getQuery()->getResult();
    }
}
