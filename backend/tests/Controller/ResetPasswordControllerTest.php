<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class ResetPasswordControllerTest extends WebTestCase
{
    private const HOST = 'app.example.test';

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        static::createClient([], ['HTTP_HOST' => self::HOST]);

        $entityManager = $this->entityManager();
        $schemaTool = new SchemaTool($entityManager);
        $metadata = [$entityManager->getClassMetadata(User::class)];

        try {
            $schemaTool->dropSchema($metadata);
        } catch (\Throwable) {
        }

        $schemaTool->createSchema($metadata);
    }

    public function testForgotPasswordCreatesTokenAndSendsAbsoluteLink(): void
    {
        $user = $this->createUser();
        $client = static::createClient([], ['HTTP_HOST' => self::HOST]);

        $capturedResetUrl = null;
        $mailService = $this->createMock(MailService::class);
        $mailService->expects(self::once())
            ->method('send')
            ->with(
                $user->getEmail(),
                'Reinitialisation de ton mot de passe',
                'email/reset_password.html.twig',
                self::callback(function (array $context) use (&$capturedResetUrl, $user): bool {
                    self::assertSame($user->getId(), $context['user']->getId());
                    self::assertArrayHasKey('resetUrl', $context);
                    self::assertMatchesRegularExpression(
                        '#^http://' . preg_quote(self::HOST, '#') . '/reset-password/[a-f0-9]{64}$#',
                        $context['resetUrl']
                    );

                    $capturedResetUrl = $context['resetUrl'];

                    return true;
                })
            );
        static::getContainer()->set(MailService::class, $mailService);

        $client->request('GET', '/forgot-password');
        $forgotToken = $client->getCrawler()->filter('input[name="_token"]')->attr('value');

        $client->request('POST', '/forgot-password', [
            'email' => $user->getEmail(),
            '_token' => $forgotToken,
        ]);

        self::assertResponseRedirects('/login');

        $this->entityManager()->clear();
        $updatedUser = $this->userRepository()->find($user->getId());
        self::assertInstanceOf(User::class, $updatedUser);
        self::assertNotNull($updatedUser->getResetPasswordToken());
        self::assertNotNull($updatedUser->getResetPasswordTokenExpiresAt());
        self::assertGreaterThan(new \DateTimeImmutable(), $updatedUser->getResetPasswordTokenExpiresAt());

        $plainToken = basename((string) parse_url((string) $capturedResetUrl, PHP_URL_PATH));
        self::assertSame(hash('sha256', $plainToken), $updatedUser->getResetPasswordToken());
    }

    public function testForgotPasswordReturnsGenericResponseForUnknownEmail(): void
    {
        $client = static::createClient([], ['HTTP_HOST' => self::HOST]);

        $mailService = $this->createMock(MailService::class);
        $mailService->expects(self::never())->method('send');
        static::getContainer()->set(MailService::class, $mailService);

        $client->request('GET', '/forgot-password');
        $forgotToken = $client->getCrawler()->filter('input[name="_token"]')->attr('value');

        $client->request('POST', '/forgot-password', [
            'email' => 'missing-' . bin2hex(random_bytes(3)) . '@example.test',
            '_token' => $forgotToken,
        ]);

        self::assertResponseRedirects('/login');
    }

    public function testForgotPasswordKeepsGenericResponseWhenMailerFails(): void
    {
        $user = $this->createUser();
        $client = static::createClient([], ['HTTP_HOST' => self::HOST]);

        $mailService = $this->createMock(MailService::class);
        $mailService->expects(self::once())
            ->method('send')
            ->willThrowException(new \RuntimeException('SMTP down'));
        static::getContainer()->set(MailService::class, $mailService);

        $client->request('GET', '/forgot-password');
        $forgotToken = $client->getCrawler()->filter('input[name="_token"]')->attr('value');

        $client->request('POST', '/forgot-password', [
            'email' => $user->getEmail(),
            '_token' => $forgotToken,
        ]);

        self::assertResponseRedirects('/login');

        $this->entityManager()->clear();
        $updatedUser = $this->userRepository()->find($user->getId());
        self::assertInstanceOf(User::class, $updatedUser);
        self::assertNotNull($updatedUser->getResetPasswordToken());
    }

    public function testResetPasswordUpdatesPasswordAndInvalidatesToken(): void
    {
        $oldPassword = 'OldP@ssword1';
        $newPassword = 'NewP@ssword1';
        $user = $this->createUser($oldPassword);
        $client = static::createClient([], ['HTTP_HOST' => self::HOST]);

        $mailService = $this->createMock(MailService::class);
        $capturedResetUrl = null;
        $mailService->expects(self::once())
            ->method('send')
            ->willReturnCallback(function (string $to, string $subject, string $template, array $context) use (&$capturedResetUrl): void {
                $capturedResetUrl = $context['resetUrl'];
            });
        static::getContainer()->set(MailService::class, $mailService);

        $client->request('GET', '/forgot-password');
        $forgotToken = $client->getCrawler()->filter('input[name="_token"]')->attr('value');

        $client->request('POST', '/forgot-password', [
            'email' => $user->getEmail(),
            '_token' => $forgotToken,
        ]);

        self::assertResponseRedirects('/login');

        $plainToken = basename((string) parse_url((string) $capturedResetUrl, PHP_URL_PATH));
        self::assertNotSame('', $plainToken);

        $client->request('GET', '/reset-password/' . $plainToken);
        self::assertResponseIsSuccessful();
        $resetToken = $client->getCrawler()->filter('input[name="_token"]')->attr('value');

        $client->request('POST', '/reset-password/' . $plainToken, [
            '_token' => $resetToken,
            'password' => $newPassword,
            'confirm_password' => $newPassword,
        ]);

        self::assertResponseRedirects('/login');

        $this->entityManager()->clear();
        $updatedUser = $this->userRepository()->find($user->getId());
        self::assertInstanceOf(User::class, $updatedUser);
        self::assertNull($updatedUser->getResetPasswordToken());
        self::assertNull($updatedUser->getResetPasswordTokenExpiresAt());
        self::assertTrue($this->passwordHasher()->isPasswordValid($updatedUser, $newPassword));
        self::assertFalse($this->passwordHasher()->isPasswordValid($updatedUser, $oldPassword));

        $this->assertLoginFails(
            static::createClient([], ['HTTP_HOST' => self::HOST]),
            $updatedUser->getEmail(),
            $oldPassword
        );

        $this->assertLoginSucceeds(
            static::createClient([], ['HTTP_HOST' => self::HOST]),
            $updatedUser->getEmail(),
            $newPassword
        );

        $client->request('GET', '/reset-password/' . $plainToken);
        self::assertResponseRedirects('/forgot-password');
    }

    public function testExpiredResetTokenIsRejected(): void
    {
        $user = $this->createUser();
        $plainToken = bin2hex(random_bytes(32));
        $user->setResetPasswordToken(hash('sha256', $plainToken));
        $user->setResetPasswordTokenExpiresAt(new \DateTimeImmutable('-10 minutes'));
        $this->entityManager()->flush();

        $client = static::createClient([], ['HTTP_HOST' => self::HOST]);
        $client->request('GET', '/reset-password/' . $plainToken);

        self::assertResponseRedirects('/forgot-password');
    }

    private function createUser(string $plainPassword = 'OldP@ssword1'): User
    {
        $email = sprintf('user-%s@example.test', bin2hex(random_bytes(4)));

        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher()->hashPassword($user, $plainPassword));

        $this->entityManager()->persist($user);
        $this->entityManager()->flush();

        return $user;
    }

    private function assertLoginSucceeds($client, string $email, string $password): void
    {
        $client->request('GET', '/login');
        $csrfToken = $client->getCrawler()->filter('input[name="_csrf_token"]')->attr('value');

        $client->request('POST', '/login', [
            '_username' => $email,
            '_password' => $password,
            '_csrf_token' => $csrfToken,
        ]);

        self::assertResponseRedirects('/');
    }

    private function assertLoginFails($client, string $email, string $password): void
    {
        $client->request('GET', '/login');
        $csrfToken = $client->getCrawler()->filter('input[name="_csrf_token"]')->attr('value');

        $client->request('POST', '/login', [
            '_username' => $email,
            '_password' => $password,
            '_csrf_token' => $csrfToken,
        ]);

        self::assertResponseRedirects('/login');
    }

    private function entityManager(): EntityManagerInterface
    {
        return static::getContainer()->get(EntityManagerInterface::class);
    }

    private function userRepository(): UserRepository
    {
        return $this->entityManager()->getRepository(User::class);
    }

    private function passwordHasher(): UserPasswordHasherInterface
    {
        return static::getContainer()->get(UserPasswordHasherInterface::class);
    }
}
