<?php

namespace App\Service;

use App\Entity\Utilisateur;
use App\Entity\Program;
use App\Repository\ProgramRepository;

/**
 * Main service for university program recommendations
 */
class OrientationRecommenderService
{
    public function __construct(
        private ProgramRepository $programRepository,
        private ScoreCalculatorService $scoreCalculator,
        private FormulaParserService $formulaParser,
        private EngagementScoringService $engagementScoring
    ) {}

    /**
     * Get program recommendations for a user
     */
    public function getRecommendations(
        Utilisateur $user, 
        array $userScores, 
        array $options = []
    ): array {
        $limit = $options['limit'] ?? 10;
        $geographicBonus = $options['geographicBonus'] ?? false;
        $filters = $options['filters'] ?? [];

        // Get user's BAC type
        $bacType = $this->getUserBacType($user);
        
        // Get programs for user's BAC type (or all programs if no BAC type)
        $programs = $this->programRepository->findWithFilters(array_merge($filters, [
            'bacType' => $bacType
        ]));

        $recommendations = [];

        foreach ($programs as $program) {
            $recommendation = $this->evaluateProgram($user, $program, $userScores, $geographicBonus);
            
            if ($recommendation) {
                $recommendations[] = $recommendation;
            }
        }

        // Sort by final score (descending)
        usort($recommendations, function($a, $b) {
            return $b['finalScore'] <=> $a['finalScore'];
        });

        return array_slice($recommendations, 0, $limit);
    }

    /**
     * Evaluate a single program for a user
     */
    private function evaluateProgram(
        Utilisateur $user, 
        Program $program, 
        array $userScores, 
        bool $geographicBonus = false
    ): ?array {
        // Skip invalid programs
        if (!$program->isValid()) {
            return null;
        }

        // Calculate T_user for this program
        $tUser = $this->calculateTUser($program, $userScores);
        if ($tUser === null) {
            return null; // Cannot calculate T for this program
        }

        // Apply geographic bonus if enabled (reduced from 7% to 5% for more realistic impact)
        if ($geographicBonus) {
            $tUser *= 1.05; // 5% bonus instead of 7%
        }

        // Calculate chance score
        $chanceScore = $this->calculateChanceScore($tUser, $program->getCutoff2024());

        // Calculate interest fit
        $formulaSubjects = $this->formulaParser->getRequiredVariables($program->getFormulaT());
        $interestFit = $this->engagementScoring->calculateInterestFit($user, $formulaSubjects);

        // Calculate global engagement
        $globalEngagement = $this->engagementScoring->computeGlobalEngagement($user);

        // Improved final score calculation with more realistic weights
        if ($globalEngagement > 0.1 || $interestFit > 0.1) {
            // User has engagement data - balanced algorithm
            $finalScore = 
                0.70 * $chanceScore +      // Academic score is most important
                0.20 * $interestFit +      // Interest fit is secondary
                0.10 * $globalEngagement;  // Global engagement is tertiary
        } else {
            // No engagement data - pure academic focus with slight randomization to avoid ties
            $finalScore = 
                0.95 * $chanceScore +
                0.05 * (mt_rand(0, 100) / 1000); // Small random factor to break ties
        }

        return [
            'program' => $program,
            'tUser' => round($tUser, 2),
            'cutoff2024' => $program->getCutoff2024(),
            'margin' => round($tUser - $program->getCutoff2024(), 2),
            'chanceScore' => $chanceScore,
            'chanceLevel' => $this->getChanceLevel($tUser - $program->getCutoff2024()),
            'interestFit' => $interestFit,
            'globalEngagement' => $globalEngagement,
            'finalScore' => $finalScore,
            'formulaSubjects' => $formulaSubjects,
            'reasons' => $this->generateRecommendationReasons($tUser, $program, $interestFit, $globalEngagement)
        ];
    }

