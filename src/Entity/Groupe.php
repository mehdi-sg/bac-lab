<?php

namespace App\Entity;

use App\Repository\GroupeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
// IMPORTATION POUR LE CONTROLE DE SAISIE CÔTÉ SERVEUR (Comme dans le Workshop)
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GroupeRepository::class)]
class Groupe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    /* --- CONTROLE DE SAISIE : NOM DU GROUPE --- */
    #[Assert\NotBlank(message: "Le nom du groupe est obligatoire.")]
    #[Assert\Length(
        min: 3, 
        max: 100, 
        minMessage: "Le nom du groupe doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom du groupe ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    /* --- CONTROLE DE SAISIE : DESCRIPTION --- */
    #[Assert\NotBlank(message: "La description ne peut pas être vide.")]
    #[Assert\Length(
        min: 10, 
        minMessage: "La description doit être assez explicite (minimum {{ limit }} caractères)."
    )]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $isPublic = true;

    /* --- RELATION : ManyToOne vers Filiere --- */
    #[ORM\ManyToOne(targetEntity: Filiere::class)]
    #[ORM\JoinColumn(nullable: true)] // Peut être nul si c'est un groupe général
    private ?Filiere $filiere = null;

    /* --- RELATION : ManyToOne vers Utilisateur (Le créateur) --- */
    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $createur = null;

    /* --- RELATION : OneToMany vers Message --- */
    #[ORM\OneToMany(mappedBy: 'groupe', targetEntity: Message::class, cascade: ['remove'])]
    private Collection $messages;

    /* --- RELATION : OneToMany vers MembreGroupe (Gestion des rôles) --- */
    #[ORM\OneToMany(mappedBy: 'groupe', targetEntity: MembreGroupe::class, cascade: ['all'], orphanRemoval: true)]
    private Collection $membres;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->membres = new ArrayCollection();
    }

    // ======================================================
    // GETTERS & SETTERS
    // ======================================================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function isPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;
        return $this;
    }

    public function getFiliere(): ?Filiere
    {
        return $this->filiere;
    }

    public function setFiliere(?Filiere $filiere): self
    {
        $this->filiere = $filiere;
        return $this;
    }

    public function getCreateur(): ?Utilisateur
    {
        return $this->createur;
    }

    public function setCreateur(?Utilisateur $createur): self
    {
        $this->createur = $createur;
        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    /**
     * @return Collection<int, MembreGroupe>
     */
    public function getMembres(): Collection
    {
        return $this->membres;
    }
}