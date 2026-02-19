<?php

namespace App\Service;

use App\Entity\Utilisateur;
use App\Repository\UserSubjectInterestRepository;

/**
 * Service for calculating user engagement and interest scores
 */
class EngagementScoringService
{
    public function __construct(
        private UserSubjectInterestRepository $userSubjectInterestRepository
    ) {}

    /**
     * Compute subject interest score for a user
     */
    public function computeSubjectInterest(Utilisateur $user, string $subjectCode): float
    {
        $interest = $this->userSubjectInterestRepository->findOneBy([
            'user' => $user,
            'subjectCode' => $subjectCode
        ]);

        if (!$interest) {
            return 0.0;
        }

        return $interest->getInterestScore() ?? 0.0;
    }

    /**
     * Compute interest scores for multiple subjects
     */
    public function computeSubjectsInterest(Utilisateur $user, array $subjectCodes): array
    {
        if (empty($subjectCodes)) {
            return [];
        }

        $interests = $this->userSubjectInterestRepository->findUserInterestsBySubjects($user, $subjectCodes);
        $scores = [];

        foreach ($subjectCodes as $code) {
            $scores[$code] = isset($interests[$code]) ? $interests[$code]->getInterestScore() : 0.0;
        }

        return $scores;
    }

    /**
     * Compute global engagement score for a user
     */
    public function computeGlobalEngagement(Utilisateur $user): float
    {
        return $this->userSubjectInterestRepository->getUserGlobalEngagement($user);
    }

    /**
     * Update engagement metrics for a user and subject
     */
    public function updateEngagementMetrics(
        Utilisateur $user, 
        string $subjectCode, 
        array $metrics
    ): void {
        $this->userSubjectInterestRepository->updateEngagementMetrics($user, $subjectCode, $metrics);
    }

    /**
     * Get user's engagement statistics
     */
    public function getUserEngagementStats(Utilisateur $user): array
    {
        return $this->userSubjectInterestRepository->getUserEngagementStats($user);
    }

    /**
     * Get top subjects by interest for a user
     */
    public function getTopSubjectsByInterest(Utilisateur $user, int $limit = 5): array
    {
        $interests = $this->userSubjectInterestRepository->getTopSubjectsByInterest($user, $limit);
        
        $result = [];
        foreach ($interests as $interest) {
            $result[] = [
                'subjectCode' => $interest->getSubjectCode(),
                'interestScore' => $interest->getInterestScore(),
                'metrics' => [
                    'resourceViews' => $interest->getResourceViews(),
                    'downloads' => $interest->getDownloads(),
                    'favorites' => $interest->getFavorites(),
                    'quizAttempts' => $interest->getQuizAttempts(),
                    'quizAverage' => $interest->getQuizAverageScore(),
                    'timeSpent' => $interest->getTimeSpentMinutes()
                ]
            ];
        }

        return $result;
    }

    /**
     * Calculate interest fit for a program based on formula subjects
     */
    public function calculateInterestFit(Utilisateur $user, array $formulaSubjects): float
    {
        if (empty($formulaSubjects)) {
            return 0.0;
        }

        // Remove FG and MG from subjects as they're not actual subjects
        $actualSubjects = array_filter($formulaSubjects, function($subject) {
            return !in_array($subject, ['FG', 'MG', 'ALL']);
        });

        if (empty($actualSubjects)) {
            return 0.0;
        }

        $subjectInterests = $this->computeSubjectsInterest($user, $actualSubjects);
        
        // Calculate average interest score
        $totalInterest = array_sum($subjectInterests);
        $averageInterest = $totalInterest / count($actualSubjects);

        return $averageInterest;
    }

    /**
     * Simulate engagement metrics for testing/demo purposes
     */
    public function simulateEngagementData(Utilisateur $user): void
    {
        // Subject codes that might have engagement
        $subjects = ['M', 'SP', 'SVT', 'F', 'Ang', 'A', 'PH', 'HG', 'Ec', 'Ge'];
        
        foreach ($subjects as $subject) {
            // Generate random but realistic engagement data
            $metrics = [
                'resourceViews' => rand(5, 50),
                'downloads' => rand(1, 20),
                'favorites' => rand(0, 10),
                'comments' => rand(0, 5),
                'quizScore' => rand(8, 18) + (rand(0, 100) / 100), // 8.0 to 18.99
                'timeSpent' => rand(30, 300) // 30 minutes to 5 hours
            ];

            $this->updateEngagementMetrics($user, $subject, $metrics);
        }
    }

    /**
     * Get engagement level description
     */
    public function getEngagementLevelDescription(float $score): array
    {
        if ($score >= 0.8) {
            return [
                'level' => 'Très élevé',
                'color' => 'success',
                'description' => 'Engagement exceptionnel avec cette matière'
            ];
        } elseif ($score >= 0.6) {
            return [
                'level' => 'Élevé',
                'color' => 'info',
                'description' => 'Bon niveau d\'engagement'
            ];
        } elseif ($score >= 0.4) {
            return [
                'level' => 'Moyen',
                'color' => 'warning',
                'description' => 'Engagement modéré'
            ];
        } elseif ($score >= 0.2) {
            return [
                'level' => 'Faible',
                'color' => 'secondary',
                'description' => 'Engagement limité'
            ];
        } else {
            return [
                'level' => 'Très faible',
                'color' => 'danger',
                'description' => 'Peu ou pas d\'engagement'
            ];
        }
    }
}