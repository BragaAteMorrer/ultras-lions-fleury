<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LinktreeController extends AbstractController
{
    #[Route('/liens', name: 'linktree_index', methods: ['GET'])]
    #[Route('/linktree', name: 'linktree_legacy', methods: ['GET'])]
    public function index(): Response
    {
        $links = [
            [
                'title' => 'Actualités',
                'description' => 'Les derniers communiqués et infos du groupe',
                'url' => $this->generateUrl('post_index'),
                'icon' => 'newspaper',
                'featured' => true,
                'openInNewTab' => false,
            ],
            [
                'title' => 'Table de vente',
                'description' => 'Produits, textiles, stickers et accessoires',
                'url' => $this->generateUrl('merch_index'),
                'icon' => 'bag',
                'featured' => true,
                'openInNewTab' => false,
            ],
            [
                'title' => 'Billetterie',
                'description' => 'Réservations et matchs disponibles',
                'url' => $this->generateUrl('ticket_index'),
                'icon' => 'ticket-perforated',
                'featured' => true,
                'openInNewTab' => false,
            ],
            [
                'title' => 'Photos de match',
                'description' => 'Les albums des déplacements et des tribunes',
                'url' => $this->generateUrl('gamephoto_index'),
                'icon' => 'camera',
                'featured' => false,
                'openInNewTab' => false,
            ],
            [
                'title' => 'Médias',
                'description' => 'Photos, vidéos et communiqués',
                'url' => $this->generateUrl('media_index'),
                'icon' => 'play-btn',
                'featured' => false,
                'openInNewTab' => false,
            ],
            [
                'title' => 'Le groupe',
                'description' => 'Histoire, mentalité et fonctionnement',
                'url' => $this->generateUrl('group_index'),
                'icon' => 'people',
                'featured' => false,
                'openInNewTab' => false,
            ],
            [
                'title' => 'Cartage',
                'description' => 'Accès membre et règlement intérieur',
                'url' => $this->generateUrl('cartage_form'),
                'icon' => 'person-badge',
                'featured' => false,
                'openInNewTab' => false,
            ],
        ];

        if ($this->isGranted('ROLE_ADMIN')) {
            $links[] = [
                'title' => 'Espace admin',
                'description' => 'Réservé au bureau et aux administrateurs',
                'url' => $this->generateUrl('app_admin_dashboard_index'),
                'icon' => 'gear',
                'featured' => false,
                'openInNewTab' => false,
            ];
        }

        return $this->render('linktree/index.html.twig', [
            'links' => $links,
        ]);
    }
}
