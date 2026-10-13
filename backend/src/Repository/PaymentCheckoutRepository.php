<?php

namespace App\Repository;

use App\Entity\PaymentCheckout;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PaymentCheckout>
 */
class PaymentCheckoutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentCheckout::class);
    }

    public function countPending(): int
    {
        return (int) $this->createQueryBuilder('checkout')
            ->select('COUNT(checkout.id)')
            ->andWhere('checkout.status = :status')
            ->setParameter('status', 'pending')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countPaidBetween(\DateTimeInterface $from, \DateTimeInterface $to): int
    {
        return (int) $this->createQueryBuilder('checkout')
            ->select('COUNT(checkout.id)')
            ->andWhere('checkout.status = :status')
            ->andWhere('checkout.paidAt BETWEEN :from AND :to')
            ->setParameter('status', 'paid')
            ->setParameter('from', $from, Types::DATETIME_IMMUTABLE)
            ->setParameter('to', $to, Types::DATETIME_IMMUTABLE)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function sumPaidBetween(\DateTimeInterface $from, \DateTimeInterface $to): float
    {
        return (float) $this->createQueryBuilder('checkout')
            ->select('COALESCE(SUM(checkout.amount), 0)')
            ->andWhere('checkout.status = :status')
            ->andWhere('checkout.paidAt BETWEEN :from AND :to')
            ->setParameter('status', 'paid')
            ->setParameter('from', $from, Types::DATETIME_IMMUTABLE)
            ->setParameter('to', $to, Types::DATETIME_IMMUTABLE)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
