<?php

namespace App\Controller;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/medias')]
class MediaController extends AbstractController
{
    #[Route('/videos', name: 'media_videos')]
    public function videos(EntityManagerInterface $em): Response
    {
        return $this->renderCategory($em, 'video', 'components_navbar.html.videos');
    }

    #[Route('/communiques', name: 'media_communiques')]
    public function communiques(EntityManagerInterface $em): Response
    {
        return $this->renderCategory($em, 'communique', 'components_navbar.html.communiques');
    }

    #[Route('/street-art', name: 'media_streetart')]
    public function streetart(EntityManagerInterface $em): Response
    {
        return $this->renderCategory($em, 'streetart', 'components_navbar.html.streetart');
    }

    private function renderCategory(EntityManagerInterface $em, string $category, string $titleKey): Response
    {
        $events = $em->getRepository(Event::class)->findBy(
            ['category' => $category],
            ['date' => 'DESC', 'id' => 'DESC']
        );

        return $this->render('events/category.html.twig', [
            'events' => $events,
            'titleKey' => $titleKey,
        ]);
    }
}
