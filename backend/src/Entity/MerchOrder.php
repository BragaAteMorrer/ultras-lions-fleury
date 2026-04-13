<?php

namespace App\Entity;

use App\Repository\MerchOrderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MerchOrderRepository::class)]
#[ORM\Table(name: 'merch_order')]
class MerchOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'merch_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Merch $merch = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $customerFirstName = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $customerLastName = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $size = null;

    #[ORM\Column(type: 'integer')]
    private int $quantity = 1;

    #[ORM\Column(type: 'float')]
    private float $unitPrice = 0.0;

    #[ORM\Column(type: 'float')]
    private float $totalPrice = 0.0;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $note = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(length: 20)]
    private string $paymentMethod = 'sumup';

    #[ORM\Column(type: 'boolean')]
    private bool $executed = false;

    public function getId(): ?int { return $this->id; }

    public function getUser(): ?User { return $this->user; }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getMerch(): ?Merch { return $this->merch; }

    public function setMerch(?Merch $merch): self
    {
        $this->merch = $merch;
        return $this;
    }

    public function getEmail(): ?string { return $this->email; }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getCustomerFirstName(): ?string { return $this->customerFirstName; }

    public function setCustomerFirstName(?string $name): self
    {
        $this->customerFirstName = $name;
        return $this;
    }

    public function getCustomerLastName(): ?string { return $this->customerLastName; }

    public function setCustomerLastName(?string $name): self
    {
        $this->customerLastName = $name;
        return $this;
    }

    public function getSize(): ?string { return $this->size; }

    public function setSize(?string $size): self
    {
        $this->size = $size;
        return $this;
    }

    public function getQuantity(): int { return $this->quantity; }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = max(1, $quantity);
        return $this;
    }

    public function getUnitPrice(): float { return $this->unitPrice; }

    public function setUnitPrice(float $unitPrice): self
    {
        $this->unitPrice = $unitPrice;
        return $this;
    }

    public function getTotalPrice(): float { return $this->totalPrice; }

    public function setTotalPrice(float $totalPrice): self
    {
        $this->totalPrice = $totalPrice;
        return $this;
    }

    public function getNote(): ?string { return $this->note; }

    public function setNote(?string $note): self
    {
        $this->note = $note;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getPaymentMethod(): string { return $this->paymentMethod; }

    public function setPaymentMethod(string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    public function isExecuted(): bool { return $this->executed; }

    public function setExecuted(bool $executed): self
    {
        $this->executed = $executed;
        return $this;
    }
}
