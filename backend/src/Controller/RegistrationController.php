<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\InviteCode;
use App\Form\RegistrationFormType;
use App\Repository\InviteCodeRepository;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        InviteCodeRepository $inviteRepo,
        EntityManagerInterface $em,
        MailService $mailService
    ) {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Vérification du code d'invitation
            $code = $form->get('inviteCode')->getData();
            $invite = $inviteRepo->findOneBy([
                'code' => $code,
                'used' => false
            ]);

            if (!$invite || $invite->getExpiresAt() < new \DateTimeImmutable()) {
                $this->addFlash('danger', 'Code invalide ou expiré !');
                return $this->redirectToRoute('app_register');
            }

            // Upload photo de profil (optionnel)
            $file = $form->get('photoProfil')->getData();
            if ($file) {
                $filename = uniqid('', true) . '.' . $file->guessExtension();
                $file->move('uploads/profils', $filename);
                $user->setPhotoProfil($filename);
            }

            // Hash du mot de passe
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );
            $user->setPassword($hashedPassword);

            // Rôle par défaut
            $user->setRoles(['ROLE_USER']);

            // Code d'invitation marqué comme utilisé
            $invite->setUsed(true);

            // Sauvegarde utilisateur
            $em->persist($user);
            $em->flush();

            // 📩 Envoi du mail de bienvenue (utilisateur PAS connecté)
            try {
                $mailService->send(
                    to: $user->getEmail(),
                    subject: '🔥 Bienvenue chez les Ultras Lions',
                    template: 'email/welcome.html.twig',
                    context: [
                        'user' => $user
                    ]
                );
            } catch (\Throwable $e) {
                // On ne bloque PAS l'inscription si le mail échoue
                // (log possible ici si tu veux)
            }

            $this->addFlash('success', 'Bienvenue dans les Ultras Lions !');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
