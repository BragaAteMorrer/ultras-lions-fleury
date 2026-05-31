<?php

namespace App\Controller;

use App\Repository\GroupPageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/groupe')]
class GroupController extends AbstractController
{
    #[Route('/', name: 'group_index')]
    public function index(GroupPageRepository $groupPageRepository): Response
    {
        $group = $groupPageRepository->findOneBy([]);

        return $this->render('group/index.html.twig', [
            'group' => $group,
        ]);
    }
}
