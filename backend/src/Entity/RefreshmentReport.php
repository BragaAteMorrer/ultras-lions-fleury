<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'refreshment_report')]
#[ORM\UniqueConstraint(name: 'uniq_refreshment_report_ticket', columns: ['ticket_id'])]
class RefreshmentReport
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Ticket $ticket = null;

    #[ORM\Column(length: 180)]
    private string $recipient = '';

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $sentAt = null;

    #[ORM\Column(length: 24, options: ['default' => 'pending'])]
    private string $deliveryStatus = 'pending';

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $attempts = 0;

    #[ORM\Column(length: 64, nullable: true, unique: true)]
    private ?string $deliveryKey = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $lastAttemptAt = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $snapshot = null;

    public function getDeliveryStatus(): string { return $this->deliveryStatus; }
    public function setDeliveryStatus(string $status): self { $this->deliveryStatus = $status; return $this; }
    public function getAttempts(): int { return $this->attempts; }
    public function markAttempt(string $key, array $snapshot): void {
        $this->attempts++;
        $this->deliveryKey = $key;
        $this->snapshot = $snapshot;
        $this->lastAttemptAt = new \DateTimeImmutable();
        $this->deliveryStatus = 'sending';
    }
    public function getDeliveryKey(): ?string { return $this->deliveryKey; }
    public function getLastAttemptAt(): ?\DateTimeImmutable { return $this->lastAttemptAt; }
    public function getSnapshot(): ?array { return $this->snapshot; }

    public function getId(): ?int { return $this->id; }
    public function getTicket(): ?Ticket { return $this->ticket; }
    public function setTicket(?Ticket $value): self { $this->ticket = $value; return $this; }
    public function getRecipient(): string { return $this->recipient; }
    public function setRecipient(string $value): self { $this->recipient = $value; return $this; }
    public function getSentAt(): ?\DateTimeImmutable { return $this->sentAt; }
    public function setSentAt(?\DateTimeImmutable $value): self { $this->sentAt = $value; return $this; }
}
