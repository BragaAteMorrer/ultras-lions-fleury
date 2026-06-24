<?php

namespace App\Repository;

use App\Entity\BilletwebLead;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BilletwebLead>
 */
class BilletwebLeadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BilletwebLead::class);
    }

    /**
     * @return BilletwebLead[]
     */
    public function findForUserProfile(User $user): array
    {
        $qb = $this->createQueryBuilder('lead')
            ->leftJoin('lead.ticket', 'ticket')
            ->addSelect('ticket')
            ->where('lead.user = :user')
            ->setParameter('user', $user)
            ->orderBy('lead.createdAt', 'DESC');

        $email = strtolower(trim((string) $user->getEmail()));
        if ($email !== '') {
            $qb
                ->orWhere('LOWER(lead.email) = :email')
                ->setParameter('email', $email);
        }

        return $qb->getQuery()->getResult();
    }
}
