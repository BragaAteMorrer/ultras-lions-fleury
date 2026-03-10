<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Repository\GalleryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/galeries')]
class GalleryController extends AbstractController
{
    #[Route('/', name: 'gallery_index')]
    public function index(GalleryRepository $galleryRepository): Response
    {
        $galleries = $galleryRepository->findBy([], ['id' => 'DESC']);

        return $this->render('galleries/list.html.twig', [
            'galleries' => $galleries,
        ]);
    }

    #[Route('/{id}', name: 'gallery_show')]
    public function show(Gallery $gallery): Response
    {
        return $this->render('galleries/show.html.twig', [
            'gallery' => $gallery,
        ]);
    }
}
