<?php

namespace App\Entity;

use App\Repository\MembreGroupeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MembreGroupeRepository::class)]
#[ORM\Table(
    name: 'membre_groupe',
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'uniq_membre_groupe', columns: ['utilisateur_id', 'groupe_id'])
    ]
)]
class MembreGroupe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // --- RELATION : ManyToOne vers Utilisateur ---
    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull(message: "L'utilisateur est obligatoire.")]
    private ?Utilisateur $utilisateur = null;

    // --- RELATION : ManyToOne vers Groupe ---
    #[ORM\ManyToOne(targetEntity: Groupe::class, inversedBy: 'membres')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull(message: "Le groupe est obligatoire.")]
    private ?Groupe $groupe = null;

    // --- ROLE ---
    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Le rôle du membre est obligatoire.")]
    #[Assert\Choice(
        choices: ['MEMBRE', 'ANIMATEUR', 'ADMIN'],
        message: "Le rôle choisi n'est pas valide (MEMBRE, ANIMATEUR ou ADMIN)."
    )]
    private string $roleMembre = 'MEMBRE';

    // --- DATE DE JONCTION ---
    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotNull(message: "La date de jonction est obligatoire.")]
    private ?\DateTimeImmutable $dateJoint = null;

    public function __construct()
    {
        $this->dateJoint = new \DateTimeImmutable();
    }

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

    public function getRoleMembre(): string
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
