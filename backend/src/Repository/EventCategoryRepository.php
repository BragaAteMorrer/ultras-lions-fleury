<?php

namespace App\Repository;

use App\Entity\EventCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventCategory>
 */
class EventCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventCategory::class);
    }

    /**
     * @return EventCategory[]
     */
    public function findBySection(string $section): array
    {
        return $this->findBy(['section' => $section], ['name' => 'ASC']);
    }

    public function findOneBySection(string $section): ?EventCategory
    {
        return $this->findOneBy(['section' => $section], ['id' => 'ASC']);
    }
}
