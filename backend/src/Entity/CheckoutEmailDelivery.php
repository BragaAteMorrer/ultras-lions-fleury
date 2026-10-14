<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'checkout_email_delivery')]
#[ORM\UniqueConstraint(name: 'uniq_checkout_email_delivery_checkout', columns: ['checkout_id'])]
final class CheckoutEmailDelivery
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private PaymentCheckout $checkout;

    #[ORM\Column(length: 20)]
    private string $status = 'pending';

    #[ORM\Column(type: 'integer')]
    private int $attempts = 0;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $sentAt = null;

    public function __construct(PaymentCheckout $checkout) { $this->checkout = $checkout; }
    public function getId(): ?int { return $this->id; }
    public function getCheckout(): PaymentCheckout { return $this->checkout; }
    public function getStatus(): string { return $this->status; }
    public function getAttempts(): int { return $this->attempts; }
    public function getStartedAt(): ?\DateTimeImmutable { return $this->startedAt; }
    public function getSentAt(): ?\DateTimeImmutable { return $this->sentAt; }
    public function markSending(): void { $this->status = 'sending'; ++$this->attempts; $this->startedAt = new \DateTimeImmutable(); }
    public function markSent(): void { $this->status = 'sent'; $this->sentAt = new \DateTimeImmutable(); }
    public function markUncertain(): void { $this->status = 'uncertain'; }
}
