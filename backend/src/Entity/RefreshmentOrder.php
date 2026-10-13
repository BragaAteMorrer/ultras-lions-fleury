<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'refreshment_order')]
class RefreshmentOrder
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?Ticket $ticket = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?RefreshmentProduct $product = null;

    #[ORM\Column(type: 'integer')]
    private int $quantity = 1;

    #[ORM\Column(length: 20)]
    private string $status = 'confirmed';

    #[ORM\Column(type: 'integer')]
    private int $unitPriceCents = 0;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $customerEmail = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct() { $this->createdAt = new \DateTimeImmutable(); }
    public function getId(): ?int { return $this->id; }
    public function getTicket(): ?Ticket { return $this->ticket; }
    public function setTicket(?Ticket $ticket): self { $this->ticket = $ticket; return $this; }
    public function getProduct(): ?RefreshmentProduct { return $this->product; }
    public function setProduct(?RefreshmentProduct $product): self { $this->product = $product; return $this; }
    public function getQuantity(): int { return $this->quantity; }
    public function setQuantity(int $quantity): self { $this->quantity = max(1, $quantity); return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }
    public function getUnitPriceCents(): int { return $this->unitPriceCents; }
    public function setUnitPriceCents(int $price): self { $this->unitPriceCents = max(0, $price); return $this; }
    public function getCustomerEmail(): ?string { return $this->customerEmail; }
    public function setCustomerEmail(?string $value): self { $this->customerEmail = $value; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function __toString(): string { return sprintf('%s x%d', $this->product?->getName() ?? 'Produit', $this->quantity); }
}
