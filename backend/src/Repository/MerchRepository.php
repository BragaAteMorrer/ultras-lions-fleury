<?php

namespace App\Repository;

use App\Entity\Merch;
use App\Entity\MerchCategory;
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
            ->leftJoin('m.image', 'img')
            ->leftJoin('m.category', 'cat')
            ->addSelect('img, cat')
            ->orderBy('m.id', 'DESC');

        if ($category) {
            $qb->andWhere('m.category = :category')
                ->setParameter('category', $category);
        }

        if (!$isMember) {
            $qb->andWhere('m.audience = :audiencePublic')
                ->andWhere('(m.publicUntil IS NULL OR m.publicUntil >= :now)')
                ->setParameter('audiencePublic', 'public')
                ->setParameter('now', new \DateTimeImmutable());
        }

        return $qb->getQuery()->getResult();
    }

    public function isVisibleForUser(Merch $merch, bool $isMember, ?\DateTimeInterface $now = null): bool
    {
        return $merch->isVisibleForUser($isMember, $now);
    }
}
