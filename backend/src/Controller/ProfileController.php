<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profil/{id}', name: 'profile_show')]
    public function show(User $user): Response
    {
        return $this->render('profile/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profil/{id}/edit', name: 'profile_edit')]
    public function edit(Request $request, User $user, EntityManagerInterface $em): Response
    {
        if ($this->getUser() !== $user) {
            throw $this->createAccessDeniedException("Tu ne peux modifier que ton propre compte.");
        }

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile|null $photoFile */
            $photoFile = $form->get('photoProfil')->getData();

            $fs = new Filesystem();

            if ($photoFile) {
                $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/profils';

                if (!$fs->exists($uploadsDir)) {
                    $fs->mkdir($uploadsDir, 0775);
                }

                $newFilename = uniqid('pf_') . '.' . $photoFile->guessExtension();
                $photoFile->move($uploadsDir, $newFilename);

                $user->setPhotoProfil($newFilename);
            }

            $em->flush();

            $this->addFlash('success', 'Profil mis à jour !');

            return $this->redirectToRoute('profile_show', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }
}