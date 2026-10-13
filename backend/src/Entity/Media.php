<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[ORM\Table(name: 'media')]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $path = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $alt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[Vich\UploadableField(mapping: 'media', fileNameProperty: 'path')]
    #[Assert\File(
        maxSize: '100M',
        extensions: ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'mp4'],
    )]
    private ?File $imageFile = null;

    #[ORM\ManyToOne(inversedBy: 'media')]
    #[ORM\JoinColumn(name: 'gallery_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Gallery $gallery = null;

    #[ORM\ManyToOne(inversedBy: 'media')]
    #[ORM\JoinColumn(name: 'group_page_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?GroupPage $groupPage = null;

    #[ORM\ManyToOne(inversedBy: 'media')]
    #[ORM\JoinColumn(name: 'merch_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Merch $merch = null;

    #[ORM\ManyToOne(inversedBy: 'media')]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Event $event = null;

    #[ORM\ManyToOne(inversedBy: 'media')]
    #[ORM\JoinColumn(name: 'post_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Post $post = null;

    public function __toString(): string
    {
        return $this->alt ?? $this->path ?? ('Media #'.$this->id);
    }

    public function getId(): ?int { return $this->id; }

    public function getPath(): ?string { return $this->path; }

    public function setPath(?string $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function getExtension(): ?string
    {
        if (!$this->path) {
            return null;
        }

        $extension = pathinfo($this->path, PATHINFO_EXTENSION);

        return $extension ? strtolower($extension) : null;
    }

    public function isVideo(): bool
    {
        return in_array($this->getExtension(), ['mp4'], true);
    }

    public function isImage(): bool
    {
        return in_array($this->getExtension(), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'], true);
    }

    public function getMimeType(): string
    {
        return match ($this->getExtension()) {
            'mp4' => 'video/mp4',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'avif' => 'image/avif',
            default => 'application/octet-stream',
        };
    }

    public function getAlt(): ?string { return $this->alt; }

    public function setAlt(?string $alt): self
    {
        $this->alt = $alt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): self
    {
        $this->imageFile = $imageFile;

        if ($imageFile !== null) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getGallery(): ?Gallery { return $this->gallery; }

    public function setGallery(?Gallery $gallery): self
    {
        $this->gallery = $gallery;
        return $this;
    }

    public function getGroupPage(): ?GroupPage { return $this->groupPage; }

    public function setGroupPage(?GroupPage $groupPage): self
    {
        $this->groupPage = $groupPage;
        return $this;
    }

    public function getMerch(): ?Merch
    {
        return $this->merch;
    }

    public function setMerch(?Merch $merch): self
    {
        $this->merch = $merch;
        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;
        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;
        return $this;
    }
}
