<?php

namespace App\Controller;

use App\Repository\ChantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChantController extends AbstractController
{
    #[Route('/chants', name: 'chant_index')]
    public function index(ChantRepository $chantRepository): Response
    {
        $chants = $chantRepository->createQueryBuilder('c')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('chants/list.html.twig', [
            'chants' => $chants,
        ]);
    }
}
