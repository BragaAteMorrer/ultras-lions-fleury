<?php

namespace App\Controller;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        EntityManagerInterface $em
    ): Response {

        // 1. Dernieres actualites (posts + evenements + photos de match)
        $postRepo = $em->getRepository('App\\Entity\\Post');
        $posts = $postRepo->findPublicPosts(6);

        $events = $em->createQueryBuilder()
            ->select('e', 'img', 'cat')
            ->from(Event::class, 'e')
            ->leftJoin('e.image', 'img')
            ->leftJoin('e.category', 'cat')
            ->where('cat.section IN (:sections)')
            ->setParameter('sections', ['photos_de_match', 'evenements'])
            ->orderBy('e.date', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();

        $latestItems = [];
        foreach ($posts as $post) {
            $latestItems[] = [
                'type' => 'post',
                'date' => $post->getCreatedAt(),
                'item' => $post,
            ];
        }
        foreach ($events as $event) {
            $latestItems[] = [
                'type' => 'event',
                'date' => $event->getDate(),
                'item' => $event,
            ];
        }

        usort($latestItems, static function (array $a, array $b) {
            $dateA = $a['date'] instanceof \DateTimeInterface ? $a['date']->getTimestamp() : 0;
            $dateB = $b['date'] instanceof \DateTimeInterface ? $b['date']->getTimestamp() : 0;
            return $dateB <=> $dateA;
        });

        $latestItems = array_slice($latestItems, 0, 6);

        // 2. Billetterie (3 prochains matchs visibles)
        $ticketRepo = $em->getRepository('App\\Entity\\Ticket');
        $isMember = $this->getUser() !== null;
        $tickets = $ticketRepo->findPublicTickets(null, $isMember);
        $tickets = array_slice($tickets, 0, 3);

        // 3. Galeries
        $galleries = $em->createQueryBuilder()
            ->select('g', 'm')
            ->from('App\\Entity\\Gallery', 'g')
            ->leftJoin('g.media', 'm')
            ->orderBy('g.id', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();

        // 4. Merch
        $merch = $em->createQueryBuilder()
            ->select('m', 'img')
            ->from('App\\Entity\\Merch', 'm')
            ->leftJoin('m.image', 'img')
            ->orderBy('m.id', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();

        // 5. Groupe
        $group = $em->createQueryBuilder()
            ->select('g', 'logo', 'banner')
            ->from('App\\Entity\\GroupPage', 'g')
            ->leftJoin('g.logo', 'logo')
            ->leftJoin('g.banner', 'banner')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $this->render('home/index.html.twig', [
            'latest_items' => $latestItems,
            'tickets' => $tickets,
            'galleries' => $galleries,
            'merch' => $merch,
            'group' => $group,
        ]);
    }
}
