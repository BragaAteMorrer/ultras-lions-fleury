<php

namespace App\Entity;

use App\Repository\CartageRegistrationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartageRegistrationRepository::class)]
#[ORM\Table(name: 'cartage_registration')]
class CartageRegistration
{
    public const STATUS_PENDING_CASH = 'pending_cash';
    public const STATUS_PENDING_ONLINE = 'pending_online';
    public const STATUS_PAID_ONLINE = 'paid_online';
    public const STATUS_VALIDATED_CASH = 'validated_cash';
    public const STATUS_CANCELLED = 'cancelled';
    public const COMPLETED_STATUSES = [
        self::STATUS_PAID_ONLINE,
        self::STATUS_VALIDATED_CASH,
    ];
    public const PENDING_STATUSES = [
        self::STATUS_PENDING_CASH,
        self::STATUS_PENDING_ONLINE,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'qr_token_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private CartageQrToken $qrToken = null;

    #[ORM\Column(length: 120)]
    private string $firstName = '';

    #[ORM\Column(length: 120)]
    private string $lastName = '';

    #[ORM\Column(length: 180)]
    private string $email = '';

    #[ORM\Column(length: 40)]
    private string $phone = '';

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private \DateTimeImmutable $birthDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private string $address = null;

    #[ORM\Column(length: 20, nullable: true)]
    private string $postalCode = null;

    #[ORM\Column(length: 120, nullable: true)]
    private string $city = null;

    #[ORM\Column(length: 30, nullable: true)]
    private string $shirtSize = null;

    #[ORM\Column(length: 30, nullable: true)]
    private string $poloSize = null;

    #[ORM\Column(length: 30, nullable: true)]
    private string $pullSize = null;

    #[ORM\Column(length: 30, nullable: true)]
    private string $sweatSize = null;

    #[ORM\Column(length: 30, nullable: true)]
    private string $jacketSize = null;

    #[ORM\Column(length: 30, nullable: true)]
    private string $shortSize = null;

    #[ORM\Column(length: 255, nullable: true)]
    private string $password = null;

    #[ORM\Column(type: 'float')]
    private float $amount = 20.0;

    #[ORM\Column(length: 20)]
    private string $paymentMethod = 'cash';

    #[ORM\Column(length: 30)]
    private string $status = self::STATUS_PENDING_CASH;

    #[ORM\Column(length: 9)]
    private string $season = '';

    #[ORM\Column(length: 80, nullable: true)]
    private string $checkoutReference = null;

    #[ORM\Column(length: 180, nullable: true)]
    private string $sumupCheckoutId = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private \DateTimeImmutable $paidAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private \DateTimeImmutable $validatedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private \DateTimeImmutable $internalRulesAcceptedAt = null;

    #[ORM\Column(length: 180, nullable: true)]
    private string $internalRulesTitle = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private string $internalRulesContent = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return trim($this->firstName.' '.$this->lastName) : ('Cartage #'.$this->id);
    }

    public function getId(): int { return $this->id; }

    public function getQrToken(): CartageQrToken { return $this->qrToken; }
    public function setQrToken(CartageQrToken $qrToken): self { $this->qrToken = $qrToken; return $this; }

    public function getFirstName(): string { return $this->firstName; }
    public function setFirstName(string $firstName): self { $this->firstName = trim($firstName); return $this; }

    public function getLastName(): string { return $this->lastName; }
    public function setLastName(string $lastName): self { $this->lastName = trim($lastName); return $this; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = trim($email); return $this; }

    public function getPhone(): string { return $this->phone; }
    public function setPhone(string $phone): self { $this->phone = trim($phone); return $this; }

    public function getBirthDate(): \DateTimeImmutable { return $this->birthDate; }
    public function setBirthDate(\DateTimeImmutable $birthDate): self { $this->birthDate = $birthDate; return $this; }

    public function getAddress(): string { return $this->address; }
    public function setAddress(string $address): self { $this->address = $address !== null  trim($address) : null; return $this; }

    public function getPostalCode(): string { return $this->postalCode; }
    public function setPostalCode(string $postalCode): self { $this->postalCode = $postalCode !== null  trim($postalCode) : null; return $this; }

