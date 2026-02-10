<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table(name: 'message')]
#[ORM\Index(name: 'idx_message_groupe_created', columns: ['groupe_id', 'created_at'])]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Le contenu du message ne peut pas être vide.")]
    #[Assert\Length(
        max: 2000,
        maxMessage: "Un message ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $contenu = null;

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank(message: "Le type de message est obligatoire.")]
    #[Assert\Choice(
        choices: ['TEXTE', 'IMAGE', 'PDF'],
        message: "Le type de message doit être soit TEXTE, IMAGE ou PDF."
    )]
    private string $typeMessage = 'TEXTE';

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    // Réponse à un message (parent)
    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(name: 'parent_message_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?self $parentMessage = null;

    // Soft delete
    #[ORM\Column(name: 'deleted_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    // Expéditeur
    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "L'expéditeur est obligatoire.")]
    private ?Utilisateur $expediteur = null;

    // Groupe
    #[ORM\ManyToOne(targetEntity: Groupe::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Le groupe de destination est obligatoire.")]
    private ?Groupe $groupe = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->typeMessage = 'TEXTE';
    }

    // =======================
    // Getters / Setters
    // =======================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(?string $contenu): self
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getTypeMessage(): string
    {
        return $this->typeMessage;
    }

    public function setTypeMessage(string $typeMessage): self
    {
        $this->typeMessage = $typeMessage;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
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

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }
}
