<?php

namespace App\Entity;

use App\Repository\RessourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\DBAL\Types\Types;


#[ORM\Entity(repositoryClass: RessourceRepository::class)]
class Ressource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire')]
    #[Assert\Length(max: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $auteur = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url(message: 'URL invalide')]
    private ?string $urlFichier = null;

    #[ORM\Column(length: 50)]
    #[Assert\Choice(choices: ['PDF', 'VIDEO', 'LIEN'], message: 'Type de fichier invalide')]
    private ?string $typeFichier = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageCouverture = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tags = null;
    
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $categorie = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero(message: 'La taille doit être positive')]
    private ?int $tailleFichier = null;

    #[ORM\Column(options: ['default' => 0])]
    private int $nombreVues = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $nombreTelechargements = 0;

    #[ORM\Column(type: 'decimal', precision: 3, scale: 2, options: ['default' => '0.00'])]
    private string $noteMoyenne = '0.00';

    #[ORM\Column(length: 50, options: ['default' => 'EN_ATTENTE'])]
    #[Assert\Choice(['EN_ATTENTE', 'VALIDEE', 'REJETEE'])]
    private string $statut = 'EN_ATTENTE';

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $dateAjout;

    #[ORM\Column(options: ['default' => true])]
    private bool $estActive = true;

    #[ORM\OneToMany(mappedBy: 'ressource', targetEntity: EvaluationRessource::class, orphanRemoval: true)]
    private Collection $evaluations;

    public function __construct()
    {
        $this->dateAjout = new \DateTime();
        $this->evaluations = new ArrayCollection();
    }

    // --------------------
    // Getters / Setters
    // --------------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    public function setAuteur(?string $auteur): self
    {
        $this->auteur = $auteur;
        return $this;
    }

    public function getUrlFichier(): ?string
    {
        return $this->urlFichier;
    }

    public function setUrlFichier(?string $urlFichier): self
    {
        $this->urlFichier = $urlFichier;
        return $this;
    }

    public function getTypeFichier(): ?string
    {
        return $this->typeFichier;
    }

    public function setTypeFichier(string $typeFichier): self
    {
        $this->typeFichier = $typeFichier;
        return $this;
    }

    public function getImageCouverture(): ?string
    {
        return $this->imageCouverture;
    }

    public function setImageCouverture(?string $imageCouverture): self
    {
        $this->imageCouverture = $imageCouverture;
        return $this;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(?string $tags): self
    {
        $this->tags = $tags;
        return $this;
    }
    
    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(?string $categorie): self
    {
        $this->categorie = $categorie;
        return $this;
    }

    public function getTailleFichier(): ?int
    {
        return $this->tailleFichier;
    }

    public function setTailleFichier(?int $tailleFichier): self
    {
        $this->tailleFichier = $tailleFichier;
        return $this;
    }

    public function getNombreVues(): int
    {
        return $this->nombreVues;
    }

    public function setNombreVues(int $nombreVues): self
    {
        $this->nombreVues = $nombreVues;
        return $this;
    }

    public function getNombreTelechargements(): int
    {
        return $this->nombreTelechargements;
    }

    public function setNombreTelechargements(int $nombreTelechargements): self
    {
        $this->nombreTelechargements = $nombreTelechargements;
        return $this;
    }

    public function getNoteMoyenne(): string
    {
        return $this->noteMoyenne;
    }

    public function setNoteMoyenne(string $noteMoyenne): self
    {
        $this->noteMoyenne = $noteMoyenne;
        return $this;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getDateAjout(): \DateTimeInterface
    {
        return $this->dateAjout;
    }

    public function setDateAjout(\DateTimeInterface $dateAjout): self
    {
        $this->dateAjout = $dateAjout;
        return $this;
    }

    public function isEstActive(): bool
    {
        return $this->estActive;
    }

    public function setEstActive(bool $estActive): self
    {
        $this->estActive = $estActive;
        return $this;
    }

    // --------------------
    // Collections
    // --------------------

    /**
     * @return Collection<int, EvaluationRessource>
     */
    public function getEvaluations(): Collection
    {
        return $this->evaluations;
    }

    public function addEvaluation(EvaluationRessource $evaluation): self
    {
        if (!$this->evaluations->contains($evaluation)) {
            $this->evaluations->add($evaluation);
            $evaluation->setRessource($this);
        }

        return $this;
    }

    public function removeEvaluation(EvaluationRessource $evaluation): self
    {
        if ($this->evaluations->removeElement($evaluation)) {
            if ($evaluation->getRessource() === $this) {
                $evaluation->setRessource(null);
            }
        }

        return $this;
    }
    
    /**
     * Get all comments from evaluations
     * @return Collection<int, EvaluationRessource>
     */
    public function getComments(): Collection
    {
        return $this->evaluations->filter(fn($e) => $e->hasComment());
    }
    
    /**
     * Get all ratings from evaluations
     * @return Collection<int, EvaluationRessource>
     */
    public function getRatings(): Collection
    {
        return $this->evaluations->filter(fn($e) => $e->hasRating());
    }
    
    /**
     * Calculate average rating
     */
    public function calculateAverageRating(): void
    {
        $ratings = $this->getRatings();
        if ($ratings->count() === 0) {
            $this->noteMoyenne = '0.00';
            return;
        }
        
        $sum = 0;
        foreach ($ratings as $evaluation) {
            $sum += $evaluation->getNote();
        }
        
        $average = $sum / $ratings->count();
        $this->noteMoyenne = number_format($average, 2, '.', '');
    }

    // --------------------
    // Helpers
    // --------------------

    public function incrementVues(): void
    {
        $this->nombreVues++;
    }

    public function incrementTelechargements(): void
    {
        $this->nombreTelechargements++;
    }
}
