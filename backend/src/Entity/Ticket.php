<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[ORM\Table(name: 'ticket')]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $title = null;

    #[ORM\Column(length: 180)]
    private ?string $opponent = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $matchDate = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $matchLocation = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $venue = null;

    #[ORM\Column(length: 2048, nullable: true)]
    #[Assert\Url]
    private ?string $billetwebUrl = null;

    #[ORM\Column(type: 'float')]
    private ?float $price = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'integer')]
    private int $stock = 0;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'image_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Media $image = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?TicketCategory $category = null;

    public function __toString(): string
    {
        return (string) $this->title;
    }

    public function getId(): ?int { return $this->id; }

    public function getTitle(): ?string { return $this->title; }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getOpponent(): ?string { return $this->opponent; }

    public function setOpponent(string $opponent): self
    {
        $this->opponent = $opponent;
        return $this;
    }

    public function getMatchDate(): ?\DateTimeInterface { return $this->matchDate; }

    public function setMatchDate(\DateTimeInterface $matchDate): self
    {
        $this->matchDate = $matchDate;
        return $this;
    }

    public function getMatchLocation(): ?string { return $this->matchLocation; }

    public function setMatchLocation(?string $matchLocation): self
    {
        $this->matchLocation = $matchLocation;
        return $this;
    }

    public function getVenue(): ?string { return $this->venue; }

    public function setVenue(?string $venue): self
    {
        $this->venue = $venue;
        return $this;
    }

    public function getBilletwebUrl(): ?string { return $this->billetwebUrl; }

    public function setBilletwebUrl(?string $billetwebUrl): self
    {
        $this->billetwebUrl = $billetwebUrl;
        return $this;
    }

    public function getPrice(): ?float { return $this->price; }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getDescription(): ?string { return $this->description; }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getStock(): int { return $this->stock; }

    public function setStock(int $stock): self
    {
        $this->stock = max(0, $stock);
        return $this;
    }

    public function getImage(): ?Media { return $this->image; }

    public function setImage(?Media $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getCategory(): ?TicketCategory { return $this->category; }

    public function setCategory(?TicketCategory $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function isHomeMatch(): bool
    {
        return $this->matchLocation === null || $this->matchLocation === 'domicile';
    }

    public function usesBilletweb(): bool
    {
        return $this->isHomeMatch() && $this->billetwebUrl !== null && trim($this->billetwebUrl) !== '';
    }
}
