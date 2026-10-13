<?php

namespace App\Repository;

use App\Entity\Gadget;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Gadget>
 */
class GadgetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gadget::class);
    }

    /**
     * @return Gadget[]
     */
    public function findVisible(int $limit = 0): array
    {
        $qb = $this->createQueryBuilder('g')
            ->leftJoin('g.image', 'image')
            ->addSelect('image')
            ->andWhere('g.visible = :visible')
            ->setParameter('visible', true)
            ->orderBy('g.position', 'ASC')
            ->addOrderBy('g.createdAt', 'DESC');

        if ($limit > 0) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }
}
