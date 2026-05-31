<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/medias')]
class MediaController extends AbstractController
{
    #[Route('/videos', name: 'media_videos')]
    public function videos(): RedirectResponse
    {
        return $this->redirectToRoute('media_category', ['slug' => 'video']);
    }

    #[Route('/communiques', name: 'media_communiques')]
    public function communiques(): RedirectResponse
    {
        return $this->redirectToRoute('media_category', ['slug' => 'communique']);
    }

    #[Route('/street-art', name: 'media_streetart')]
    public function streetart(): RedirectResponse
    {
        return $this->redirectToRoute('media_category', ['slug' => 'streetart']);
    }

    #[Route('/{slug}', name: 'media_category')]
    public function category(string $slug, EventCategoryRepository $eventCategoryRepository, EntityManagerInterface $em): Response
    {
        $category = $eventCategoryRepository->findOneBy([
            'slug' => $slug,
            'section' => 'medias',
        ]);

        if (!$category) {
            throw $this->createNotFoundException();
        }

        $events = $em->getRepository(Event::class)->findBy(
            ['category' => $category],
            ['date' => 'DESC', 'id' => 'DESC']
        );

        return $this->render('events/category.html.twig', [
            'events' => $events,
            'category' => $category,
        ]);
    }
}
