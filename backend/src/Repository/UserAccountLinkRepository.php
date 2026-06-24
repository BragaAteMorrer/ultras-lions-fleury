<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserAccountLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserAccountLink>
 */
class UserAccountLinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAccountLink::class);
    }

    /**
     * @return UserAccountLink[]
     */
    public function findAcceptedForGuardian(User $guardian): array
    {
        return $this->createQueryBuilder('link')
            ->andWhere('link.guardian = :guardian')
            ->andWhere('link.status = :status')
            ->setParameter('guardian', $guardian)
            ->setParameter('status', UserAccountLink::STATUS_ACCEPTED)
            ->orderBy('link.acceptedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return UserAccountLink[]
     */
    public function findPendingForGuardian(User $guardian): array
    {
        return $this->createQueryBuilder('link')
            ->andWhere('link.guardian = :guardian')
            ->andWhere('link.status = :status')
            ->setParameter('guardian', $guardian)
            ->setParameter('status', UserAccountLink::STATUS_PENDING)
            ->orderBy('link.requestedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return UserAccountLink[]
     */
    public function findPendingForManagedUser(User $managedUser): array
    {
        return $this->createQueryBuilder('link')
            ->andWhere('link.managedUser = :managedUser')
            ->andWhere('link.status = :status')
            ->setParameter('managedUser', $managedUser)
            ->setParameter('status', UserAccountLink::STATUS_PENDING)
            ->orderBy('link.requestedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return UserAccountLink[]
     */
    public function findAcceptedForManagedUser(User $managedUser): array
    {
        return $this->createQueryBuilder('link')
            ->andWhere('link.managedUser = :managedUser')
            ->andWhere('link.status = :status')
            ->setParameter('managedUser', $managedUser)
            ->setParameter('status', UserAccountLink::STATUS_ACCEPTED)
            ->orderBy('link.acceptedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findExisting(User $guardian, User $managedUser): ?UserAccountLink
    {
        return $this->findOneBy([
            'guardian' => $guardian,
            'managedUser' => $managedUser,
        ]);
    }
}
