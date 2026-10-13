<?php

namespace App\Twig;

use App\Repository\EventRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class EventSeasonExtension extends AbstractExtension implements GlobalsInterface
{
    private EventRepository $eventRepository;
    private bool $loaded = false;
    /** @var string[] */
    private array $seasons = [];

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function getGlobals(): array
    {
        return [
            'event_seasons' => $this->getSeasons(),
        ];
    }

    /**
     * @return string[]
     */
    private function getSeasons(): array
    {
        if ($this->loaded) {
            return $this->seasons;
        }

        $this->loaded = true;

        try {
            $this->seasons = $this->eventRepository->findSeasonsBySection('photos_de_match');
        } catch (\Throwable $e) {
            $this->seasons = [];
        }

        return $this->seasons;
    }
}
