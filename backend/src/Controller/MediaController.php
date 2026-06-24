<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventCategoryRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/medias')]
class MediaController extends AbstractController
{
    #[Route('', name: 'media_index')]
    public function index(EntityManagerInterface $em, PostRepository $postRepository): Response
    {
        $items = [];

        $photoEvents = $em->getRepository(Event::class)->createQueryBuilder('e')
            ->leftJoin('e.category', 'cat')
            ->addSelect('cat')
            ->leftJoin('e.image', 'image')
            ->addSelect('image')
            ->leftJoin('e.media', 'media')
            ->addSelect('media')
            ->where('cat.section = :photoSection')
            ->setParameter('photoSection', 'photos_de_match')
            ->orderBy('e.date', 'DESC')
            ->addOrderBy('e.id', 'DESC')
            ->setMaxResults(30)
            ->getQuery()
            ->getResult();

        foreach ($photoEvents as $event) {
            $items[] = [
                'type' => 'Photo de match',
                'title' => $event->getTitle(),
                'url' => $this->generateUrl('event_show', ['id' => $event->getId()]),
                'date' => $event->getDate(),
                'cover' => $this->getEventCover($event),
                'description' => $event->getDescription(),
            ];
        }

        $communiques = $postRepository->createQueryBuilder('p')
            ->leftJoin('p.category', 'cat')
            ->addSelect('cat')
            ->leftJoin('p.image', 'image')
            ->addSelect('image')
            ->where('cat.slug IN (:slugs)')
            ->andWhere('p.createdAt <= :now')
            ->setParameter('slugs', ['communique', 'communiques'])
            ->setParameter('now', new \DateTime())
            ->orderBy('p.createdAt', 'DESC')
            ->addOrderBy('p.id', 'DESC')
            ->setMaxResults(30)
            ->getQuery()
            ->getResult();

        foreach ($communiques as $post) {
            $items[] = [
                'type' => 'Communique',
                'title' => $post->getTitle(),
                'url' => $this->generateUrl('post_show', ['slug' => $post->getSlug()]),
                'date' => $post->getCreatedAt(),
                'cover' => $post->getImage(),
                'description' => $post->getContent(),
            ];
        }

        $videoEvents = $em->getRepository(Event::class)->createQueryBuilder('e')
            ->leftJoin('e.category', 'cat')
            ->addSelect('cat')
            ->leftJoin('e.image', 'image')
            ->addSelect('image')
            ->leftJoin('e.media', 'media')
            ->addSelect('media')
            ->where('cat.section = :mediaSection')
            ->andWhere('(cat.slug IN (:videoSlugs) OR LOWER(cat.name) LIKE :videoName)')
            ->setParameter('mediaSection', 'medias')
            ->setParameter('videoSlugs', ['video', 'videos'])
            ->setParameter('videoName', '%video%')
            ->orderBy('e.date', 'DESC')
            ->addOrderBy('e.id', 'DESC')
            ->setMaxResults(30)
            ->getQuery()
            ->getResult();

        foreach ($videoEvents as $event) {
            $items[] = [
                'type' => 'Video',
                'title' => $event->getTitle(),
                'url' => $this->generateUrl('event_show', ['id' => $event->getId()]),
                'date' => $event->getDate(),
                'cover' => $this->getEventCover($event),
                'description' => $event->getDescription(),
            ];
        }

        usort($items, static function (array $first, array $second): int {
            $firstTime = $first['date'] instanceof \DateTimeInterface ? $first['date']->getTimestamp() : 0;
            $secondTime = $second['date'] instanceof \DateTimeInterface ? $second['date']->getTimestamp() : 0;

            return $secondTime <=> $firstTime;
        });

        return $this->render('media/index.html.twig', [
            'items' => array_slice($items, 0, 60),
        ]);
    }

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

    private function getEventCover(Event $event): mixed
    {
        if ($event->getImage()) {
            return $event->getImage();
        }

        if (!$event->getMedia()->isEmpty()) {
            $media = $event->getMedia()->first();

            return $media ?: null;
        }

        return null;
    }
}
