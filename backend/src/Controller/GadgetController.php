<?php

namespace App\Controller;

use App\Repository\GadgetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gadgets')]
class GadgetController extends AbstractController
{
    #[Route('', name: 'gadget_index')]
    public function index(GadgetRepository $gadgetRepository): Response
    {
        return $this->render('gadgets/index.html.twig', [
            'gadgets' => $gadgetRepository->findVisible(),
        ]);
    }
}
