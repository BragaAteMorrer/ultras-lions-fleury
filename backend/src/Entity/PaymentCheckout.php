<?php

namespace App\Entity;

use App\Repository\PaymentCheckoutRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentCheckoutRepository::class)]
#[ORM\Table(name: 'payment_checkout')]
class PaymentCheckout
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $type = null; // merch | ticket

    #[ORM\Column(length: 20)]
    private string $status = 'pending'; // pending | paid | failed

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $sumupCheckoutId = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $checkoutReference = null;

    #[ORM\Column(type: 'float')]
    private float $amount = 0.0;

    #[ORM\Column(length: 3)]
    private string $currency = 'EUR';

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $customerFirstName = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $customerLastName = null;

    #[ORM\Column(type: 'json')]
    private array $cart = [];

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $paidAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $processedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $emailSentAt = null;

    public function getId(): ?int { return $this->id; }

    public function getType(): ?string { return $this->type; }
    public function setType(string $type): self { $this->type = $type; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'paid' => 'Payé',
            'pending' => 'En attente',
            'failed' => 'Échoué',
            default => $this->status,
        };
    }

    public function getSumupCheckoutId(): ?string { return $this->sumupCheckoutId; }
    public function setSumupCheckoutId(?string $id): self { $this->sumupCheckoutId = $id; return $this; }

    public function getCheckoutReference(): ?string { return $this->checkoutReference; }
    public function setCheckoutReference(?string $ref): self { $this->checkoutReference = $ref; return $this; }

    public function getAmount(): float { return $this->amount; }
    public function setAmount(float $amount): self { $this->amount = $amount; return $this; }

    public function getCurrency(): string { return $this->currency; }
    public function setCurrency(string $currency): self { $this->currency = $currency; return $this; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): self { $this->email = $email; return $this; }

    public function getCustomerFirstName(): ?string { return $this->customerFirstName; }
    public function setCustomerFirstName(?string $name): self { $this->customerFirstName = $name; return $this; }

    public function getCustomerLastName(): ?string { return $this->customerLastName; }
    public function setCustomerLastName(?string $name): self { $this->customerLastName = $name; return $this; }

    public function getCart(): array { return $this->cart; }
    public function setCart(array $cart): self { $this->cart = $cart; return $this; }

    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $dt): self { $this->createdAt = $dt; return $this; }

    public function getPaidAt(): ?\DateTimeInterface { return $this->paidAt; }
    public function setPaidAt(?\DateTimeInterface $dt): self { $this->paidAt = $dt; return $this; }

    public function getProcessedAt(): ?\DateTimeInterface { return $this->processedAt; }
    public function setProcessedAt(?\DateTimeInterface $dt): self { $this->processedAt = $dt; return $this; }

    public function getEmailSentAt(): ?\DateTimeInterface { return $this->emailSentAt; }
    public function setEmailSentAt(?\DateTimeInterface $dt): self { $this->emailSentAt = $dt; return $this; }
}
