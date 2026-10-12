<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByRole(string $role)
    {
        return $this->createQueryBuilder('u')
            ->where('JSON_CONTAINS(u.roles, :role) = 1')
            ->setParameter('role', json_encode([$role]))
            ->getQuery()
            ->getResult();
    }

    public function findOneByEmail(string $email): ?User
    {
        $email = strtolower(trim($email));
        if ($email === '') {
            return null;
        }

        return $this->createQueryBuilder('u')
            ->andWhere('LOWER(u.email) = :email')
            ->setParameter('email', $email)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByResetPasswordToken(string $token): ?User
    {
        $token = trim($token);
        if ($token === '') {
            return null;
        }

        $hashedToken = hash('sha256', $token);

        return $this->createQueryBuilder('u')
            ->andWhere('u.resetPasswordToken = :token OR u.resetPasswordToken = :hashedToken')
            ->setParameter('token', $token)
            ->setParameter('hashedToken', $hashedToken)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
