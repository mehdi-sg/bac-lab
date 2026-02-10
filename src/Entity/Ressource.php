<?php

namespace App\Entity;

use App\Repository\RessourceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
    #[Assert\Choice(
        choices: ['PDF', 'VIDEO', 'LIEN'],
        message: 'Type de fichier invalide'
    )]
    private ?string $typeFichier = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageCouverture = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tags = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero(message: 'La taille doit être positive')]
    private ?int $tailleFichier = null;

    #[ORM\Column(options: ['default' => 0])]
    private int $nombreVues = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $nombreTelechargements = 0;

    #[ORM\Column(type: 'decimal', precision: 3, scale: 2, options: ['default' => 0])]
    private float $noteMoyenne = 0.00;

    #[ORM\Column(length: 50, options: ['default' => 'EN_ATTENTE'])]
    #[Assert\Choice(['EN_ATTENTE', 'VALIDEE', 'REJETEE'])]
    private string $statut = 'EN_ATTENTE';

    #[ORM\Column]
    private \DateTimeInterface $dateAjout;

    #[ORM\Column(options: ['default' => true])]
    private bool $estActive = true;


    #[ORM\ManyToOne(inversedBy: 'ressources')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeRessource $typeRessource = null;

    public function getTypeRessource(): ?TypeRessource { return $this->typeRessource; }
    public function setTypeRessource(?TypeRessource $typeRessource): self { $this->typeRessource = $typeRessource; return $this; }
 

    public function __construct()
    {
        $this->dateAjout = new \DateTime();
    }

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

    public function getTailleFichier(): ?int
    {
        return $this->tailleFichier;
    }

    public function setTailleFichier(?int $tailleFichier): self
    {
        $this->tailleFichier = $tailleFichier;
        return $this;
    }

    public function incrementVues(): void
    {
        $this->nombreVues++;
    }

    public function incrementTelechargements(): void
    {
        $this->nombreTelechargements++;
    }
}
