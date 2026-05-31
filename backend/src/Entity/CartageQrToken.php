<php

namespace App\Entity;

use App\Repository\CartageQrTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartageQrTokenRepository::class)]
#[ORM\Table(name: 'cartage_qr_token')]
class CartageQrToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id = null;

    #[ORM\Column(length: 120)]
    private string $label = 'Cartage';

    #[ORM\Column(length: 80, unique: true)]
    private string $token = '';

    #[ORM\Column(type: 'float')]
    private float $amount = 20.0;

    #[ORM\Column]
    private bool $enabled = true;

    #[ORM\Column(nullable: true)]
    private int $maxUses = null;

    #[ORM\Column]
    private int $usedCount = 0;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private \DateTimeImmutable $expiresAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->token = bin2hex(random_bytes(24));
        $this->createdAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->label;
    }

    public function getId(): int { return $this->id; }

    public function getLabel(): string { return $this->label; }
    public function setLabel(string $label): self { $this->label = trim($label); return $this; }

    public function getToken(): string { return $this->token; }
    public function setToken(string $token): self { $this->token = trim($token); return $this; }

    public function getAmount(): float { return $this->amount; }
    public function setAmount(float $amount): self { $this->amount = max(0, $amount); return $this; }

    public function isEnabled(): bool { return $this->enabled; }
    public function setEnabled(bool $enabled): self { $this->enabled = $enabled; return $this; }

    public function getMaxUses(): int { return $this->maxUses; }
    public function setMaxUses(int $maxUses): self { $this->maxUses = $maxUses !== null  max(0, $maxUses) : null; return $this; }

    public function getUsedCount(): int { return $this->usedCount; }
    public function setUsedCount(int $usedCount): self { $this->usedCount = max(0, $usedCount); return $this; }
    public function incrementUsedCount(): self { $this->usedCount++; return $this; }

    public function getExpiresAt(): \DateTimeImmutable { return $this->expiresAt; }
    public function setExpiresAt(\DateTimeImmutable $expiresAt): self { $this->expiresAt = $expiresAt; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function getPath(): string
    {
        return '/cartage/qr/'.$this->token;
    }

    public function getQrPreview(): string
    {
        return $this->token;
    }

    public function isUsable(\DateTimeImmutable $now = null): bool
    {
        $now = new \DateTimeImmutable();

        if (!$this->enabled) {
            return false;
        }

        if ($this->expiresAt !== null && $this->expiresAt < $now) {
            return false;
        }

        return $this->maxUses === null || $this->usedCount < $this->maxUses;
    }
}
