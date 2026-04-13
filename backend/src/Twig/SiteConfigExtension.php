<?php

namespace App\Twig;

use App\Repository\SiteConfigRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class SiteConfigExtension extends AbstractExtension implements GlobalsInterface
{
    private SiteConfigRepository $siteConfigRepository;
    private ?string $cachedSiteName = null;
    private bool $loaded = false;

    public function __construct(SiteConfigRepository $siteConfigRepository)
    {
        $this->siteConfigRepository = $siteConfigRepository;
    }

    public function getGlobals(): array
    {
        return [
            'site_name' => $this->getSiteName(),
        ];
    }

    private function getSiteName(): ?string
    {
        if ($this->loaded) {
            return $this->cachedSiteName;
        }

        $this->loaded = true;

        try {
            $config = $this->siteConfigRepository->findOneBy([], ['id' => 'ASC']);
            $this->cachedSiteName = $config?->getSiteName();
        } catch (\Throwable $e) {
            $this->cachedSiteName = null;
        }

        return $this->cachedSiteName;
    }
}
