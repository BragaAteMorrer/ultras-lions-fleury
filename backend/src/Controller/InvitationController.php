<?php

namespace App\Controller;

use App\Entity\InviteCode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvitationController extends AbstractController
{
    #[Route('/invitation', name: 'invitation_check')]
    public function invitation(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $codeValue = $request->request->get('inviteCode');

            $invite = $em->getRepository(InviteCode::class)->findOneBy(['code' => $codeValue]);

            if (!$invite) {
                $this->addFlash('error', '❌ Code invalide');
            } elseif ($invite->isUsed()) {
                $this->addFlash('error', '❌ Ce code a déjà été utilisé');
            } elseif ($invite->getExpiresAt() < new \DateTime()) {
                $this->addFlash('error', '❌ Ce code est expiré');
            } else {
                // ✔ Code valide → On redirige vers l'inscription
                return $this->redirectToRoute('app_register_with_code', [
                    'code' => $invite->getCode()
                ]);
            }
        }

        return $this->render('security/invitation.html.twig');
    }
}
