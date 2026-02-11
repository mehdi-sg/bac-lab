<?php

namespace App\Entity;

use App\Repository\EvaluationRessourceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: EvaluationRessourceRepository::class)]
#[UniqueEntity(
    fields: ['ressource', 'utilisateur'],
    message: 'Vous avez déjà évalué cette ressource.'
)]
class EvaluationRessource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Rating (from EvaluationRessource)
    #[ORM\Column(nullable: true)]
    #[Assert\Range(
        min: 1,
        max: 5,
        notInRangeMessage: 'La note doit être entre {{ min }} et {{ max }}'
    )]
    private ?int $note = null;

    // Comment (from CommentaireRessource)
    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(
        min: 10, 
        max: 1000, 
        minMessage: 'Le commentaire doit contenir au moins {{ limit }} caractères', 
        maxMessage: 'Le commentaire ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $commentaire = null;

    // Favorite flag (from FavoriRessource)
    #[ORM\Column(type: 'boolean')]
    private bool $estFavori = false;

    // Report flag (from CommentaireRessource)
    #[ORM\Column(type: 'boolean')]
    private bool $estSignale = false;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateEvaluation = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateCommentaire = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateFavori = null;

    #[ORM\ManyToOne(inversedBy: 'evaluations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ressource $ressource = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    public function __construct()
    {
        $this->dateEvaluation = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(?int $note): self
    {
        $this->note = $note;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;
        if ($commentaire && !$this->dateCommentaire) {
            $this->dateCommentaire = new \DateTime();
        }
        return $this;
    }

    public function isEstFavori(): bool
    {
        return $this->estFavori;
    }

    public function setEstFavori(bool $estFavori): self
    {
        $this->estFavori = $estFavori;
        if ($estFavori && !$this->dateFavori) {
            $this->dateFavori = new \DateTime();
        } elseif (!$estFavori) {
            $this->dateFavori = null;
        }
        return $this;
    }

    public function isEstSignale(): bool
    {
        return $this->estSignale;
    }

    public function setEstSignale(bool $estSignale): self
    {
        $this->estSignale = $estSignale;
        return $this;
    }

    public function getDateEvaluation(): ?\DateTimeInterface
    {
        return $this->dateEvaluation;
    }

    public function setDateEvaluation(\DateTimeInterface $dateEvaluation): self
    {
        $this->dateEvaluation = $dateEvaluation;
        return $this;
    }

    public function getDateCommentaire(): ?\DateTimeInterface
    {
        return $this->dateCommentaire;
    }

    public function setDateCommentaire(?\DateTimeInterface $dateCommentaire): self
    {
        $this->dateCommentaire = $dateCommentaire;
        return $this;
    }

    public function getDateFavori(): ?\DateTimeInterface
    {
        return $this->dateFavori;
    }

    public function setDateFavori(?\DateTimeInterface $dateFavori): self
    {
        $this->dateFavori = $dateFavori;
        return $this;
    }

    public function getRessource(): ?Ressource
    {
        return $this->ressource;
    }

    public function setRessource(?Ressource $ressource): self
    {
        $this->ressource = $ressource;
        return $this;
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

    /**
     * Check if this evaluation has a rating
     */
    public function hasRating(): bool
    {
        return $this->note !== null;
    }

    /**
     * Check if this evaluation has a comment
     */
    public function hasComment(): bool
    {
        return $this->commentaire !== null && trim($this->commentaire) !== '';
    }
}

