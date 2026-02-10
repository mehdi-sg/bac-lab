<?php

namespace App\Entity;

use App\Repository\GroupeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GroupeRepository::class)]
#[ORM\Table(name: 'groupe')]
class Groupe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le nom du groupe est obligatoire.")]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: "Le nom du groupe doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom du groupe ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description ne peut pas être vide.")]
    #[Assert\Length(
        min: 10,
        minMessage: "La description doit être assez explicite (minimum {{ limit }} caractères)."
    )]
    private ?string $description = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isPublic = true;

    #[ORM\ManyToOne(targetEntity: Filiere::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Filiere $filiere = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Le créateur est obligatoire.")]
    private ?Utilisateur $createur = null;

    #[ORM\OneToMany(mappedBy: 'groupe', targetEntity: Message::class, orphanRemoval: true, cascade: ['remove'])]
    private Collection $messages;

    #[ORM\OneToMany(mappedBy: 'groupe', targetEntity: MembreGroupe::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $membres;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->membres = new ArrayCollection();
    }

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

    public function isPublic(): bool
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

    public function setCreateur(Utilisateur $createur): self
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

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setGroupe($this);
        }
        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            if ($message->getGroupe() === $this) {
                $message->setGroupe(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, MembreGroupe>
     */
    public function getMembres(): Collection
    {
        return $this->membres;
    }

    public function addMembre(MembreGroupe $membre): self
    {
        if (!$this->membres->contains($membre)) {
            $this->membres->add($membre);
            $membre->setGroupe($this);
        }
        return $this;
    }

    public function removeMembre(MembreGroupe $membre): self
    {
        if ($this->membres->removeElement($membre)) {
            if ($membre->getGroupe() === $this) {
                $membre->setGroupe(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return (string) ($this->nom ?? '');
    }
}
