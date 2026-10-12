<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class StaffController extends AbstractController
{
    #[Route('/staff', name: 'staff_list')]
    public function staff(UserRepository $repo)
    {
        $admins = $repo->findByRole('ROLE_ADMIN');

        return $this->render('staff/index.html.twig', [
            'staff' => $admins
        ]);
    }
}
