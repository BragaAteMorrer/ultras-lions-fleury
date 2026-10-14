<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'refreshment_product')]
class RefreshmentProduct
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?RefreshmentCategory $category = null;

    #[ORM\Column(length: 160)]
    private string $name = '';

    #[ORM\Column(type: 'integer')]
    private int $priceCents = 0;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $stock = null;

    #[ORM\Column(options: ['default' => true'])]
    private bool $active = true;

    public function getStock(): ?int { return $this->stock; }
    public function setStock(?int $stock): self { $this->stock = $stock === null ? null : max(0, $stock); return $this; }
    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $value): self { $this->name = $value; return $this; }
    public function getCategory(): ?RefreshmentCategory { return $this->category; }
    public function setCategory(?RefreshmentCategory $value): self { $this->category = $value; return $this; }
    public function getPriceCents(): int { return $this->priceCents; }
    public function setPriceCents(int $value): self { $this->priceCents = max(0, $value); return $this; }
    public function isActive(): bool { return $this->active; }
    public function setActive(bool $value): self { $this->active = $value; return $this; }
    public function __toString(): string { return $this->name; }
}
