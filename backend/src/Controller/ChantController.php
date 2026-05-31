<?php

namespace App\Controller;

use App\Repository\ChantRepository;
use App\Repository\SiteConfigRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChantController extends AbstractController
{
    #[Route('/chants', name: 'chant_index')]
    public function index(ChantRepository $chantRepository, SiteConfigRepository $siteConfigRepository): Response
    {
        $chants = $chantRepository->createQueryBuilder('c')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();

        $config = $siteConfigRepository->findOneBy([], ['id' => 'ASC']);

        return $this->render('chants/list.html.twig', [
            'chants' => $chants,
            'chantsSubtitle' => $config?->getChantsSubtitle(),
        ]);
    }
}
