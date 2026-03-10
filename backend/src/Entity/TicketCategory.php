<?php

namespace App\Entity;

use App\Repository\TicketCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketCategoryRepository::class)]
#[ORM\Table(name: 'ticket_category')]
#[ORM\UniqueConstraint(name: 'uniq_ticket_category_slug', columns: ['slug'])]
class TicketCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\Column(length: 150)]
    private ?string $slug = null;

    /** @var Collection<int, Ticket> */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Ticket::class)]
    private Collection $tickets;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }

    public function setName(string $name): self
    {
        $this->name = $name;

        if (!$this->slug) {
            $this->slug = self::slugify($name);
        }

        return $this;
    }

    public function getSlug(): ?string { return $this->slug; }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    /** @return Collection<int, Ticket> */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setCategory($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->removeElement($ticket) && $ticket->getCategory() === $this) {
            $ticket->setCategory(null);
        }

        return $this;
    }

    private static function slugify(string $value): string
    {
        $value = trim(mb_strtolower($value));
        $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
        $value = preg_replace('/[^a-z0-9]+/i', '-', (string) $value);
        $value = trim((string) $value, '-');

        return $value ?: 'categorie';
    }
}

