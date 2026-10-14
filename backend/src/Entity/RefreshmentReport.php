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

    public function getId(): ?int { return $this->id; }
    public function getTicket(): ?Ticket { return $this->ticket; }
    public function setTicket(?Ticket $value): self { $this->ticket = $value; return $this; }
    public function getRecipient(): string { return $this->recipient; }
    public function setRecipient(string $value): self { $this->recipient = $value; return $this; }
    public function getSentAt(): ?\DateTimeImmutable { return $this->sentAt; }
    public function setSentAt(?\DateTimeImmutable $value): self { $this->sentAt = $value; return $this; }
}
