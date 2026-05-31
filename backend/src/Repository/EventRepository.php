<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @return string[]
     */
    public function findSeasonsBySection(string $section): array
    {
        return $this->createQueryBuilder('e')
            ->select('DISTINCT e.season AS season')
            ->join('e.category', 'cat')
            ->where('cat.section = :section')
            ->andWhere('e.season IS NOT NULL')
            ->andWhere("e.season <> ''")
            ->setParameter('section', $section)
            ->orderBy('e.season', 'DESC')
            ->getQuery()
            ->getSingleColumnResult();
    }
}
