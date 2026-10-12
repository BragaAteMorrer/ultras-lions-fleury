<?php

namespace App\Controller\Admin;

use App\Service\AdminDashboardStatisticsService;
use App\Repository\EventRepository;
use App\Repository\PostRepository;
use App\Service\AdminNavigationBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractController
{
    public function __construct(
        private readonly AdminNavigationBuilder $adminNavigationBuilder,
        private readonly AdminDashboardStatisticsService $dashboardStatistics,
        private readonly PostRepository $postRepository,
        private readonly EventRepository $eventRepository,
    ) {
    }

    #[Route('/admin', name: 'app_admin_dashboard_index')]
    public function index(Request $request): Response
    {
        $dashboard = $this->dashboardStatistics->build((string) $request->query->get('period', '30d'));
        $recentPosts = $this->postRepository->findBy([], ['createdAt' => 'DESC'], 4);
        $upcomingEvents = $this->eventRepository->findUpcoming(4);

        return $this->render('admin/dashboard.html.twig', [
            'dashboard' => $dashboard,
            'lastPosts' => $recentPosts,
            'upcomingEvents' => $upcomingEvents,
            'quickActions' => $this->adminNavigationBuilder->quickActions(),
            'adminNavigation' => $this->adminNavigationBuilder->navigation(),
        ]);
    }
}
