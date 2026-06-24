<?php

namespace App\Controller\Admin;

use App\Entity\Chant;
use App\Entity\BilletwebLead;
use App\Entity\CartageQrToken;
use App\Entity\CartageRegistration;
use App\Entity\Event;
use App\Entity\EventCategory;
use App\Entity\Gadget;
use App\Entity\Gallery;
use App\Entity\GroupPage;
use App\Entity\Media;
use App\Entity\Merch;
use App\Entity\MerchCategory;
use App\Entity\MerchOrder;
use App\Entity\MerchStock;
use App\Entity\PaymentCheckout;
use App\Entity\Ticket;
use App\Entity\TicketCategory;
use App\Entity\TicketOrder;
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
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractDashboardController
{
    private EntityManagerInterface $em;
    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(EntityManagerInterface $em, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->em = $em;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    #[Route('/admin', name: 'app_admin_dashboard_index')]
    public function index(): Response
    {
        // --- Stats ---
        $postRepository = $this->em->getRepository(Post::class);
        $recentPosts = $postRepository->findBy([], ['createdAt' => 'DESC'], 5);
        $allPosts = $postRepository->findBy([], ['createdAt' => 'ASC']);

        $chartBuckets = [];
        foreach ($allPosts as $post) {
            if (!$post instanceof Post || !$post->getCreatedAt()) {
                continue;
            }

            $monthKey = $post->getCreatedAt()->format('Y-m');
            $chartBuckets[$monthKey] = ($chartBuckets[$monthKey] ?? 0) + 1;
        }

        ksort($chartBuckets);

        $chartData = [];
        $chartMax = 0;
        foreach ($chartBuckets as $monthKey => $total) {
            [$year, $month] = explode('-', $monthKey);
            $chartData[] = [
                'month' => sprintf('%s/%s', $month, $year),
                'total' => $total,
            ];
            $chartMax = max($chartMax, $total);
        }

        $stats = [
            'posts'      => $this->safeCount(Post::class),
            'events'     => $this->safeCount(Event::class),
            'galleries'  => $this->safeCount(Gallery::class),
            'media'      => $this->safeCount(Media::class),
            'visits'     => $this->safeCount(Visit::class),
        ];

        $quickActions = [
            [
                'label' => 'Articles',
                'icon' => 'fa fa-newspaper',
                'url' => (clone $this->adminUrlGenerator)->setController(PostCrudController::class)->setAction('index')->generateUrl(),
                'description' => 'Publier et modifier les actualités.',
            ],
            [
                'label' => 'Médias',
                'icon' => 'fa fa-photo-film',
                'url' => (clone $this->adminUrlGenerator)->setController(MediaCrudController::class)->setAction('index')->generateUrl(),
                'description' => 'Photos, vidéos et fichiers partagés.',
            ],
            [
                'label' => 'Merch',
                'icon' => 'fa fa-shirt',
                'url' => (clone $this->adminUrlGenerator)->setController(MerchCrudController::class)->setAction('index')->generateUrl(),
                'description' => 'Produits, gadgets et stocks.',
            ],
            [
                'label' => 'Billetterie',
                'icon' => 'fa fa-ticket',
                'url' => (clone $this->adminUrlGenerator)->setController(TicketCrudController::class)->setAction('index')->generateUrl(),
                'description' => 'Matchs, tarifs et disponibilités.',
            ],
            [
                'label' => 'Cartage',
                'icon' => 'fa fa-id-card',
                'url' => (clone $this->adminUrlGenerator)->setController(CartageRequestCrudController::class)->setAction('index')->generateUrl(),
                'description' => 'Demandes non payees et relances.',
            ],
            [
                'label' => 'Site',
                'icon' => 'fa fa-gear',
                'url' => (clone $this->adminUrlGenerator)->setController(SiteConfigCrudController::class)->setAction('index')->generateUrl(),
                'description' => 'Logo, fond et réglages globaux.',
            ],
        ];

        return $this->render('admin/dashboard.html.twig', [
            'stats'      => $stats,
            'lastPosts'  => $recentPosts,
            'chartData'  => $chartData,
            'chartMax'   => $chartMax,
            'quickActions' => $quickActions,
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
            ->addCssFile('styles/navbar.css')
            ->addCssFile('styles/easyadmin-home.css')
            ->addCssFile('styles/dashboard.css')
            ->addJsFile('js/app.js')
            ->addJsFile('js/dashboard.js')
            ->addJsFile('js/easyadmin-editor.js')
            ->addJsFile('js/merch-stocks.js');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToRoute('Retour au site', 'fa fa-arrow-left', 'home');

        yield MenuItem::section('Gestion');

        yield MenuItem::subMenu('Contenu', 'fa fa-layer-group')->setSubItems([
            MenuItem::linkToCrud('Articles', 'fa fa-newspaper', Post::class),
            MenuItem::linkToCrud('Pages', 'fa fa-file', Page::class),
            MenuItem::linkToCrud('Chants', 'fa fa-music', Chant::class),
            MenuItem::linkToCrud('Le groupe', 'fa fa-users', GroupPage::class),
        ]);

        yield MenuItem::subMenu('Medias & evenements', 'fa fa-photo-film')->setSubItems([
            MenuItem::linkToCrud('Photos de match', 'fa fa-camera', Event::class)
                ->setController(PhotoMatchCrudController::class),
            MenuItem::linkToCrud('Galeries', 'fa fa-images', Gallery::class),
            MenuItem::linkToCrud('Medias', 'fa fa-photo-film', Media::class),
            MenuItem::linkToCrud('Evenements', 'fa fa-bus', Event::class),
            MenuItem::linkToCrud('Categories', 'fa fa-tags', EventCategory::class),
        ]);

        yield MenuItem::subMenu('Boutique', 'fa fa-shirt')->setSubItems([
            MenuItem::linkToCrud('Merch', 'fa fa-shirt', Merch::class),
            MenuItem::linkToCrud('Gadgets', 'fa fa-icons', Gadget::class),
            MenuItem::linkToCrud('Stocks merch', 'fa fa-boxes-stacked', MerchStock::class)
                ->setController(MerchStockCrudController::class),
            MenuItem::linkToCrud('Categories merch', 'fa fa-list', MerchCategory::class),
            MenuItem::linkToCrud('Commandes merch', 'fa fa-receipt', MerchOrder::class)
                ->setController(MerchOrderCrudController::class),
        ]);

        yield MenuItem::subMenu('Billetterie', 'fa fa-ticket')->setSubItems([
            MenuItem::linkToCrud('Billetterie', 'fa fa-ticket', Ticket::class),
            MenuItem::linkToCrud('Categories billetterie', 'fa fa-list', TicketCategory::class),
            MenuItem::linkToCrud('Commandes billets', 'fa fa-receipt', TicketOrder::class)
                ->setController(TicketOrderCrudController::class),
            MenuItem::linkToCrud('Pre-inscriptions Billetweb', 'fa fa-list-check', BilletwebLead::class)
                ->setController(BilletwebLeadCrudController::class),
        ]);

        yield MenuItem::subMenu('Cartage', 'fa fa-id-card')->setSubItems([
            MenuItem::linkToCrud('Demandes de cartage', 'fa fa-hourglass-half', CartageRegistration::class)
                ->setController(CartageRequestCrudController::class),
            MenuItem::linkToCrud('Relances paiement', 'fa fa-envelope', CartageRegistration::class)
                ->setController(CartageReminderCrudController::class),
            MenuItem::linkToCrud('Cartages', 'fa fa-id-card', CartageRegistration::class)
                ->setController(CartageRegistrationCrudController::class),
            MenuItem::linkToCrud('QR codes cartage', 'fa fa-qrcode', CartageQrToken::class)
                ->setController(CartageQrTokenCrudController::class),
        ]);

        yield MenuItem::subMenu('Paiements', 'fa fa-credit-card')->setSubItems([
            MenuItem::linkToCrud('Paiements SumUp', 'fa fa-credit-card', PaymentCheckout::class)
                ->setController(PaymentCheckoutCrudController::class),
        ]);

        yield MenuItem::subMenu('Utilisateurs', 'fa fa-users')->setSubItems([
            MenuItem::linkToCrud('Utilisateurs', 'fa fa-users', User::class),
            MenuItem::linkToRoute('Envoyer un mail', 'fa fa-envelope', 'app_admin_mailing'),
            MenuItem::linkToCrud('Codes pour acces', 'fa fa-key', InviteCode::class),
        ]);

        yield MenuItem::subMenu('Configuration', 'fa fa-gear')->setSubItems([
            MenuItem::linkToCrud('Site', 'fa fa-gear', SiteConfig::class),
        ]);
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
