<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\EventCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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
        return $this->findPublicPostsPage($limit, 0);
    }

    /**
     * @return Post[]
     */
    public function findPublicPostsPage(
        ?int $limit = null,
        int $offset = 0,
        ?string $search = null,
        ?string $categorySlug = null,
        ?array $excludeIds = null,
    ): array {
        $qb = $this->createPublicPostsQueryBuilder($search, $categorySlug, $excludeIds);

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        if ($offset > 0) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function countPublicPosts(
        ?string $search = null,
        ?string $categorySlug = null,
        ?array $excludeIds = null,
    ): int {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'cat');

        $this->applyPublicPostFilters($qb, $search, $categorySlug, $excludeIds);

        return (int) $qb
            ->select('COUNT(DISTINCT p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return EventCategory[]
     */
    public function findPublicCategories(): array
    {
        $rows = $this->createQueryBuilder('p')
            ->select('DISTINCT IDENTITY(p.category) AS categoryId')
            ->leftJoin('p.category', 'cat')
            ->andWhere('p.category IS NOT NULL')
            ->andWhere('(cat.slug IS NULL OR cat.slug != :communique OR p.createdAt <= :now)')
            ->setParameter('communique', 'communique')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getScalarResult();

        $categoryIds = array_values(array_unique(array_map(
            static fn (array $row): int => (int) ($row['categoryId'] ?? 0),
            $rows
        )));

        if ($categoryIds === []) {
            return [];
        }

        return $this->getEntityManager()->getRepository(EventCategory::class)->findBy(
            ['id' => $categoryIds],
            ['name' => 'ASC']
        );
    }

    /**
     * @return Post[]
     */
    public function findPublicRelatedPosts(Post $post, int $limit = 3): array
    {
        $related = [];
        $excludedIds = [];

        if ($post->getId() !== null) {
            $excludedIds[] = $post->getId();
        }

        $categorySlug = $post->getCategory()?->getSlug();
        if ($categorySlug) {
            $sameCategory = $this->findPublicPostsPage(
                $limit,
                0,
                null,
                $categorySlug,
                $excludedIds
            );

            foreach ($sameCategory as $item) {
                $related[] = $item;
                if ($item->getId() !== null) {
                    $excludedIds[] = $item->getId();
                }
            }
        }

        if (count($related) < $limit) {
            $fallback = $this->findPublicPostsPage(
                $limit * 2,
                0,
                null,
                null,
                $excludedIds
            );

            foreach ($fallback as $item) {
                if (count($related) >= $limit) {
                    break;
                }

                if ($item->getId() !== null && in_array($item->getId(), $excludedIds, true)) {
                    continue;
                }

                $related[] = $item;
                if ($item->getId() !== null) {
                    $excludedIds[] = $item->getId();
                }
            }
        }

        return array_slice($related, 0, $limit);
    }

    public function findPublicBySlug(string $slug): ?Post
    {
        $qb = $this->createPublicPostsQueryBuilder();

        return $qb
            ->andWhere('p.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function createPublicPostsQueryBuilder(
        ?string $search = null,
        ?string $categorySlug = null,
        ?array $excludeIds = null,
    ): QueryBuilder {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'cat')
            ->addSelect('cat')
            ->leftJoin('p.image', 'image')
            ->addSelect('image')
            ->orderBy('p.createdAt', 'DESC')
            ->addOrderBy('p.id', 'DESC');

        $this->applyPublicPostFilters($qb, $search, $categorySlug, $excludeIds);

        return $qb;
    }

    private function applyPublicPostFilters(
        QueryBuilder $qb,
        ?string $search = null,
        ?string $categorySlug = null,
        ?array $excludeIds = null,
    ): void {
        $qb
            ->andWhere('(cat.slug IS NULL OR cat.slug != :communique OR p.createdAt <= :now)')
            ->setParameter('communique', 'communique')
            ->setParameter('now', new \DateTime());

        if ($search !== null && $search !== '') {
            $qb
                ->andWhere('(
                    LOWER(p.title) LIKE :search
                    OR LOWER(COALESCE(p.content, \'\')) LIKE :search
                    OR LOWER(COALESCE(cat.name, \'\')) LIKE :search
                    OR LOWER(COALESCE(cat.slug, \'\')) LIKE :search
                )')
                ->setParameter('search', '%' . mb_strtolower($search) . '%');
        }

        if ($categorySlug !== null && $categorySlug !== '') {
            $qb
                ->andWhere('cat.slug = :categorySlug')
                ->setParameter('categorySlug', $categorySlug);
        }

        if ($excludeIds !== null && $excludeIds !== []) {
            $qb
                ->andWhere('p.id NOT IN (:excludeIds)')
                ->setParameter('excludeIds', array_values(array_unique(array_map('intval', $excludeIds))));
        }
    }
}
