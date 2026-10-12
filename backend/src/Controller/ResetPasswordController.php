<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ResetPasswordController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('/forgot-password', name: 'forgot_password', methods: ['GET', 'POST'])]
    public function forgot(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        MailService $mailService,
        LoggerInterface $logger
    ): Response {
        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('forgot_password', (string) $request->request->get('_token', ''))) {
                throw $this->createAccessDeniedException($this->translator->trans('reset_password.flash.invalid_csrf'));
            }

            $email = strtolower(trim((string) $request->request->get('email', '')));
            $user = $email !== '' ? $userRepository->findOneByEmail($email) : null;

            if ($user instanceof User) {
                $plainToken = bin2hex(random_bytes(32));
                $user->setResetPasswordToken(hash('sha256', $plainToken));
                $user->setResetPasswordTokenExpiresAt(new \DateTimeImmutable('+1 hour'));
                $em->flush();

                try {
                    $mailService->send(
                        to: (string) $user->getEmail(),
                        subject: $this->translator->trans('reset_password.email.subject'),
                        template: 'email/reset_password.html.twig',
                        context: [
                            'user' => $user,
                            'resetUrl' => $this->generateUrl(
                                'reset_password',
                                ['token' => $plainToken],
                                UrlGeneratorInterface::ABSOLUTE_URL
                            ),
                        ],
                    );
                } catch (\Throwable $exception) {
                    $logger->error('Reset password email could not be sent.', [
                        'user_id' => $user->getId(),
                        'exception' => $exception::class,
                    ]);
                }
            }

            $this->addFlash('success', $this->translator->trans('reset_password.flash.link_sent_if_exists'));

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/forgot_password.html.twig');
    }

    #[Route('/reset-password/{token}', name: 'reset_password', methods: ['GET', 'POST'])]
    public function reset(
        string $token,
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): Response {
        $user = $userRepository->findOneByResetPasswordToken($token);

        if ($user === null || !$user->isResetPasswordTokenValid($token)) {
            $this->addFlash('danger', $this->translator->trans('reset_password.flash.link_invalid_or_expired'));

            return $this->redirectToRoute('forgot_password');
        }

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('reset_password', (string) $request->request->get('_token', ''))) {
                throw $this->createAccessDeniedException($this->translator->trans('reset_password.flash.invalid_csrf'));
            }

            $password = (string) $request->request->get('password', '');
            $confirmPassword = (string) $request->request->get('confirm_password', '');

            if (strlen($password) < 8) {
                $this->addFlash('danger', $this->translator->trans('reset_password.flash.password_min_length'));

                return $this->redirectToRoute('reset_password', ['token' => $token]);
            }

            if (!preg_match('/[A-Z]/', $password)) {
                $this->addFlash('danger', $this->translator->trans('reset_password.flash.password_uppercase'));

                return $this->redirectToRoute('reset_password', ['token' => $token]);
            }

            if (!preg_match('/[a-z]/', $password)) {
                $this->addFlash('danger', $this->translator->trans('reset_password.flash.password_lowercase'));

                return $this->redirectToRoute('reset_password', ['token' => $token]);
            }

            if (!preg_match('/[0-9]/', $password)) {
                $this->addFlash('danger', $this->translator->trans('reset_password.flash.password_number'));

                return $this->redirectToRoute('reset_password', ['token' => $token]);
            }

            if (!preg_match('/[\W]/', $password)) {
                $this->addFlash('danger', $this->translator->trans('reset_password.flash.password_special'));

                return $this->redirectToRoute('reset_password', ['token' => $token]);
            }

            if ($password !== $confirmPassword) {
                $this->addFlash('danger', $this->translator->trans('reset_password.flash.passwords_mismatch'));

                return $this->redirectToRoute('reset_password', ['token' => $token]);
            }

            $user->setPassword($passwordHasher->hashPassword($user, $password));
            $user->setResetPasswordToken(null);
            $user->setResetPasswordTokenExpiresAt(null);
            $em->flush();

            $this->addFlash('success', $this->translator->trans('reset_password.flash.updated'));

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', [
            'token' => $token,
        ]);
    }
}