    public function getCity(): string { return $this->city; }
    public function setCity(string $city): self { $this->city = $city !== null  trim($city) : null; return $this; }

    public function getShirtSize(): string { return $this->shirtSize; }
    public function setShirtSize(string $shirtSize): self { $this->shirtSize = $shirtSize !== null  trim($shirtSize) : null; return $this; }

    public function getPoloSize(): string { return $this->poloSize; }
    public function setPoloSize(string $poloSize): self { $this->poloSize = $poloSize !== null  trim($poloSize) : null; return $this; }

    public function getPullSize(): string { return $this->pullSize; }
    public function setPullSize(string $pullSize): self { $this->pullSize = $pullSize !== null  trim($pullSize) : null; return $this; }

    public function getSweatSize(): string { return $this->sweatSize; }
    public function setSweatSize(string $sweatSize): self { $this->sweatSize = $sweatSize !== null  trim($sweatSize) : null; return $this; }

    public function getJacketSize(): string { return $this->jacketSize; }
    public function setJacketSize(string $jacketSize): self { $this->jacketSize = $jacketSize !== null  trim($jacketSize) : null; return $this; }

    public function getShortSize(): string { return $this->shortSize; }
    public function setShortSize(string $shortSize): self { $this->shortSize = $shortSize !== null  trim($shortSize) : null; return $this; }

    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }

    public function getAmount(): float { return $this->amount; }
    public function setAmount(float $amount): self { $this->amount = max(0, $amount); return $this; }

    public function getPaymentMethod(): string { return $this->paymentMethod; }
    public function setPaymentMethod(string $paymentMethod): self { $this->paymentMethod = $paymentMethod; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    public function getSeason(): string { return $this->season; }
    public function setSeason(string $season): self { $this->season = trim($season); return $this; }

    public function getCheckoutReference(): string { return $this->checkoutReference; }
    public function setCheckoutReference(string $checkoutReference): self { $this->checkoutReference = $checkoutReference; return $this; }

    public function getSumupCheckoutId(): string { return $this->sumupCheckoutId; }
    public function setSumupCheckoutId(string $sumupCheckoutId): self { $this->sumupCheckoutId = $sumupCheckoutId; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function getPaidAt(): \DateTimeImmutable { return $this->paidAt; }
    public function setPaidAt(\DateTimeImmutable $paidAt): self { $this->paidAt = $paidAt; return $this; }

    public function getValidatedAt(): \DateTimeImmutable { return $this->validatedAt; }
    public function setValidatedAt(\DateTimeImmutable $validatedAt): self { $this->validatedAt = $validatedAt; return $this; }

    public function getInternalRulesAcceptedAt(): \DateTimeImmutable { return $this->internalRulesAcceptedAt; }
    public function setInternalRulesAcceptedAt(\DateTimeImmutable $internalRulesAcceptedAt): self { $this->internalRulesAcceptedAt = $internalRulesAcceptedAt; return $this; }

    public function getInternalRulesTitle(): string { return $this->internalRulesTitle; }
    public function setInternalRulesTitle(string $internalRulesTitle): self { $this->internalRulesTitle = $internalRulesTitle !== null  trim($internalRulesTitle) : null; return $this; }

    public function getInternalRulesContent(): string { return $this->internalRulesContent; }
    public function setInternalRulesContent(string $internalRulesContent): self { $this->internalRulesContent = $internalRulesContent; return $this; }

    public function markPaidOnline(): self
    {
        $this->status = self::STATUS_PAID_ONLINE;
        $this->paidAt = new \DateTimeImmutable();

        return $this;
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, self::COMPLETED_STATUSES, true);
    }

    public function isPending(): bool
    {
        return in_array($this->status, self::PENDING_STATUSES, true);
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING_CASH => 'En attente de validation liquide',
            self::STATUS_PENDING_ONLINE => 'En attente de paiement en ligne',
            self::STATUS_PAID_ONLINE => 'Cartage valide - paiement en ligne',
            self::STATUS_VALIDATED_CASH => 'Cartage valide - liquide',
            self::STATUS_CANCELLED => 'Annule',
            default => $this->status,
        };
    }
}