    /**
     * Calculate T_user for a program
     */
    private function calculateTUser(Program $program, array $userScores): ?float
    {
        $formula = $program->getFormulaT();
        
        // Prepare variables for formula parsing
        $variables = [];
        
        // Add FG (user's global formula score)
        if (isset($userScores['FG'])) {
            $variables['FG'] = $userScores['FG'];
        }

        // Add individual subject scores
        foreach ($userScores as $subject => $score) {
            if ($subject !== 'FG') {
                $variables[$subject] = $score;
            }
        }

        // Add fallback values for language subjects that users might not have
        $languageSubjects = ['ESP', 'IT', 'Info'];
        foreach ($languageSubjects as $langSubject) {
            if (!isset($variables[$langSubject])) {
                // Use a neutral score (10/20) for missing language subjects
                // This allows formulas to be calculated instead of failing
                $variables[$langSubject] = 10.0;
            }
        }

        return $this->formulaParser->parseFormula($formula, $variables);
    }

    /**
     * Calculate chance score based on margin
     */
    private function calculateChanceScore(float $tUser, ?float $cutoff): float
    {
        if ($cutoff === null || $cutoff <= 0) {
            return 0.5; // Neutral score for unknown cutoffs instead of 0
        }

        $margin = $tUser - $cutoff;
        
        // More realistic chance calculation based on Tunisian admission patterns
        if ($margin >= 15) {
            return 0.98; // Excellent chance (15+ points above)
        } elseif ($margin >= 10) {
            return 0.92; // Very high chance (10-15 points above)
        } elseif ($margin >= 5) {
            return 0.82; // High chance (5-10 points above)
        } elseif ($margin >= 2) {
            return 0.72; // Good chance (2-5 points above)
        } elseif ($margin >= 0) {
            return 0.65; // Fair chance (0-2 points above)
        } elseif ($margin >= -2) {
            return 0.55; // Borderline chance (0-2 points below)
        } elseif ($margin >= -5) {
            return 0.35; // Low chance (2-5 points below)
        } elseif ($margin >= -10) {
            return 0.20; // Very low chance (5-10 points below)
        } else {
            return 0.05; // Minimal chance (10+ points below)
        }
    }

    /**
     * Get chance level description
     */
    private function getChanceLevel(float $margin): array
    {
        if ($margin >= 10) {
            return [
                'level' => 'Excellente',
                'color' => 'success',
                'description' => 'Admission quasi-garantie'
            ];
        } elseif ($margin >= 5) {
            return [
                'level' => 'Très Élevée',
                'color' => 'success',
                'description' => 'Très bonnes chances d\'admission'
            ];
        } elseif ($margin >= 2) {
            return [
                'level' => 'Élevée',
                'color' => 'success',
                'description' => 'Bonnes chances d\'admission'
            ];
        } elseif ($margin >= 0) {
            return [
                'level' => 'Bonne',
                'color' => 'info',
                'description' => 'Chances favorables d\'admission'
            ];
        } elseif ($margin >= -2) {
            return [
                'level' => 'Moyenne',
                'color' => 'warning',
                'description' => 'Chances modérées - amélioration recommandée'
            ];
        } elseif ($margin >= -5) {
            return [
                'level' => 'Faible',
                'color' => 'warning',
                'description' => 'Chances limitées - amélioration nécessaire'
            ];
        } else {
            return [
                'level' => 'Très Faible',
                'color' => 'danger',
                'description' => 'Admission difficile - amélioration importante requise'
            ];
        }
    }

    /**
     * Generate recommendation reasons
     */
    private function generateRecommendationReasons(
        float $tUser, 
        Program $program, 
        float $interestFit, 
        float $globalEngagement
    ): array {
        $reasons = [];
        $margin = $tUser - $program->getCutoff2024();

        // Academic reasons
        if ($margin > 10) {
            $reasons[] = "Score largement au-dessus du seuil (+" . round($margin, 1) . " points)";
        } elseif ($margin > 0) {
            $reasons[] = "Score au-dessus du seuil (+" . round($margin, 1) . " points)";
        } elseif ($margin > -5) {
            $reasons[] = "Score proche du seuil (" . round($margin, 1) . " points)";
        } else {
            $reasons[] = "Score en dessous du seuil (" . round($margin, 1) . " points) - Amélioration nécessaire";
        }

        // Interest reasons (only if user has engagement)
        if ($interestFit > 0.1) {
            if ($interestFit > 0.7) {
                $reasons[] = "Très forte affinité avec les matières requises";
            } elseif ($interestFit > 0.5) {
                $reasons[] = "Bonne affinité avec les matières requises";
            } elseif ($interestFit > 0.3) {
                $reasons[] = "Affinité modérée avec les matières requises";
            }
        } else {
            $reasons[] = "Recommandation basée sur votre score académique";
        }

        // Engagement reasons (only if significant)
        if ($globalEngagement > 0.5) {
            $reasons[] = "Bon niveau d'engagement sur la plateforme";
        } elseif ($globalEngagement > 0.3) {
            $reasons[] = "Engagement modéré sur la plateforme";
        }

        return $reasons;
    }

