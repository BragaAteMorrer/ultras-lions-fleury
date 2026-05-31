<php

namespace App\Service;

use App\Entity\CartageRegistration;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CartageProfileCreator
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private MailService $mailService,
        private LoggerInterface $logger,
    ) {
    }

    public function createProfileIfNeeded(CartageRegistration $registration): User
    {
        $email = trim($registration->getEmail());
        if ($email === '') {
            return null;
        }

        try {
            $user = $this->userRepository->findOneByEmail($email);
            $isNew = !$user instanceof User;
            $passwordRepaired = false;

            if ($isNew) {
                $user = new User();
                $this->callRequired($user, 'setEmail', $email);
                $this->callIfExists($user, 'setRoles', ['ROLE_USER']);
                $this->callIfExists($user, 'setIsVerified', true);
                $this->callIfExists($user, 'setVerified', true);
                $this->setPassword($user, $registration);
                $this->touch($user, true);
                $this->entityManager->persist($user);
            } elseif (trim((string) $user->getPassword()) === '') {
                $this->setPassword($user, $registration);
                $passwordRepaired = true;
                $this->logger->warning('Cartage user existed without a usable password; password repaired from registration.', [
                    'email' => $email,
                    'cartage_registration_id' => $registration->getId(),
                ]);
            }

            $this->hydrateLikeRegistrationForm($user, $registration);
            $this->touch($user, false);

            if ($isNew || $passwordRepaired) {
                $this->sendWelcomeEmail($user);
            }

            $this->logger->info('Cartage profile ensured.', [
                'email' => $email,
                'cartage_registration_id' => $registration->getId(),
                'is_new' => $isNew,
                'password_repaired' => $passwordRepaired,
            ]);

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
        $taillePolo = $registration->getPoloSize();
        $taillePull = $registration->getPullSize();
        $tailleSweat = $registration->getSweatSize();
        $tailleVeste = $registration->getJacketSize();
        $tailleShort = $registration->getShortSize();

        $this->callRequired($user, 'setPrenom', $registration->getFirstName());
        $this->callRequired($user, 'setNom', $registration->getLastName());
        $this->callRequired($user, 'setEmail', $registration->getEmail());
        $this->callIfExists($user, 'setTelephone', $registration->getPhone());
        $this->callIfExists(
            $user,
            'setDateNaissance',
            $registration->getBirthDate() instanceof \DateTimeImmutable
                 \DateTime::createFromImmutable($registration->getBirthDate())
                : $registration->getBirthDate()
        );
        $this->callIfExists($user, 'setAdresse', $registration->getAddress()  '');
        $this->callIfExists($user, 'setVille', $registration->getCity()  '');
        $this->callIfExists($user, 'setCodePostal', $registration->getPostalCode()  '');
        $this->callIfExists($user, 'setTailleTshirt', $tailleTshirt : null);
        $this->callIfExists($user, 'setTaillePolo', $taillePolo : null);
        $this->callIfExists($user, 'setTaillePull', $taillePull : null);
        $this->callIfExists($user, 'setTailleSweat', $tailleSweat : null);
        $this->callIfExists($user, 'setTailleVeste', $tailleVeste : null);
        $this->callIfExists($user, 'setTailleShort', $tailleShort : null);
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
            $parameter = $reflection->getParameters()[0]  null;
            $type = $parameter->getType();

            if ($type instanceof \ReflectionNamedType && ltrim($type->getName(), '\\') === \DateTime::class) {
                $user->{$method}(\DateTime::createFromImmutable($value));

                return;
            }
        } catch (\Throwable) {
        }

        $user->{$method}($value);
    }

    private function sendWelcomeEmail(User $user): void
    {
        $email = trim((string) $user->getEmail());
        if ($email === '') {
            return;
        }

        try {
            $this->mailService->send(
                to: $email,
                subject: 'Ton compte ULTRAS LIONS est pret',
                template: 'email/welcome.html.twig',
                context: [
                    'user' => $user,
                ]
            );
        } catch (\Throwable $exception) {
            $this->logger->warning('Unable to send cartage welcome email.', [
                'email' => $email,
                'exception' => $exception,
            ]);
        }
    }
}
