<?php

namespace App\Entity;

use App\Repository\QuizRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_quiz')]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    private ?string $niveau = null;

    #[ORM\Column]
    private ?int $duree = null;

    #[ORM\Column(name: 'nb_questions')]
    private ?int $nbQuestions = null;


    #[ORM\Column(name: 'date_creation', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column]
    private ?bool $etat = null;

    #[ORM\ManyToOne(targetEntity: Chapitre::class)]
    #[ORM\JoinColumn(name: 'id_chapitre', referencedColumnName: 'id_chapitre', nullable: false)]
    private ?Chapitre $chapitre = null;

    #[ORM\ManyToOne(targetEntity: Matiere::class, inversedBy: 'questions')]
#[ORM\JoinColumn(name: 'id_matiere', referencedColumnName: 'id_matiere', nullable: false)]
private ?Matiere $matiere = null;


    #[ORM\OneToMany(
        mappedBy: 'quiz',
        targetEntity: Question::class,
        cascade: ['remove'],
        orphanRemoval: true
    )]
    private Collection $questions;
    
    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }
    
    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    // Getters et Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getChapitre(): ?Chapitre
    {
        return $this->chapitre;
    }

    public function setChapitre(?Chapitre $chapitre): static
    {
        $this->chapitre = $chapitre;
        return $this;
    }

    public function getMatiere(): ?Matiere
    {
        return $this->matiere;
    }

    public function setMatiere(?Matiere $matiere): static
    {
        $this->matiere = $matiere;
        return $this;
    }


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(string $niveau): static
    {
        $this->niveau = $niveau;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getNbQuestions(): ?int
    {
        return $this->nbQuestions;
    }

    public function setNbQuestions(int $nbQuestions): static
    {
        $this->nbQuestions = $nbQuestions;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function isEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): static
    {
        $this->etat = $etat;

        return $this;
    }
}
