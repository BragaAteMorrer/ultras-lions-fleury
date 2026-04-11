<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Chant;
use App\Entity\Event;
use App\Entity\Gallery;
use App\Entity\GroupPage;
use App\Entity\Media;
use App\Entity\Merch;
use App\Entity\MerchCategory;
use App\Entity\Ticket;
use App\Entity\TicketCategory;
use App\Entity\Page;
use App\Entity\Post;
use App\Entity\SiteConfig;
use App\Entity\Visit;
use App\Entity\User;
use App\Entity\InviteCode;
use Doctrine\DBAL\Exception as DbalException;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractDashboardController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/admin', name: 'app_admin_dashboard_index')]
    public function index(): Response
    {
        // --- Stats ---
        $stats = [
            'posts'      => $this->safeCount(Post::class),
            'events'     => $this->safeCount(Event::class),
            'galleries'  => $this->safeCount(Gallery::class),
            'media'      => $this->safeCount(Media::class),
            'visits'     => $this->safeCount(Visit::class),
        ];

        // --- Derniers posts ---
        $lastPosts = $this->em->getRepository(Post::class)
            ->findBy([], ['createdAt' => 'DESC'], 5);

        // --- Évolution mensuelle ---
        $conn = $this->em->getConnection();
        $chartData = $conn->executeQuery("
            SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS total
            FROM post
            GROUP BY month
            ORDER BY month ASC
        ")->fetchAllAssociative();

        return $this->render('admin/dashboard.html.twig', [
            'stats'      => $stats,
            'lastPosts'  => $lastPosts,
            'chartData'  => $chartData,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Ultras Lions – Admin');
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addJsFile('assets/js/dashboard.js')
            ->addJsFile('assets/js/easyadmin-editor.js')
            ->addCssFile('styles/dashboard.css');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Contenu du site');
        yield MenuItem::linkToCrud('Articles', 'fa fa-newspaper', Post::class);
        yield MenuItem::linkToCrud('Pages', 'fa fa-file', Page::class);
        yield MenuItem::linkToCrud('Chants', 'fa fa-music', Chant::class);
        yield MenuItem::linkToCrud('Le Groupe', 'fa fa-users', GroupPage::class);
        yield MenuItem::linkToCrud('Photos de match', 'fa fa-camera', Event::class)
            ->setController(PhotoMatchCrudController::class);
        yield MenuItem::linkToCrud('Galeries', 'fa fa-images', Gallery::class);
        yield MenuItem::linkToCrud('Medias', 'fa fa-photo-film', Media::class);

        yield MenuItem::section('Categories & Evenements');
        yield MenuItem::linkToCrud('Categories', 'fa fa-tags', Category::class);
        yield MenuItem::linkToCrud('Evenements', 'fa fa-bus', Event::class);

        yield MenuItem::section('Boutique & Billetterie');
        yield MenuItem::linkToCrud('Merch', 'fa fa-shirt', Merch::class);
        yield MenuItem::linkToCrud('Billetterie', 'fa fa-ticket', Ticket::class);
        yield MenuItem::linkToCrud('Categories Billetterie', 'fa fa-list', TicketCategory::class);
        yield MenuItem::linkToCrud('Categories Merch', 'fa fa-list', MerchCategory::class);

        yield MenuItem::section('Utilisateurs');
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-users', User::class);
        yield MenuItem::linkToCrud('Codes pour accès', 'fa fa-key', InviteCode::class);

        yield MenuItem::section('Configuration');
        yield MenuItem::linkToCrud('Site', 'fa fa-gear', SiteConfig::class);
    }

    private function safeCount(string $entityClass): int
    {
        try {
            return $this->em->getRepository($entityClass)->count([]);
        } catch (DbalException) {
            return 0;
        }
    }
}
