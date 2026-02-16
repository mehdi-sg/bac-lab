<?php

namespace App\Repository;

use App\Entity\UserSubjectInterest;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserSubjectInterest>
 */
class UserSubjectInterestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSubjectInterest::class);
    }

    /**
     * Find user interests by subject codes
     */
    public function findUserInterestsBySubjects(Utilisateur $user, array $subjectCodes): array
    {
        if (empty($subjectCodes)) {
            return [];
        }

        $result = $this->createQueryBuilder('usi')
            ->where('usi.user = :user')
            ->andWhere('usi.subjectCode IN (:subjects)')
            ->setParameter('user', $user)
            ->setParameter('subjects', $subjectCodes)
            ->getQuery()
            ->getResult();

        // Index by subject code for easy access
        $interests = [];
        foreach ($result as $interest) {
            $interests[$interest->getSubjectCode()] = $interest;
        }

        return $interests;
    }

    /**
     * Get or create user subject interest
     */
    public function getOrCreateUserInterest(Utilisateur $user, string $subjectCode): UserSubjectInterest
    {
        $interest = $this->findOneBy([
            'user' => $user,
            'subjectCode' => $subjectCode
        ]);

        if (!$interest) {
            $interest = new UserSubjectInterest();
            $interest->setUser($user);
            $interest->setSubjectCode($subjectCode);
            $interest->setInterestScore(0.0);
            
            $this->getEntityManager()->persist($interest);
        }

        return $interest;
    }

    /**
     * Get user's global engagement score
     */
    public function getUserGlobalEngagement(Utilisateur $user): float
    {
        $result = $this->createQueryBuilder('usi')
            ->select('AVG(usi.interestScore) as avgInterest, COUNT(usi.id) as totalSubjects')
            ->where('usi.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleResult();

        $avgInterest = (float) ($result['avgInterest'] ?? 0);
        $totalSubjects = (int) ($result['totalSubjects'] ?? 0);

        // Boost score based on number of subjects engaged with
        $diversityBonus = min(0.2, $totalSubjects * 0.02); // Max 20% bonus for 10+ subjects
        
        return min(1.0, $avgInterest + $diversityBonus);
    }

    /**
     * Update user engagement metrics
     */
    public function updateEngagementMetrics(
        Utilisateur $user, 
        string $subjectCode, 
        array $metrics
    ): void {
        $interest = $this->getOrCreateUserInterest($user, $subjectCode);

        // Update metrics
        if (isset($metrics['resourceViews'])) {
            $interest->setResourceViews($interest->getResourceViews() + $metrics['resourceViews']);
        }
        
        if (isset($metrics['downloads'])) {
            $interest->setDownloads($interest->getDownloads() + $metrics['downloads']);
        }
        
        if (isset($metrics['favorites'])) {
            $interest->setFavorites($interest->getFavorites() + $metrics['favorites']);
        }
        
        if (isset($metrics['comments'])) {
            $interest->setComments($interest->getComments() + $metrics['comments']);
        }
        
        if (isset($metrics['quizScore'])) {
            $currentAttempts = $interest->getQuizAttempts();
            $currentAvg = $interest->getQuizAverageScore() ?? 0;
            
            // Calculate new average
            $newAvg = (($currentAvg * $currentAttempts) + $metrics['quizScore']) / ($currentAttempts + 1);
            $interest->setQuizAverageScore($newAvg);
            $interest->setQuizAttempts($currentAttempts + 1);
        }
        
        if (isset($metrics['timeSpent'])) {
            $interest->setTimeSpentMinutes($interest->getTimeSpentMinutes() + $metrics['timeSpent']);
        }

        // Recalculate interest score
        $interest->updateInterestScore();
        
        $this->getEntityManager()->flush();
    }

    /**
     * Get top subjects by interest for a user
     */
    public function getTopSubjectsByInterest(Utilisateur $user, int $limit = 5): array
    {
        return $this->createQueryBuilder('usi')
            ->where('usi.user = :user')
            ->setParameter('user', $user)
            ->orderBy('usi.interestScore', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get engagement statistics for a user
     */
    public function getUserEngagementStats(Utilisateur $user): array
    {
        $result = $this->createQueryBuilder('usi')
            ->select('
                COUNT(usi.id) as totalSubjects,
                SUM(usi.resourceViews) as totalViews,
                SUM(usi.downloads) as totalDownloads,
                SUM(usi.favorites) as totalFavorites,
                SUM(usi.comments) as totalComments,
                SUM(usi.quizAttempts) as totalQuizAttempts,
                SUM(usi.timeSpentMinutes) as totalTimeSpent,
                AVG(usi.interestScore) as avgInterest
            ')
            ->where('usi.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleResult();

        return [
            'totalSubjects' => (int) ($result['totalSubjects'] ?? 0),
            'totalViews' => (int) ($result['totalViews'] ?? 0),
            'totalDownloads' => (int) ($result['totalDownloads'] ?? 0),
            'totalFavorites' => (int) ($result['totalFavorites'] ?? 0),
            'totalComments' => (int) ($result['totalComments'] ?? 0),
            'totalQuizAttempts' => (int) ($result['totalQuizAttempts'] ?? 0),
            'totalTimeSpent' => (int) ($result['totalTimeSpent'] ?? 0),
            'avgInterest' => (float) ($result['avgInterest'] ?? 0)
        ];
    }
}