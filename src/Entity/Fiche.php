<?php

namespace App\Entity;

use App\Repository\FicheRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FicheRepository::class)]
class Fiche
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 20)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updatedAt = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $isPublic = false;

    /**
     * @var Collection<int, FicheVersion>
     */
    #[ORM\OneToMany(targetEntity: FicheVersion::class, mappedBy: 'fiche')]
    private Collection $ficheVersions;

    /**
     * @var Collection<int, FicheModerateur>
     */
    #[ORM\OneToMany(targetEntity: FicheModerateur::class, mappedBy: 'fiche', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private Collection $moderateurs;

    /**
     * @var Collection<int, FicheJoinRequest>
     */
    #[ORM\OneToMany(targetEntity: FicheJoinRequest::class, mappedBy: 'fiche', cascade: ['persist', 'remove'])]
    private Collection $joinRequests;

    /**
     * @var Collection<int, FicheFavori>
     */
    #[ORM\OneToMany(targetEntity: FicheFavori::class, mappedBy: 'fiche', cascade: ['persist', 'remove'])]
    private Collection $favoris;

    #[ORM\ManyToOne(targetEntity: Filiere::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Filiere $filiere = null;

    public function __construct()

    {
        $this->ficheVersions = new ArrayCollection();
        $this->moderateurs = new ArrayCollection();
        $this->joinRequests = new ArrayCollection();
        $this->favoris = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function isPublic(): bool { return $this->isPublic; }
    public function setIsPublic(bool $isPublic): static { $this->isPublic = $isPublic; return $this; }
    
    /**
     * @return Collection<int, FicheVersion>
     */
    public function getFicheVersions(): Collection
    {
        return $this->ficheVersions;
    }

    public function addFicheVersion(FicheVersion $ficheVersion): static
    {
        if (!$this->ficheVersions->contains($ficheVersion)) {
            $this->ficheVersions->add($ficheVersion);
            $ficheVersion->setFiche($this);
        }

        return $this;
    }

    public function removeFicheVersion(FicheVersion $ficheVersion): static
    {
        if ($this->ficheVersions->removeElement($ficheVersion)) {
            // set the owning side to null (unless already changed)
            if ($ficheVersion->getFiche() === $this) {
                $ficheVersion->setFiche(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FicheModerateur>
     */
    public function getModerateurs(): Collection
    {
        return $this->moderateurs;
    }

    public function addModerateur(FicheModerateur $moderateur): static
    {
        if (!$this->moderateurs->contains($moderateur)) {
            $this->moderateurs->add($moderateur);
            $moderateur->setFiche($this);
        }

        return $this;
    }

    public function removeModerateur(FicheModerateur $moderateur): static
    {
        if ($this->moderateurs->removeElement($moderateur)) {
            if ($moderateur->getFiche() === $this) {
                $moderateur->setFiche(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FicheJoinRequest>
     */
    public function getJoinRequests(): Collection
    {
        return $this->joinRequests;
    }

    public function addJoinRequest(FicheJoinRequest $joinRequest): static
    {
        if (!$this->joinRequests->contains($joinRequest)) {
            $this->joinRequests->add($joinRequest);
            $joinRequest->setFiche($this);
        }

        return $this;
    }

    public function removeJoinRequest(FicheJoinRequest $joinRequest): static
    {
        if ($this->joinRequests->removeElement($joinRequest)) {
            if ($joinRequest->getFiche() === $this) {
                $joinRequest->setFiche(null);
            }
        }

        return $this;
    }

    /**
     * Check if user is moderateur of this fiche
     */
    public function isModerateur(Utilisateur $utilisateur): bool
    {
        foreach ($this->moderateurs as $moderateur) {
            if ($moderateur->getUtilisateur() === $utilisateur) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Collection<int, FicheFavori>
     */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(FicheFavori $favori): static
    {
        if (!$this->favoris->contains($favori)) {
            $this->favoris->add($favori);
            $favori->setFiche($this);
        }

        return $this;
    }

    public function removeFavori(FicheFavori $favori): static
    {
        if ($this->favoris->removeElement($favori)) {
            if ($favori->getFiche() === $this) {
                $favori->setFiche(null);
            }
        }

        return $this;
    }

    /**
     * Check if fiche is favorited by a user
     */
    public function isFavoritedBy(Utilisateur $utilisateur): bool
    {
        foreach ($this->favoris as $favori) {
            if ($favori->getUtilisateur() === $utilisateur) {
                return true;
            }
        }

        return false;
    }

    public function getFiliere(): ?Filiere

    {
        return $this->filiere;
    }

    public function setFiliere(?Filiere $filiere): static
    {
        $this->filiere = $filiere;
        return $this;
    }
}
