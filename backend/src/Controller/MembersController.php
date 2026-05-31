<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MembersController extends AbstractController
{
    #[Route('/members', name: 'members_list')]
    public function index(UserRepository $repo)
    {
        $members = $repo->findBy([], ['nom' => 'ASC', 'prenom' => 'ASC']);

        return $this->render('members/list.html.twig', [
            'members' => $members
        ]);
    }
}
