<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @return Post[]
     */
    public function findPublicPosts(?int $limit = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'cat')
            ->addSelect('cat')
            ->orderBy('p.createdAt', 'DESC');

        $now = new \DateTime();
        $qb->andWhere('(cat.slug IS NULL OR cat.slug != :communique OR p.createdAt <= :now)')
            ->setParameter('communique', 'communique')
            ->setParameter('now', $now);

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function findPublicBySlug(string $slug): ?Post
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'cat')
            ->addSelect('cat')
            ->where('p.slug = :slug')
            ->setParameter('slug', $slug);

        $now = new \DateTime();
        $qb->andWhere('(cat.slug IS NULL OR cat.slug != :communique OR p.createdAt <= :now)')
            ->setParameter('communique', 'communique')
            ->setParameter('now', $now);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
