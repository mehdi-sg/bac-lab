<?php

namespace App\Entity;

use App\Repository\FicheVersionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FicheVersionRepository::class)]
class FicheVersion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $editedAt = null;

    #[ORM\Column(length: 255)]
    private ?string $editorName = null;

    #[ORM\ManyToOne(inversedBy: 'ficheVersions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Fiche $fiche = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getEditedAt(): ?\DateTimeImmutable
    {
        return $this->editedAt;
    }

    public function setEditedAt(\DateTimeImmutable $editedAt): static
    {
        $this->editedAt = $editedAt;

        return $this;
    }

    public function getEditorName(): ?string
    {
        return $this->editorName;
    }

    public function setEditorName(string $editorName): static
    {
        $this->editorName = $editorName;

        return $this;
    }

    public function getFiche(): ?Fiche
    {
        return $this->fiche;
    }

    public function setFiche(?Fiche $fiche): static
    {
        $this->fiche = $fiche;

        return $this;
    }
}
