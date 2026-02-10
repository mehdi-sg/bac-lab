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

    public function __construct()
    {
        $this->ficheVersions = new ArrayCollection();
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
}
