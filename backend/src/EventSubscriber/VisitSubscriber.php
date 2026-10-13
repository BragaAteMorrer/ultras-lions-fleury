<?php

namespace App\EventSubscriber;

use App\Entity\Visit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class VisitSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        #[Autowire('%env(bool:VISIT_TRACKING)%')] private bool $visitTracking,
    ) {}

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest() || !$this->visitTracking) {
            return;
        }

        $request = $event->getRequest();
        $path = $request->getPathInfo();

        // ignore admin
        if ($this->shouldSkipPath($path)) {
            return;
        }

        $request->attributes->set('_track_visit', [
            'visitedAt' => new \DateTimeImmutable(),
            'page' => $path,
            'ip' => $request->getClientIp() ?? 'unknown',
        ]);
    }

    public function onKernelTerminate(TerminateEvent $event): void
    {
        if (!$event->isMainRequest() || !$this->visitTracking) {
            return;
        }

        $data = $event->getRequest()->attributes->get('_track_visit');
        if (!is_array($data)) {
            return;
        }

        $visit = new Visit();
        $visit->setVisitedAt($data['visitedAt']);
        $visit->setPage($data['page']);
        $visit->setIp($data['ip']);

        $this->em->persist($visit);
        $this->em->flush();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::TERMINATE => 'onKernelTerminate',
        ];
    }

    private function shouldSkipPath(string $path): bool
    {
        if (str_starts_with($path, '/admin')) {
            return true;
        }

        if (str_starts_with($path, '/_wdt') || str_starts_with($path, '/_profiler')) {
            return true;
        }

        if (str_starts_with($path, '/assets')
            || str_starts_with($path, '/build')
            || str_starts_with($path, '/uploads')
            || str_starts_with($path, '/img')
            || $path === '/favicon.ico'
        ) {
            return true;
        }

        return false;
    }
}
