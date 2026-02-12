<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Profil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Length(max: 50, maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères.")]
    private string $nom;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire.")]
    #[Assert\Length(max: 50, maxMessage: "Le prénom ne peut pas dépasser {{ limit }} caractères.")]
    private string $prenom;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message: "Le niveau est obligatoire.")]
    #[Assert\Choice(choices: ['1ère année', '2ème année', '3ème année', '4ème année', '5ème année', 'M1', 'M2'], message: "Le niveau doit être 1ère année, 2ème année, 3ème année, 4ème année, 5ème année, M1 ou M2.")]
    private string $niveau;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le gouvernorat est obligatoire.")]
    #[Assert\Length(max: 50, maxMessage: "Le gouvernorat ne peut pas dépasser {{ limit }} caractères.")]
    private string $gouvernorat;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\ManyToOne(targetEntity: Filiere::class)]
    #[ORM\JoinColumn(name: 'filiere_id', referencedColumnName: 'id', nullable: true)]
    private ?Filiere $filiere = null;

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

    public function getFiliere(): ?Filiere { return $this->filiere; }
    public function setFiliere(?Filiere $filiere): self { $this->filiere = $filiere; return $this; }

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
