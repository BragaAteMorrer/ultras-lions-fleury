<?php

namespace App\Entity;

use App\Repository\GroupPageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupPageRepository::class)]
#[ORM\Table(name: 'group_page')]
class GroupPage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $histoireText = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $mentaliteText = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $fonctionnementText = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $rejoindreText = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $seCarterText = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'logo_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Media $logo = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'banner_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Media $banner = null;

    /** @var Collection<int, Media> */
    #[ORM\OneToMany(mappedBy: 'groupPage', targetEntity: Media::class, cascade: ['persist'])]
    private Collection $media;

    public function __construct()
    {
        $this->media = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string { return $this->description; }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getHistoireText(): ?string { return $this->histoireText; }

    public function setHistoireText(?string $histoireText): self
    {
        $this->histoireText = $histoireText;
        return $this;
    }

    public function getMentaliteText(): ?string { return $this->mentaliteText; }

    public function setMentaliteText(?string $mentaliteText): self
    {
        $this->mentaliteText = $mentaliteText;
        return $this;
    }

    public function getFonctionnementText(): ?string { return $this->fonctionnementText; }

    public function setFonctionnementText(?string $fonctionnementText): self
    {
        $this->fonctionnementText = $fonctionnementText;
        return $this;
    }

    public function getRejoindreText(): ?string { return $this->rejoindreText; }

    public function setRejoindreText(?string $rejoindreText): self
    {
        $this->rejoindreText = $rejoindreText;
        return $this;
    }

    public function getSeCarterText(): ?string { return $this->seCarterText; }

    public function setSeCarterText(?string $seCarterText): self
    {
        $this->seCarterText = $seCarterText;
        return $this;
    }

    public function getLogo(): ?Media { return $this->logo; }

    public function setLogo(?Media $logo): self
    {
        $this->logo = $logo;
        return $this;
    }

    public function getBanner(): ?Media { return $this->banner; }

    public function setBanner(?Media $banner): self
    {
        $this->banner = $banner;
        return $this;
    }

    /** @return Collection<int, Media> */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    /**
     * @param iterable<Media> $media
     */
    public function setMedia(iterable $media): self
    {
        foreach ($this->media as $item) {
            $this->removeMedia($item);
        }

        foreach ($media as $item) {
            $this->addMedia($item);
        }

        return $this;
    }

    public function addMedia(Media $media): self
    {
        if (!$this->media->contains($media)) {
            $this->media->add($media);
            $media->setGroupPage($this);
        }

        return $this;
    }

    public function removeMedia(Media $media): self
    {
        if ($this->media->removeElement($media) && $media->getGroupPage() === $this) {
            $media->setGroupPage(null);
        }

        return $this;
    }
}
