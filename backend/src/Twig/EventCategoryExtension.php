<?php

namespace App\Twig;

use App\Repository\EventCategoryRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class EventCategoryExtension extends AbstractExtension implements GlobalsInterface
{
    private EventCategoryRepository $eventCategoryRepository;
    private bool $loaded = false;
    private array $mediaCategories = [];

    public function __construct(EventCategoryRepository $eventCategoryRepository)
    {
        $this->eventCategoryRepository = $eventCategoryRepository;
    }

    public function getGlobals(): array
    {
        return [
            'event_media_categories' => $this->getMediaCategories(),
        ];
    }

    private function getMediaCategories(): array
    {
        if ($this->loaded) {
            return $this->mediaCategories;
        }

        $this->loaded = true;

        try {
            $this->mediaCategories = $this->eventCategoryRepository->findBySection('medias');
        } catch (\Throwable $e) {
            $this->mediaCategories = [];
        }

        return $this->mediaCategories;
    }
}
