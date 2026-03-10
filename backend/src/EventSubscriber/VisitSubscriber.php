<?php

namespace App\EventSubscriber;

use App\Entity\Visit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class VisitSubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) return;

        $request = $event->getRequest();

        // ignore admin
        if (str_starts_with($request->getPathInfo(), '/admin')) {
            return;
        }

        $visit = new Visit();
        $visit->setVisitedAt(new \DateTimeImmutable());
        $visit->setPage($request->getPathInfo());
        $visit->setIp($request->getClientIp() ?? 'unknown');

        $this->em->persist($visit);
        $this->em->flush();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
