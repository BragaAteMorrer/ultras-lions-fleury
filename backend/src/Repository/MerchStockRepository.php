<?php

namespace App\Repository;

use App\Entity\MerchStock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MerchStock>
 */
class MerchStockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MerchStock::class);
    }

    public function countLowStock(int $threshold = 5): int
    {
        return (int) $this->createQueryBuilder('stock')
            ->select('COUNT(stock.id)')
            ->andWhere('stock.quantity <= :threshold')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return MerchStock[]
     */
    public function findLowStock(int $limit = 5, int $threshold = 5): array
    {
        return $this->createQueryBuilder('stock')
            ->leftJoin('stock.merch', 'merch')
            ->addSelect('merch')
            ->andWhere('stock.quantity <= :threshold')
            ->setParameter('threshold', $threshold)
            ->orderBy('stock.quantity', 'ASC')
            ->addOrderBy('merch.title', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findOneBySumupSku(string $sumupSku): ?MerchStock
    {
        $sumupSku = trim($sumupSku);
        if ($sumupSku === '') {
            return null;
        }

        return $this->findOneBy(['sumupSku' => $sumupSku]);
    }
}

