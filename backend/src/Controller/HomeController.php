<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\EventRepository;
use App\Repository\GalleryRepository;
use App\Repository\MerchRepository;
use App\Repository\GroupPageRepository;
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

        // 🔥 1. Récupérer les 3 derniers posts avec FETCH JOIN
        $lastPosts = $em->createQueryBuilder()
            ->select('p', 'img', 'cat')
            ->from('App\Entity\Post', 'p')
            ->leftJoin('p.image', 'img')
            ->leftJoin('p.category', 'cat')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();

        // 🔥 2. Prochain événement (juste un)
        $nextEvent = $em->createQueryBuilder()
            ->select('e', 'img')
            ->from('App\Entity\Event', 'e')
            ->leftJoin('e.image', 'img')
            ->where('e.category = :category')
            ->setParameter('category', 'photo_match')
            ->addOrderBy('e.season', 'DESC')
            ->addOrderBy('e.journee', 'ASC')
            ->addOrderBy('e.date', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        // 🔥 3. Galeries (avec image de preview)
        $galleries = $em->createQueryBuilder()
            ->select('g', 'm')
            ->from('App\Entity\Gallery', 'g')
            ->leftJoin('g.media', 'm')
            ->orderBy('g.id', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();

        // 🔥 4. Merch (3 derniers articles)
        $merch = $em->createQueryBuilder()
            ->select('m', 'img')
            ->from('App\Entity\Merch', 'm')
            ->leftJoin('m.image', 'img')
            ->orderBy('m.id', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();

        // 🔥 5. Groupe (une seule entité)
        $group = $em->createQueryBuilder()
            ->select('g', 'logo', 'banner')
            ->from('App\Entity\GroupPage', 'g')
            ->leftJoin('g.logo', 'logo')
            ->leftJoin('g.banner', 'banner')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $this->render('home/index.html.twig', [
            'last_posts'  => $lastPosts,
            'event'       => $nextEvent,
            'galleries'   => $galleries,
            'merch'       => $merch,
            'group'       => $group,
        ]);
    }
}
