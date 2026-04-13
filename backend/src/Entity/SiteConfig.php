<?php

namespace App\Entity;

use App\Repository\SiteConfigRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteConfigRepository::class)]
#[ORM\Table(name: 'site_config')]
class SiteConfig
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $siteName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $heroTitle = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $heroSubtitle = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $chantsSubtitle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $chantsTitle = null;

    public function getId(): ?int { return $this->id; }

    public function getSiteName(): ?string { return $this->siteName; }

    public function setSiteName(?string $siteName): self
    {
        $this->siteName = $siteName;
        return $this;
    }

    public function getHeroTitle(): ?string { return $this->heroTitle; }

    public function setHeroTitle(?string $heroTitle): self
    {
        $this->heroTitle = $heroTitle;
        return $this;
    }

    public function getHeroSubtitle(): ?string { return $this->heroSubtitle; }

    public function setHeroSubtitle(?string $heroSubtitle): self
    {
        $this->heroSubtitle = $heroSubtitle;
        return $this;
    }

    public function getChantsSubtitle(): ?string { return $this->chantsSubtitle; }

    public function setChantsSubtitle(?string $chantsSubtitle): self
    {
        $this->chantsSubtitle = $chantsSubtitle;
        return $this;
    }

    public function getChantsTitle(): ?string { return $this->chantsTitle; }

    public function setChantsTitle(?string $chantsTitle): self
    {
        $this->chantsTitle = $chantsTitle;
        return $this;
    }

    public function __toString(): string
    {
        return $this->siteName ?? ('Config #'.$this->id);
    }
}
