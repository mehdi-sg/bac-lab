<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_question')]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $enonce = null;

    #[ORM\Column(name: 'type_question', length: 20)]
    private ?string $typeQuestion = null;

    #[ORM\Column]
    private ?int $score = null;

    #[ORM\ManyToOne(targetEntity: Quiz::class, inversedBy: 'questions')]
    #[ORM\JoinColumn(name: 'id_quiz', referencedColumnName: 'id_quiz', nullable: false, onDelete: 'CASCADE')]
    private ?Quiz $quiz = null;

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: Choix::class, cascade: ['persist', 'remove'])]
    private Collection $choix;

    public function __construct()
    {
        $this->choix = new ArrayCollection();
    }

    // Getters et Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEnonce(): ?string
    {
        return $this->enonce;
    }

    public function setEnonce(string $enonce): static
    {
        $this->enonce = $enonce;
        return $this;
    }

    public function getTypeQuestion(): ?string
    {
        return $this->typeQuestion;
    }

    public function setTypeQuestion(string $typeQuestion): static
    {
        // Validation: ne définir que si la valeur est valide
        if (in_array($typeQuestion, ['QCM', 'VRAI_FAUX'])) {
            $this->typeQuestion = $typeQuestion;
        }
        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;
        return $this;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): static
    {
        $this->quiz = $quiz;
        return $this;
    }

    /**
     * @return Collection<int, Choix>
     */
    public function getChoix(): Collection
    {
        return $this->choix;
    }

    public function addChoix(Choix $choix): static
    {
        if (!$this->choix->contains($choix)) {
            $this->choix->add($choix);
            $choix->setQuestion($this);
        }

        return $this;
    }

    public function removeChoix(Choix $choix): static
    {
        if ($this->choix->removeElement($choix)) {
            // set the owning side to null (unless already changed)
            if ($choix->getQuestion() === $this) {
                $choix->setQuestion(null);
            }
        }

        return $this;
    }
}

