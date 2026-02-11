<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Profil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private string $nom;

    #[ORM\Column(length: 50)]
    private string $prenom;

    #[ORM\Column(length: 30)]
    private string $niveau;

    #[ORM\Column(length: 50)]
    private string $gouvernorat;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $filiere = null;

    #[ORM\OneToOne(inversedBy: 'profil')]
    #[ORM\JoinColumn(nullable: false)]
    private Utilisateur $utilisateur;

    public function getId(): ?int { return $this->id; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getPrenom(): string { return $this->prenom; }
    public function setPrenom(string $prenom): self { $this->prenom = $prenom; return $this; }

    public function getNiveau(): string { return $this->niveau; }
    public function setNiveau(string $niveau): self { $this->niveau = $niveau; return $this; }

    public function getGouvernorat(): string { return $this->gouvernorat; }
    public function setGouvernorat(string $gouvernorat): self { $this->gouvernorat = $gouvernorat; return $this; }

    public function getDateNaissance(): ?\DateTimeInterface { return $this->dateNaissance; }
    public function setDateNaissance(?\DateTimeInterface $date): self { $this->dateNaissance = $date; return $this; }

    public function getFiliere(): ?string { return $this->filiere; }
    public function setFiliere(?string $filiere): self { $this->filiere = $filiere; return $this; }

    public function setUtilisateur(Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getUtilisateur(): Utilisateur
    {
        return $this->utilisateur;
    }
}
