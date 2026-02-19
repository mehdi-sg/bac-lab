<?php

namespace App\Entity;

use App\Repository\UserSubjectInterestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSubjectInterestRepository::class)]
#[ORM\Table(name: 'user_subject_interests')]
#[ORM\UniqueConstraint(name: 'user_subject_unique', columns: ['user_id', 'subject_code'])]
#[ORM\Index(columns: ['user_id'], name: 'idx_user')]
#[ORM\Index(columns: ['subject_code'], name: 'idx_subject')]
class UserSubjectInterest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $user = null;

    #[ORM\Column(length: 20)]
    private ?string $subjectCode = null;

    #[ORM\Column(type: 'decimal', precision: 4, scale: 3)]
    private ?string $interestScore = null;

    #[ORM\Column]
    private ?int $resourceViews = 0;

    #[ORM\Column]
    private ?int $downloads = 0;

    #[ORM\Column]
    private ?int $favorites = 0;

    #[ORM\Column]
    private ?int $comments = 0;

    #[ORM\Column(type: 'decimal', precision: 4, scale: 2, nullable: true)]
    private ?string $quizAverageScore = null;

    #[ORM\Column]
    private ?int $quizAttempts = 0;

    #[ORM\Column]
    private ?int $timeSpentMinutes = 0;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Utilisateur
    {
        return $this->user;
    }

    public function setUser(?Utilisateur $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getSubjectCode(): ?string
    {
        return $this->subjectCode;
    }

    public function setSubjectCode(string $subjectCode): static
    {
        $this->subjectCode = $subjectCode;
        return $this;
    }

    public function getInterestScore(): ?float
    {
        return $this->interestScore ? (float) $this->interestScore : null;
    }

    public function setInterestScore(float $interestScore): static
    {
        $this->interestScore = (string) max(0, min(1, $interestScore));
        return $this;
    }

    public function getResourceViews(): ?int
    {
        return $this->resourceViews;
    }

    public function setResourceViews(int $resourceViews): static
    {
        $this->resourceViews = max(0, $resourceViews);
        return $this;
    }

    public function getDownloads(): ?int
    {
        return $this->downloads;
    }

    public function setDownloads(int $downloads): static
    {
        $this->downloads = max(0, $downloads);
        return $this;
    }

    public function getFavorites(): ?int
    {
        return $this->favorites;
    }

    public function setFavorites(int $favorites): static
    {
        $this->favorites = max(0, $favorites);
        return $this;
    }

    public function getComments(): ?int
    {
        return $this->comments;
    }

    public function setComments(int $comments): static
    {
        $this->comments = max(0, $comments);
        return $this;
    }

    public function getQuizAverageScore(): ?float
    {
        return $this->quizAverageScore ? (float) $this->quizAverageScore : null;
    }

    public function setQuizAverageScore(?float $quizAverageScore): static
    {
        $this->quizAverageScore = $quizAverageScore !== null ? (string) max(0, min(20, $quizAverageScore)) : null;
        return $this;
    }

    public function getQuizAttempts(): ?int
    {
        return $this->quizAttempts;
    }

    public function setQuizAttempts(int $quizAttempts): static
    {
        $this->quizAttempts = max(0, $quizAttempts);
        return $this;
    }

    public function getTimeSpentMinutes(): ?int
    {
        return $this->timeSpentMinutes;
    }

    public function setTimeSpentMinutes(int $timeSpentMinutes): static
    {
        $this->timeSpentMinutes = max(0, $timeSpentMinutes);
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Update the interest score based on current metrics
     */
    public function updateInterestScore(): void
    {
        // Normalize values (0-1 scale)
        $quizNormalized = $this->quizAverageScore ? ($this->getQuizAverageScore() / 20) : 0;
        $timeNormalized = min(1, $this->timeSpentMinutes / 300); // 5 hours = max
        $downloadsNormalized = min(1, $this->downloads / 50); // 50 downloads = max
        $favoritesNormalized = min(1, $this->favorites / 20); // 20 favorites = max
        
        // Calculate weighted interest score
        $interestScore = 
            0.4 * $quizNormalized +
            0.3 * $timeNormalized +
            0.2 * $downloadsNormalized +
            0.1 * $favoritesNormalized;
        
        $this->setInterestScore($interestScore);
        $this->updatedAt = new \DateTimeImmutable();
    }
}