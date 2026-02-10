<?php

namespace App\Entity;

use App\Repository\CommentaireRessourceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentaireRessourceRepository::class)]
class CommentaireRessource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"text")]
    #[Assert\NotBlank(message: 'Le commentaire ne peut pas être vide')]
    #[Assert\Length(min: 10, max: 1000, minMessage: 'Le commentaire doit contenir au moins {{ limit }} caractères', maxMessage: 'Le commentaire ne peut pas dépasser {{ limit }} caractères')]
    private ?string $contenu = null;

    #[ORM\Column(type:"datetime")]
    private ?\DateTimeInterface $dateCommentaire = null;

    #[ORM\Column(type:"boolean")]
    private bool $estSignale = false;

    #[ORM\ManyToOne(targetEntity: Ressource::class, inversedBy: "commentaires")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ressource $ressource = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getDateCommentaire(): ?\DateTimeInterface
    {
        return $this->dateCommentaire;
    }

    public function setDateCommentaire(\DateTimeInterface $dateCommentaire): self
    {
        $this->dateCommentaire = $dateCommentaire;
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
}
