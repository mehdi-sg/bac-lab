<?php

namespace App\Entity;

use App\Repository\MembreGroupeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
// IMPORTATION POUR LE CONTROLE DE SAISIE CÔTÉ SERVEUR
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MembreGroupeRepository::class)]
class MembreGroupe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /* --- RELATION : ManyToOne vers Utilisateur --- */
    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "L'utilisateur est obligatoire.")]
    private ?Utilisateur $utilisateur = null;

    /* --- RELATION : ManyToOne vers Groupe --- */
    #[ORM\ManyToOne(targetEntity: Groupe::class, inversedBy: 'membres')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Le groupe est obligatoire.")]
    private ?Groupe $groupe = null;

    /* --- CONTROLE DE SAISIE : ROLE (Enum simulé par Choice) --- */
    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Le rôle du membre est obligatoire.")]
    #[Assert\Choice(
        choices: ['MEMBRE', 'ANIMATEUR', 'ADMIN'], 
        message: "Le rôle choisi n'est pas valide (MEMBRE, ANIMATEUR ou ADMIN)."
    )]
    private ?string $roleMembre = 'MEMBRE';

    /* --- DATE DE JONCTION --- */
    #[ORM\Column]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $dateJoint = null;

    public function __construct()
    {
        // Initialisation automatique de la date lors de l'ajout du membre
        $this->dateJoint = new \DateTimeImmutable();
    }

    // ======================================================
    // GETTERS & SETTERS
    // ======================================================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getGroupe(): ?Groupe
    {
        return $this->groupe;
    }

    public function setGroupe(?Groupe $groupe): self
    {
        $this->groupe = $groupe;
        return $this;
    }

    public function getRoleMembre(): ?string
    {
        return $this->roleMembre;
    }

    public function setRoleMembre(string $roleMembre): self
    {
        $this->roleMembre = $roleMembre;
        return $this;
    }

    public function getDateJoint(): ?\DateTimeImmutable
    {
        return $this->dateJoint;
    }

    public function setDateJoint(\DateTimeImmutable $dateJoint): self
    {
        $this->dateJoint = $dateJoint;
        return $this;
    }
}