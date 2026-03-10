<?php

namespace App\Entity;

use App\Repository\MerchRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MerchRepository::class)]
#[ORM\Table(name: 'merch')]
class Merch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $title = null;

    #[ORM\Column(type: 'float')]
    private ?float $price = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'image_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Media $image = null;

    #[ORM\ManyToOne(inversedBy: 'merch')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?MerchCategory $category = null;

    /** @var Collection<int, Media> */
    #[ORM\OneToMany(mappedBy: 'merch', targetEntity: Media::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $media;

    /** @var Collection<int, MerchStock> */
    #[ORM\OneToMany(mappedBy: 'merch', targetEntity: MerchStock::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $stocks;

    public function __construct()
    {
        $this->media = new ArrayCollection();
        $this->stocks = new ArrayCollection();
    }

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

    public function getImage(): ?Media { return $this->image; }

    public function setImage(?Media $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getCategory(): ?MerchCategory { return $this->category; }

    public function setCategory(?MerchCategory $category): self
    {
        $this->category = $category;
        return $this;
    }

    /** @return Collection<int, Media> */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedia(Media $media): self
    {
        if (!$this->media->contains($media)) {
            $this->media->add($media);
            $media->setMerch($this);
        }

        return $this;
    }

    public function removeMedia(Media $media): self
    {
        if ($this->media->removeElement($media) && $media->getMerch() === $this) {
            $media->setMerch(null);
        }

        return $this;
    }

    /** @return Collection<int, MerchStock> */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    public function addStock(MerchStock $stock): self
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks->add($stock);
            $stock->setMerch($this);
        }

        return $this;
    }

    public function removeStock(MerchStock $stock): self
    {
        if ($this->stocks->removeElement($stock) && $stock->getMerch() === $this) {
            $stock->setMerch(null);
        }

        return $this;
    }

    public function getStockForSize(string $size): ?MerchStock
    {
        foreach ($this->stocks as $stock) {
            if ($stock->getSize() === $size) {
                return $stock;
            }
        }

        return null;
    }

    public function getTotalStock(): int
    {
        $total = 0;
        foreach ($this->stocks as $stock) {
            $total += $stock->getQuantity();
        }

        return $total;
    }
}
