<?php

namespace App\Twig;

use App\Entity\SiteConfig;
use App\Repository\SiteConfigRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class SiteConfigExtension extends AbstractExtension implements GlobalsInterface
{
    private SiteConfigRepository $siteConfigRepository;
    private ?SiteConfig $cachedConfig = null;
    private ?string $cachedSiteName = null;
    private ?string $cachedBackgroundUrl = null;
    private bool $siteNameLoaded = false;
    private bool $configLoaded = false;

    public function __construct(SiteConfigRepository $siteConfigRepository)
    {
        $this->siteConfigRepository = $siteConfigRepository;
    }

    public function getGlobals(): array
    {
        return [
            'site_name' => $this->getSiteName(),
            'site_config' => $this->getConfig(),
            'site_background_url' => $this->getBackgroundUrl(),
        ];
    }

    private function getSiteName(): ?string
    {
        if ($this->siteNameLoaded) {
            return $this->cachedSiteName;
        }

        $this->siteNameLoaded = true;

        try {
            $config = $this->getConfig();
            $this->cachedSiteName = $config?->getSiteName();
        } catch (\Throwable $e) {
            $this->cachedSiteName = null;
        }

        return $this->cachedSiteName;
    }

    private function getBackgroundUrl(): ?string
    {
        if ($this->cachedBackgroundUrl !== null) {
            return $this->cachedBackgroundUrl;
        }

        try {
            $path = $this->getConfig()?->getBackgroundImage()?->getPath();
            $this->cachedBackgroundUrl = $path ? '/uploads/media/'.$path : null;
        } catch (\Throwable $e) {
            $this->cachedBackgroundUrl = null;
        }

        return $this->cachedBackgroundUrl;
    }

    private function getConfig(): ?SiteConfig
    {
        if ($this->configLoaded) {
            return $this->cachedConfig;
        }

        $this->configLoaded = true;

        try {
            $this->cachedConfig = $this->siteConfigRepository->findOneBy([], ['id' => 'ASC']);
        } catch (\Throwable $e) {
            $this->cachedConfig = null;
        }

        return $this->cachedConfig;
    }
}