    /**
     * Get user's BAC type from profile
     */
    private function getUserBacType(Utilisateur $user): ?string
    {
        if (!$user->getProfil() || !$user->getProfil()->getFiliere()) {
            return null;
        }

        $filiere = $user->getProfil()->getFiliere()->getNom();
        
        // Map filière to BAC type (Arabic) - Updated mapping
        $mapping = [
            'Lettres' => 'آداب',
            'lettres' => 'آداب',
            'Mathématiques' => 'رياضيات', 
            'math' => 'رياضيات',
            'Sciences expérimentales' => 'علوم تجريبية',
            'sciences_exp' => 'علوم تجريبية',
            'Économie et Gestion' => 'إقتصاد وتصرف',
            'eco_gestion' => 'إقتصاد وتصرف',
            'Sciences techniques' => 'العلوم التقنية',
            'techniques' => 'العلوم التقنية',
            'Sciences informatiques' => 'علوم الإعلامية',
            'informatique' => 'علوم الإعلامية',
            'Sport' => 'رياضة',
            'sport' => 'رياضة'
        ];

        // Try exact match first
        if (isset($mapping[$filiere])) {
            return $mapping[$filiere];
        }
        
        // Try normalized filière
        $normalizedFiliere = $this->scoreCalculator->normalizeFiliere($filiere);
        if ($normalizedFiliere && isset($mapping[$normalizedFiliere])) {
            return $mapping[$normalizedFiliere];
        }

        // Fallback: return null to get all programs
        return null;
    }

    /**
     * Get "What If" simulation
     */
    public function getWhatIfSimulation(
        Utilisateur $user,
        array $baseScores,
        array $modifications = []
    ): array {
        // Apply modifications to base scores
        $simulatedScores = array_merge($baseScores, $modifications);
        
        // Get recommendations with modified scores
        $recommendations = $this->getRecommendations($user, $simulatedScores, ['limit' => 20]);
        
        // Compare with original recommendations
        $originalRecommendations = $this->getRecommendations($user, $baseScores, ['limit' => 20]);
        
        return [
            'original' => $originalRecommendations,
            'simulated' => $recommendations,
            'improvements' => $this->calculateImprovements($originalRecommendations, $recommendations)
        ];
    }

    /**
     * Calculate improvements between original and simulated recommendations
     */
    private function calculateImprovements(array $original, array $simulated): array
    {
        $improvements = [];
        
        // Create lookup for original programs
        $originalLookup = [];
        foreach ($original as $index => $rec) {
            $originalLookup[$rec['program']->getId()] = [
                'position' => $index + 1,
                'finalScore' => $rec['finalScore'],
                'chanceLevel' => $rec['chanceLevel']['level']
            ];
        }
        
        // Compare simulated results
        foreach ($simulated as $index => $rec) {
            $programId = $rec['program']->getId();
            $newPosition = $index + 1;
            
            if (isset($originalLookup[$programId])) {
                $oldPosition = $originalLookup[$programId]['position'];
                $positionChange = $oldPosition - $newPosition;
                
                if ($positionChange > 0) {
                    $improvements[] = [
                        'program' => $rec['program'],
                        'positionImprovement' => $positionChange,
                        'newPosition' => $newPosition,
                        'oldPosition' => $oldPosition,
                        'scoreImprovement' => $rec['finalScore'] - $originalLookup[$programId]['finalScore']
                    ];
                }
            } else {
                // New program in top recommendations
                $improvements[] = [
                    'program' => $rec['program'],
                    'positionImprovement' => 'NEW',
                    'newPosition' => $newPosition,
                    'oldPosition' => null,
                    'scoreImprovement' => $rec['finalScore']
                ];
            }
        }
        
        return $improvements;
    }
}