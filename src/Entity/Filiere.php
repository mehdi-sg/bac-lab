<?php
// src/Entity/Filiere.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\FiliereRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FiliereRepository::class)]
#[ORM\Table(name: 'filiere')]
class Filiere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Le nom de la filière est obligatoire.")]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Le niveau est obligatoire.")]
    #[Assert\Choice(
        choices: ['Bac', 'Autre'],
        message: "Le niveau doit être Bac ou Autre."
    )]
    private ?string $niveau = null;

    #[ORM\Column(type: 'boolean')]
    private bool $actif = true;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->actif = true;
    }

    // Getters & Setters
    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getNiveau(): ?string { return $this->niveau; }
    public function setNiveau(string $niveau): self { $this->niveau = $niveau; return $this; }

    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $actif): self { $this->actif = $actif; return $this; }

    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }
}
