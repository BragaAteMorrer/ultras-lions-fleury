<?php

namespace App\Entity;

use App\Repository\InviteCodeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InviteCodeRepository::class)]
class InviteCode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $code = null;

    #[ORM\Column]
    private bool $used = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $expiresAt;

    public function __construct()
    {
        $this->code = bin2hex(random_bytes(16)); // Code aléatoire de 32 chars
        $this->expiresAt = new \DateTimeImmutable('+1 week');
    }

    // ------------------------------------------
    // GETTERS / SETTERS
    // ------------------------------------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    public function setUsed(bool $used): self
    {
        $this->used = $used;
        return $this;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeImmutable $expiresAt): self
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    // Bonus : pratique pour vérifier si un code est valide
    public function isValid(): bool
    {
        return !$this->used && $this->expiresAt > new \DateTimeImmutable();
    }
}
