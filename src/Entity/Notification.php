<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ORM\Table(name: 'notifications')]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(length: 255)]
    private string $type = 'info';

    #[ORM\Column(length: 255)]
    private string $title = '';

    #[ORM\Column(type: 'text')]
    private string $message = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $link = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isRead = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isSeen = false;

    #[ORM\ManyToOne(targetEntity: MembreGroupe::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?MembreGroupe $membre = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->isRead = false;
        $this->isSeen = false;
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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;
        return $this;
    }

    public function isRead(): bool
    {
        return $this->isRead;
    }

    public function setRead(bool $isRead): self
    {
        $this->isRead = $isRead;
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

    public function isSeen(): bool
    {
        return $this->isSeen;
    }

    public function setSeen(bool $isSeen): self
    {
        $this->isSeen = $isSeen;
        return $this;
    }

    public function getMembre(): ?MembreGroupe
    {
        return $this->membre;
    }

    public function setMembre(?MembreGroupe $membre): self
    {
        $this->membre = $membre;
        return $this;
    }
}
