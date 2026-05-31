<?php

namespace App\Entity;

use App\Repository\ChantRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: ChantRepository::class)]
#[ORM\Table(name: 'chant')]
class Chant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $title = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $lyrics = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $audioPath = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[Vich\UploadableField(mapping: 'chant_audio', fileNameProperty: 'audioPath')]
    private ?File $audioFile = null;

    public function __toString(): string
    {
        return $this->title ?? ('Chant #'.$this->id);
    }

    public function getId(): ?int { return $this->id; }

    public function getTitle(): ?string { return $this->title; }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getLyrics(): ?string { return $this->lyrics; }

    public function setLyrics(?string $lyrics): self
    {
        $this->lyrics = $lyrics;
        return $this;
    }

    public function getAudioPath(): ?string { return $this->audioPath; }

    public function setAudioPath(?string $audioPath): self
    {
        $this->audioPath = $audioPath;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getAudioFile(): ?File
    {
        return $this->audioFile;
    }

    public function setAudioFile(?File $audioFile): self
    {
        $this->audioFile = $audioFile;

        if ($audioFile !== null) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }
}
