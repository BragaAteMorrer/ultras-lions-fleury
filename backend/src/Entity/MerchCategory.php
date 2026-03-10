<?php

namespace App\Entity;

use App\Repository\MerchCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MerchCategoryRepository::class)]
#[ORM\Table(name: 'merch_category')]
#[ORM\UniqueConstraint(name: 'uniq_merch_category_slug', columns: ['slug'])]
class MerchCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\Column(length: 150)]
    private ?string $slug = null;

    /** @var Collection<int, Merch> */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Merch::class)]
    private Collection $merch;

    public function __construct()
    {
        $this->merch = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        if (!$this->slug) {
            $this->slug = self::slugify($name);
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    /** @return Collection<int, Merch> */
    public function getMerch(): Collection
    {
        return $this->merch;
    }

    public function addMerch(Merch $merch): self
    {
        if (!$this->merch->contains($merch)) {
            $this->merch->add($merch);
            $merch->setCategory($this);
        }

        return $this;
    }

    public function removeMerch(Merch $merch): self
    {
        if ($this->merch->removeElement($merch) && $merch->getCategory() === $this) {
            $merch->setCategory(null);
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

