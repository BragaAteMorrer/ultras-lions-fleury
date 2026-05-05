<?php

namespace App\Service;

use App\Entity\CartageRegistration;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CartageProfileCreator
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private LoggerInterface $logger,
    ) {
    }

    public function createProfileIfNeeded(CartageRegistration $registration): ?User
    {
        $email = trim($registration->getEmail());
        if ($email === '') {
            return null;
        }

        try {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            $isNew = !$user instanceof User;

            if ($isNew) {
                $user = new User();
                $this->callRequired($user, 'setEmail', $email);
                $this->callIfExists($user, 'setRoles', ['ROLE_USER']);
                $this->callIfExists($user, 'setIsVerified', true);
                $this->callIfExists($user, 'setVerified', true);
                $this->setPassword($user, $registration);
                $this->touch($user, true);
                $this->entityManager->persist($user);
            }

            $this->hydrateLikeRegistrationForm($user, $registration);
            $this->touch($user, false);

            return $user;
        } catch (\Throwable $exception) {
            $this->logger->error('Unable to create cartage user profile.', [
                'cartage_registration_id' => $registration->getId(),
                'email' => $email,
                'exception' => $exception,
            ]);

            return null;
        }
    }

    private function hydrateLikeRegistrationForm(User $user, CartageRegistration $registration): void
    {
        $tailleTshirt = $registration->getShirtSize();
        $tailleSweat = $registration->getSweatSize();
        $tailleVeste = $registration->getJacketSize();
        $tailleShort = $registration->getShortSize();

        $this->callRequired($user, 'setPrenom', $registration->getFirstName());
        $this->callRequired($user, 'setNom', $registration->getLastName());
        $this->callRequired($user, 'setEmail', $registration->getEmail());
        $this->callIfExists($user, 'setTelephone', $registration->getPhone());
        $this->callIfExists($user, 'setAdresse', $registration->getAddress() ?? '');
        $this->callIfExists($user, 'setVille', $registration->getCity() ?? '');
        $this->callIfExists($user, 'setCodePostal', $registration->getPostalCode() ?? '');
        $this->callIfExists($user, 'setTailleTshirt', $tailleTshirt ?: null);
        $this->callIfExists($user, 'setTailleSweat', $tailleSweat ?: null);
        $this->callIfExists($user, 'setTailleVeste', $tailleVeste ?: null);
        $this->callIfExists($user, 'setTailleShort', $tailleShort ?: null);
    }

    private function setPassword(User $user, CartageRegistration $registration): void
    {
        if (!method_exists($user, 'setPassword')) {
            return;
        }

        $password = $registration->getPassword();
        if ($password !== null && $password !== '') {
            $user->setPassword($password);

            return;
        }

        $temporaryPassword = bin2hex(random_bytes(24));
        $user->setPassword($this->passwordHasher->hashPassword($user, $temporaryPassword));
    }

    private function touch(User $user, bool $isNew): void
    {
        $now = new \DateTimeImmutable();

        if ($isNew) {
            $this->callDateIfExists($user, 'setCreatedAt', $now);
        }

        $this->callDateIfExists($user, 'setUpdatedAt', $now);
    }

    private function callRequired(User $user, string $method, mixed $value): void
    {
        if (!method_exists($user, $method)) {
            throw new \LogicException(sprintf('User::%s() is missing, but it is required by RegistrationFormType.', $method));
        }

        $user->{$method}($value);
    }

    private function callIfExists(User $user, string $method, mixed $value): void
    {
        if (!method_exists($user, $method)) {
            return;
        }

        $user->{$method}($value);
    }

    private function callDateIfExists(User $user, string $method, \DateTimeImmutable $value): void
    {
        if (!method_exists($user, $method)) {
            return;
        }

        try {
            $reflection = new \ReflectionMethod($user, $method);
            $parameter = $reflection->getParameters()[0] ?? null;
            $type = $parameter?->getType();

            if ($type instanceof \ReflectionNamedType && ltrim($type->getName(), '\\') === \DateTime::class) {
                $user->{$method}(\DateTime::createFromImmutable($value));

                return;
            }
        } catch (\Throwable) {
        }

        $user->{$method}($value);
    }
}
