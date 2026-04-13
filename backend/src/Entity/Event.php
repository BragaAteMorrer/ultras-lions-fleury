<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\Table(name: 'event')]
#[ORM\HasLifecycleCallbacks]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $title = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?EventCategory $category = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $opponent = null;

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $matchLocation = null;

    #[ORM\Column(length: 9, nullable: true)]
    private ?string $season = null;

    #[ORM\Column(nullable: true)]
    private ?int $journee = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'image_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Media $image = null;

    /** @var Collection<int, Media> */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Media::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $media;

    public function __construct()
    {
        $this->media = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->title;
    }

    public function getId(): ?int { return $this->id; }

    public function getTitle(): ?string { return $this->title; }

    public function setTitle(?string $title): self
    {
        $this->title = $title ?? '';
        return $this;
    }

    public function getDate(): ?\DateTimeInterface { return $this->date; }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        $this->refreshPhotoMatchTitle();
        return $this;
    }

    public function getDescription(): ?string { return $this->description; }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getCategory(): ?EventCategory
    {
        return $this->category;
    }

    public function setCategory(?EventCategory $category): self
    {
        $this->category = $category;
        if ($category?->getSection() !== 'photos_de_match') {
            $this->season = null;
            $this->journee = null;
            $this->opponent = null;
            $this->matchLocation = null;
        }
        return $this;
    }

    public function getOpponent(): ?string
    {
        return $this->opponent;
    }

    public function setOpponent(?string $opponent): self
    {
        $this->opponent = $opponent;
        $this->refreshPhotoMatchTitle();
        return $this;
    }

    public function getMatchLocation(): ?string
    {
        return $this->matchLocation;
    }

    public function setMatchLocation(?string $matchLocation): self
    {
        $this->matchLocation = $matchLocation;
        $this->refreshPhotoMatchTitle();
        return $this;
    }

    public function getSeason(): ?string
    {
        return $this->season;
    }

    public function setSeason(?string $season): self
    {
        $this->season = $season;
        return $this;
    }

    public function getJournee(): ?int
    {
        return $this->journee;
    }

    public function setJournee(?int $journee): self
    {
        $this->journee = $journee;
        return $this;
    }

    public function getImage(): ?Media { return $this->image; }

    public function setImage(?Media $image): self
    {
        $this->image = $image;
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
            $media->setEvent($this);
        }

        return $this;
    }

    public function removeMedia(Media $media): self
    {
        if ($this->media->removeElement($media) && $media->getEvent() === $this) {
            $media->setEvent(null);
        }

        return $this;
    }

    public function addMedium(Media $media): self
    {
        return $this->addMedia($media);
    }

    public function removeMedium(Media $media): self
    {
        return $this->removeMedia($media);
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updatePhotoMatchTitle(): void
    {
        $this->refreshPhotoMatchTitle();
    }

    private function refreshPhotoMatchTitle(): void
    {
        if ($this->category?->getSection() !== 'photos_de_match') {
            return;
        }

        $opponent = trim((string) $this->opponent);
        $datePart = $this->date ? $this->date->format('d/m/Y') : '';

        if ($opponent === '') {
            $this->title = $datePart !== '' ? sprintf('FC Fleury 91 - %s', $datePart) : 'FC Fleury 91';
            return;
        }

        if ($this->matchLocation === 'domicile') {
            $base = sprintf('FC Fleury 91 - %s', $opponent);
            $this->title = $datePart !== '' ? sprintf('%s - %s', $base, $datePart) : $base;
            return;
        }

        if ($this->matchLocation === 'exterieur') {
            $base = sprintf('%s - FC Fleury 91', $opponent);
            $this->title = $datePart !== '' ? sprintf('%s - %s', $base, $datePart) : $base;
            return;
        }

        $base = sprintf('FC Fleury 91 - %s', $opponent);
        $this->title = $datePart !== '' ? sprintf('%s - %s', $base, $datePart) : $base;
    }
}
