<?php

namespace App\Entity;

use App\Repository\UserAccountLinkRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserAccountLinkRepository::class)]
#[ORM\Table(name: 'user_account_link')]
#[ORM\UniqueConstraint(name: 'uniq_user_account_link_pair', columns: ['guardian_id', 'managed_user_id'])]
class UserAccountLink
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'guardian_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $guardian = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'managed_user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $managedUser = null;

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_PENDING;

    #[ORM\Column(length: 64, unique: true)]
    private string $token = '';

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $requestedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $acceptedAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $expiresAt;

    public function __construct()
    {
        $this->requestedAt = new \DateTimeImmutable();
        $this->expiresAt = $this->requestedAt->modify('+14 days');
    }

    public function getId(): ?int { return $this->id; }

    public function getGuardian(): ?User { return $this->guardian; }
    public function setGuardian(?User $guardian): self { $this->guardian = $guardian; return $this; }

    public function getManagedUser(): ?User { return $this->managedUser; }
    public function setManagedUser(?User $managedUser): self { $this->managedUser = $managedUser; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    public function getToken(): string { return $this->token; }
    public function setToken(string $token): self { $this->token = $token; return $this; }

    public function getRequestedAt(): \DateTimeImmutable { return $this->requestedAt; }
    public function setRequestedAt(\DateTimeImmutable $requestedAt): self { $this->requestedAt = $requestedAt; return $this; }

    public function getAcceptedAt(): ?\DateTimeImmutable { return $this->acceptedAt; }
    public function setAcceptedAt(?\DateTimeImmutable $acceptedAt): self { $this->acceptedAt = $acceptedAt; return $this; }

    public function getExpiresAt(): \DateTimeImmutable { return $this->expiresAt; }
    public function setExpiresAt(\DateTimeImmutable $expiresAt): self { $this->expiresAt = $expiresAt; return $this; }

    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTimeImmutable();
    }

    public function accept(): self
    {
        $this->status = self::STATUS_ACCEPTED;
        $this->acceptedAt = new \DateTimeImmutable();

        return $this;
    }
}
