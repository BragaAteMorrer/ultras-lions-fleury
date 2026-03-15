<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/photos-de-match')]
class EventController extends AbstractController
{
    #[Route('/', name: 'gamephoto_index')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $selectedSeason = trim($request->query->getString('season', ''));

        $seasons = $em->createQueryBuilder()
            ->select('DISTINCT e.season AS season')
            ->from(Event::class, 'e')
            ->where('e.season IS NOT NULL')
            ->andWhere("e.season <> ''")
            ->orderBy('e.season', 'DESC')
            ->getQuery()
            ->getSingleColumnResult();

        $deplacementsQb = $em->createQueryBuilder()
            ->select('e')
            ->from(Event::class, 'e')
            ->where('e.category = :category')
            ->setParameter('category', 'photo_match')
            ->addOrderBy('e.season', 'DESC')
            ->addOrderBy('e.journee', 'ASC')
            ->addOrderBy('e.date', 'ASC');

        if ($selectedSeason !== '') {
            $deplacementsQb
                ->andWhere('e.season = :season')
                ->setParameter('season', $selectedSeason);
        }

        $deplacements = $deplacementsQb->getQuery()->getResult();

        $eventsQb = $em->createQueryBuilder()
            ->select('e')
            ->from(Event::class, 'e')
            ->where('e.category = :category')
            ->setParameter('category', 'evenement')
            ->orderBy('e.date', 'ASC');

        if ($selectedSeason !== '') {
            $eventsQb
                ->andWhere('e.season = :season')
                ->setParameter('season', $selectedSeason);
        }

        $events = $eventsQb->getQuery()->getResult();

        return $this->render('events/list.html.twig', [
            'seasons' => $seasons,
            'selectedSeason' => $selectedSeason,
            'deplacements' => $deplacements,
            'events' => $events,
        ]);
    }

    #[Route('/{id}', name: 'event_show')]
    public function show(Event $event): Response
    {
        return $this->render('events/show.html.twig', [
            'event' => $event,
        ]);
    }
}
