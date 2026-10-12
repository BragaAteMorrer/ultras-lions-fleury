<?php

namespace App\Entity;

use App\Repository\DonationRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\PaymentCheckout;
use App\Entity\User;

#[ORM\Entity(repositoryClass: DonationRepository::class)]
#[ORM\Table(name: 'donation')]
#[ORM\Index(name: 'idx_donation_status_paid_at', columns: ['status', 'paid_at'])]
#[ORM\Index(name: 'idx_donation_created_at', columns: ['created_at'])]
class Donation
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REFUNDED = 'refunded';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_PAID,
        self::STATUS_FAILED,
        self::STATUS_CANCELLED,
        self::STATUS_REFUNDED,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\OneToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'payment_checkout_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?PaymentCheckout $paymentCheckout = null;

    #[ORM\Column(type: 'float')]
    private float $amount = 0.0;

    #[ORM\Column(length: 3)]
    private string $currency = 'EUR';

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_PENDING;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $donorEmail = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $donorFirstName = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $donorLastName = null;

    #[ORM\Column(type: 'boolean')]
    private bool $anonymous = false;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $message = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $paidAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $cancelledAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->getDisplayName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPaymentCheckout(): ?PaymentCheckout
    {
        return $this->paymentCheckout;
    }

    public function setPaymentCheckout(?PaymentCheckout $paymentCheckout): self
    {
        $this->paymentCheckout = $paymentCheckout;

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = round(max(0, $amount), 2);

        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = strtoupper(trim($currency));

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $status = strtolower(trim($status));
        $this->status = in_array($status, self::STATUSES, true) ? $status : self::STATUS_PENDING;

        return $this;
    }

    public function getStatusTranslationKey(): string
    {
        return match ($this->status) {
            self::STATUS_PAID => 'donation.status.paid',
            self::STATUS_FAILED => 'donation.status.failed',
            self::STATUS_CANCELLED => 'donation.status.cancelled',
            self::STATUS_REFUNDED => 'donation.status.refunded',
            default => 'donation.status.pending',
        };
    }

    public function getStatusTone(): string
    {
        return match ($this->status) {
            self::STATUS_PAID => 'success',
            self::STATUS_FAILED => 'danger',
            self::STATUS_CANCELLED => 'warning',
            self::STATUS_REFUNDED => 'info',
            default => 'secondary',
        };
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isAnonymous(): bool
    {
        return $this->anonymous;
    }

    public function setAnonymous(bool $anonymous): self
    {
        $this->anonymous = $anonymous;

        return $this;
    }

    public function getDonorEmail(): ?string
    {
        return $this->donorEmail;
    }

    public function setDonorEmail(?string $donorEmail): self
    {
        $this->donorEmail = $donorEmail !== null ? trim($donorEmail) : null;

        return $this;
    }

    public function getDonorFirstName(): ?string
    {
        return $this->donorFirstName;
    }

    public function setDonorFirstName(?string $donorFirstName): self
    {
        $this->donorFirstName = $donorFirstName !== null ? trim($donorFirstName) : null;

        return $this;
    }

    public function getDonorLastName(): ?string
    {
        return $this->donorLastName;
    }

    public function setDonorLastName(?string $donorLastName): self
    {
        $this->donorLastName = $donorLastName !== null ? trim($donorLastName) : null;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $message = $message !== null ? trim($message) : null;
        $this->message = $message !== '' ? $message : null;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getPaidAt(): ?\DateTimeImmutable
    {
        return $this->paidAt;
    }

    public function setPaidAt(?\DateTimeImmutable $paidAt): self
    {
        $this->paidAt = $paidAt;

        return $this;
    }

    public function getCancelledAt(): ?\DateTimeImmutable
    {
        return $this->cancelledAt;
    }

    public function setCancelledAt(?\DateTimeImmutable $cancelledAt): self
    {
        $this->cancelledAt = $cancelledAt;

        return $this;
    }

    public function markPaid(): self
    {
        $this->status = self::STATUS_PAID;
        $this->paidAt ??= new \DateTimeImmutable();

        return $this;
    }

    public function markFailed(): self
    {
        $this->status = self::STATUS_FAILED;

        return $this;
    }

    public function markCancelled(): self
    {
        $this->status = self::STATUS_CANCELLED;
        $this->cancelledAt ??= new \DateTimeImmutable();

        return $this;
    }

    public function markRefunded(): self
    {
        $this->status = self::STATUS_REFUNDED;

        return $this;
    }

    public function getPaymentReference(): ?string
    {
        return $this->paymentCheckout?->getCheckoutReference();
    }

    public function getPaymentStatus(): ?string
    {
        return $this->paymentCheckout?->getStatus();
    }

    public function getPaymentStatusTranslationKey(): string
    {
        return match ($this->getPaymentStatus()) {
            'paid' => 'donation.payment_status.paid',
            'failed' => 'donation.payment_status.failed',
            'cancelled' => 'donation.payment_status.cancelled',
            default => 'donation.payment_status.pending',
        };
    }

    public function getDisplayName(): string
    {
        if ($this->anonymous) {
            return 'Don anonyme';
        }

        $name = trim((string) ($this->donorFirstName ?? '') . ' ' . (string) ($this->donorLastName ?? ''));
        if ($name !== '') {
            return $name;
        }

        if ($this->donorEmail) {
            return $this->donorEmail;
        }

        return sprintf('Don #%d', $this->id ?? 0);
    }
}
