<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TrixUploadController extends AbstractController
{
    #[Route('/admin/trix-upload', name: 'admin_trix_upload', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function upload(Request $request, EntityManagerInterface $em): Response
    {
        $file = $request->files->get('file');

        if (!$file instanceof UploadedFile) {
            return new JsonResponse(['error' => 'Fichier manquant.'], Response::HTTP_BAD_REQUEST);
        }

        $media = new Media();
        $media->setImageFile($file);
        $media->setAlt($file->getClientOriginalName());

        $em->persist($media);
        $em->flush();

        if (!$media->getPath()) {
            return new JsonResponse(['error' => 'Upload impossible.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse([
            'url' => '/uploads/media/'.$media->getPath(),
        ]);
    }
}
