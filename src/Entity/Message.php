<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
// IMPORTATION INDISPENSABLE POUR LE CONTROLE DE SAISIE CÔTÉ SERVEUR
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    /* --- CONTROLE DE SAISIE : CONTENU DU MESSAGE --- */
    #[Assert\NotBlank(message: "Le contenu du message ne peut pas être vide.")]
    #[Assert\Length(
        max: 2000,
        maxMessage: "Un message ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $contenu = null;

    #[ORM\Column(length: 10)]
    /* --- CONTROLE DE SAISIE : TYPE DE MESSAGE --- */
    #[Assert\NotBlank]
    #[Assert\Choice(
        choices: ['TEXTE', 'IMAGE', 'PDF'],
        message: "Le type de message doit être soit TEXTE, IMAGE ou PDF."
    )]
    private ?string $typeMessage = 'TEXTE';

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /* --- RELATION "ADVANCED" : AUTO-RÉFÉRENCE (Parent Message) --- */
    /* Permet de répondre à un message spécifique (parent_message_id) */
    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?self $parentMessage = null;

    /* --- RELATION : ManyToOne vers Utilisateur (L'expéditeur) --- */
    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "L'expéditeur est obligatoire.")]
    private ?Utilisateur $expediteur = null;

    /* --- RELATION : ManyToOne vers Groupe --- */
    #[ORM\ManyToOne(targetEntity: Groupe::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Le groupe de destination est obligatoire.")]
    private ?Groupe $groupe = null;

    public function __construct()
    {
        // Initialisation automatique de la date de création
        $this->createdAt = new \DateTimeImmutable();
    }

    // ======================================================
    // GETTERS & SETTERS
    // ======================================================

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

    public function getTypeMessage(): ?string
    {
        return $this->typeMessage;
    }

    public function setTypeMessage(string $typeMessage): self
    {
        $this->typeMessage = $typeMessage;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getParentMessage(): ?self
    {
        return $this->parentMessage;
    }

    public function setParentMessage(?self $parentMessage): self
    {
        $this->parentMessage = $parentMessage;
        return $this;
    }

    public function getExpediteur(): ?Utilisateur
    {
        return $this->expediteur;
    }

    public function setExpediteur(?Utilisateur $expediteur): self
    {
        $this->expediteur = $expediteur;
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
}