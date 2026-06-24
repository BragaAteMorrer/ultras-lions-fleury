<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{
    #[Route('/forgot-password', name: 'forgot_password', methods: ['GET', 'POST'])]
    public function forgot(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        MailService $mailService
    ): Response {
        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('forgot_password', (string) $request->request->get('_token', ''))) {
                throw $this->createAccessDeniedException('Token CSRF invalide.');
            }

            $email = strtolower(trim((string) $request->request->get('email', '')));
            $user = $email !== '' ? $userRepository->findOneBy(['email' => $email]) : null;

            if ($user !== null) {
                $token = bin2hex(random_bytes(32));
                $user->setResetPasswordToken($token);
                $user->setResetPasswordTokenExpiresAt(new \DateTimeImmutable('+1 hour'));
                $em->flush();

                try {
                    $mailService->send(
                        to: $user->getUserIdentifier(),
                        subject: 'Reinitialisation de ton mot de passe',
                        template: 'email/reset_password.html.twig',
                        context: [
                            'user' => $user,
                            'resetUrl' => $this->generateUrl('reset_password', ['token' => $token], 0),
                        ],
                    );
                } catch (\Throwable) {
                    $this->addFlash('warning', 'Le lien a ete cree, mais l\'email n\'a pas pu etre envoye. Verifie la configuration SMTP.');
                    return $this->redirectToRoute('forgot_password');
                }
            }

            $this->addFlash('success', 'Si cette adresse existe, un lien de reinitialisation vient d\'etre envoye.');
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
        $user = $userRepository->findOneBy(['resetPasswordToken' => $token]);

        if ($user === null || !$user->isResetPasswordTokenValid($token)) {
            $this->addFlash('danger', 'Ce lien de reinitialisation est invalide ou expire.');
            return $this->redirectToRoute('forgot_password');
        }

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('reset_password', (string) $request->request->get('_token', ''))) {
                throw $this->createAccessDeniedException('Token CSRF invalide.');
            }

            $password = (string) $request->request->get('password', '');

            if (strlen($password) < 8) {
                $this->addFlash('danger', 'Le mot de passe doit contenir au moins 8 caracteres.');
                return $this->redirectToRoute('reset_password', ['token' => $token]);
            }

            $user->setPassword($passwordHasher->hashPassword($user, $password));
            $user->setResetPasswordToken(null);
            $user->setResetPasswordTokenExpiresAt(null);
            $em->flush();

            $this->addFlash('success', 'Ton mot de passe a ete mis a jour. Tu peux te connecter.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig');
    }
}
