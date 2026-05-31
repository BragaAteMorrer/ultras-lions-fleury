<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\InviteCode;
use App\Form\RegistrationFormType;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security; // ✅ LE BON USE
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/register/{code}', name: 'app_register_with_code')]
    public function register(
        string $code,
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        Security $security   // ✅ Correction ici
    ): Response {

        $invite = $em->getRepository(InviteCode::class)->findOneBy(['code' => $code]);

        if (!$invite || $invite->isUsed() || $invite->getExpiresAt() < new \DateTime()) {
            $this->addFlash('error', '❌ Code d’invitation invalide ou expiré.');
            return $this->redirectToRoute('invitation_check');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->remove('inviteCode'); // le champ n’est plus utile
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Photo
            $photo = $form->get('photoProfil')->getData();
            if ($photo) {
                $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/profils';
                (new Filesystem())->mkdir($uploadsDir);

                $filename = uniqid('pf_') . '.' . $photo->guessExtension();
                $photo->move($uploadsDir, $filename);
                $user->setPhotoProfil($filename);
            }

            // Password
            $user->setPassword(
                $passwordHasher->hashPassword($user, $form->get('plainPassword')->getData())
            );

            $user->setRoles(['ROLE_USER']);

            // Marquer le code comme utilisé
            $invite->setUsed(true);
            $em->persist($invite);

            // Sauvegarde
            $em->persist($user);
            $em->flush();

            // Login automatique
            return $security->login($user);
        }

        return $this->render('security/register.html.twig', [
            'form' => $form,
            'invite' => $invite,
        ]);
    }
}
