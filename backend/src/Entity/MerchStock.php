<?php

namespace App\Entity;

use App\Repository\MerchStockRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MerchStockRepository::class)]
#[ORM\Table(name: 'merch_stock')]
#[ORM\UniqueConstraint(name: 'uniq_merch_stock_merch_size', columns: ['merch_id', 'size'])]
class MerchStock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'stocks')]
    #[ORM\JoinColumn(name: 'merch_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Merch $merch = null;

    #[ORM\Column(length: 30)]
    private string $size = 'TU';

    #[ORM\Column]
    private int $quantity = 0;

    public function __toString(): string
    {
        return sprintf('%s (%d)', $this->size, $this->quantity);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMerch(): ?Merch
    {
        return $this->merch;
    }

    public function setMerch(?Merch $merch): self
    {
        $this->merch = $merch;
        return $this;
    }

    public function getSize(): string
    {
        return $this->size;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = max(0, $quantity);
        return $this;
    }
}

