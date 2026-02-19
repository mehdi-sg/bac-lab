<?php

namespace App\Service;

class ScoreCalculatorService
{
    // Mapping des filières (nom complet -> code interne)
    private const FILIERES_MAPPING = [
        'Lettres' => 'lettres',
        'Mathématiques' => 'math',
        'Sciences expérimentales' => 'sciences_exp',
        'Économie et Gestion' => 'eco_gestion',
        'Sciences techniques' => 'techniques',
        'Sciences informatiques' => 'informatique',
        'Sport' => 'sport'
    ];
    
    // Labels complets des filières
    private const FILIERES_LABELS = [
        'lettres' => 'Lettres',
        'math' => 'Mathématiques',
        'sciences_exp' => 'Sciences expérimentales',
        'eco_gestion' => 'Économie et Gestion',
        'techniques' => 'Sciences techniques',
        'informatique' => 'Sciences informatiques',
        'sport' => 'Sport'
    ];
    
    // Labels complets des matières (code -> nom complet)
    private const MATIERES_LABELS = [
        'MG' => 'Moyenne générale (MG)',
        'A' => 'Arabe',
        'PH' => 'Philosophie',
        'HG' => 'Histoire-Géographie',
        'F' => 'Français',
        'Ang' => 'Anglais',
        'M' => 'Mathématiques',
        'SP' => 'Sciences Physiques',
        'SVT' => 'Sciences de la Vie et de la Terre (SVT)',
        'Ec' => 'Économie',
        'Ge' => 'Gestion',
        'TE' => 'Technologie',
        'Algo' => 'Algorithmique',
        'STI' => 'Systèmes et Technologies de l\'Information (STI)',
        // Matières spécifiques au Sport
        'SB' => 'Sciences Biologiques / Sciences du Sport (SB)',
        'SP_Sport' => 'Spécialité Sport',
        'EP' => 'Éducation Physique (EP)'
    ];
    
    // Matières requises par filière (sans MG qui est toujours présent)
    private const MATIERES_BY_FILIERE = [
        'lettres' => ['A', 'PH', 'HG', 'F', 'Ang'],
        'math' => ['M', 'SP', 'SVT', 'F', 'Ang'],
        'sciences_exp' => ['M', 'SP', 'SVT', 'F', 'Ang'],
        'eco_gestion' => ['Ec', 'Ge', 'M', 'HG', 'F', 'Ang'],
        'techniques' => ['TE', 'M', 'SP', 'F', 'Ang'],
        'informatique' => ['M', 'Algo', 'SP', 'STI', 'F', 'Ang'],
        'sport' => ['SB', 'SP_Sport', 'EP', 'SP', 'PH', 'F', 'Ang']
    ];
    
    // Coefficients par filière (formules officielles exactes)
    private const COEFFICIENTS = [
        'lettres' => [
            'MG' => 4,
            'A' => 1.5,
            'PH' => 1.5,
            'HG' => 1,
            'F' => 1,
            'Ang' => 1
        ],
        'math' => [
            'MG' => 4,
            'M' => 2,
            'SP' => 1.5,
            'SVT' => 0.5,
            'F' => 1,
            'Ang' => 1
        ],
        'sciences_exp' => [
            'MG' => 4,
            'M' => 1,
            'SP' => 1.5,
            'SVT' => 1.5,
            'F' => 1,
            'Ang' => 1
        ],
        'eco_gestion' => [
            'MG' => 4,
            'Ec' => 1.5,
            'Ge' => 1.5,
            'M' => 0.5,
            'HG' => 0.5,
            'F' => 1,
            'Ang' => 1
        ],
        'techniques' => [
            'MG' => 4,
            'TE' => 1.5,
            'M' => 1.5,
            'SP' => 1,
            'F' => 1,
            'Ang' => 1
        ],
        'informatique' => [
            'MG' => 4,
            'M' => 1.5,
            'Algo' => 1.5,
            'SP' => 0.5,
            'STI' => 0.5,
            'F' => 1,
            'Ang' => 1
        ],
        'sport' => [
            'MG' => 4,
            'SB' => 1.5,
            'SP_Sport' => 1,
            'EP' => 0.5,
            'SP' => 0.5,
            'PH' => 0.5,
            'F' => 1,
            'Ang' => 1
        ]
    ];
    
    /**
     * Normalise le nom de la filière
     */
    public function normalizeFiliere(string $filiere): ?string
    {
        // Recherche exacte
        if (isset(self::FILIERES_MAPPING[$filiere])) {
            return self::FILIERES_MAPPING[$filiere];
        }
        
        // Recherche approximative (insensible à la casse)
        $filiereNormalized = strtolower(trim($filiere));
        
        foreach (self::FILIERES_MAPPING as $nom => $code) {
            if (strtolower($nom) === $filiereNormalized) {
                return $code;
            }
        }
        
        // Recherche par mots-clés
        if (str_contains($filiereNormalized, 'lettre')) return 'lettres';
        if (str_contains($filiereNormalized, 'math')) return 'math';
        if (str_contains($filiereNormalized, 'science') && str_contains($filiereNormalized, 'exp')) return 'sciences_exp';
        if (str_contains($filiereNormalized, 'économie') || str_contains($filiereNormalized, 'gestion')) return 'eco_gestion';
        if (str_contains($filiereNormalized, 'technique')) return 'techniques';
        if (str_contains($filiereNormalized, 'informatique')) return 'informatique';
        if (str_contains($filiereNormalized, 'sport')) return 'sport';
        
        return null;
    }
    
    /**
     * Retourne le label complet d'une filière
     */
    public function getFiliereLabel(string $filiere): string
    {
        return self::FILIERES_LABELS[$filiere] ?? $filiere;
    }
    
    /**
     * Retourne les matières requises pour une filière
     */
    public function getMatieresByFiliere(string $filiere): array
    {
        $matieres = ['MG']; // MG toujours présent
        
        if (isset(self::MATIERES_BY_FILIERE[$filiere])) {
            $matieres = array_merge($matieres, self::MATIERES_BY_FILIERE[$filiere]);
        }
        
        // Retourner avec les labels
        $result = [];
        foreach ($matieres as $code) {
            $result[$code] = self::MATIERES_LABELS[$code] ?? $code;
        }
        
        return $result;
    }
    
    /**
     * Retourne le label d'une matière
     */
    public function getMatiereLabel(string $code): string
    {
        return self::MATIERES_LABELS[$code] ?? $code;
    }
    
    /**
     * Calcule la Formule Globale (FG) selon la filière
     */
    public function computeFG(string $filiere, array $notes): float
    {
        if (!isset(self::COEFFICIENTS[$filiere])) {
            throw new \InvalidArgumentException("Filière non supportée : $filiere");
        }
        
        $coefficients = self::COEFFICIENTS[$filiere];
        $fg = 0;
        
        foreach ($coefficients as $matiere => $coeff) {
            if (!isset($notes[$matiere])) {
                throw new \InvalidArgumentException("Note manquante pour la matière : $matiere");
            }
            
            $note = (float) $notes[$matiere];
            
            // Validation des notes
            if ($note < 0 || $note > 20) {
                throw new \InvalidArgumentException("Note invalide pour $matiere : $note (doit être entre 0 et 20)");
            }
            
            $fg += $coeff * $note;
        }
        
        return round($fg, 2);
    }
    
    /**
     * Retourne les détails du calcul
     */
    public function getCalculationDetails(string $filiere, array $notes, float $fg): array
    {
        if (!isset(self::COEFFICIENTS[$filiere])) {
            return [];
        }
        
        $coefficients = self::COEFFICIENTS[$filiere];
        $details = [];
        $totalCoeff = 0;
        
        foreach ($coefficients as $matiere => $coeff) {
            $note = (float) $notes[$matiere];
            $contribution = $coeff * $note;
            
            $details[] = [
                'matiere' => $matiere,
                'matiereLabel' => self::MATIERES_LABELS[$matiere] ?? $matiere,
                'note' => $note,
                'coefficient' => $coeff,
                'contribution' => round($contribution, 2)
            ];
            
            $totalCoeff += $coeff;
        }
        
        return [
            'details' => $details,
            'totalCoefficient' => $totalCoeff,
            'formule' => $this->getFormuleText($filiere)
        ];
    }
    
    /**
     * Retourne le texte de la formule pour une filière
     */
    private function getFormuleText(string $filiere): string
    {
        $formules = [
            'lettres' => 'FG = 4×MG + 1.5×A + 1.5×PH + 1×HG + 1×F + 1×Ang',
            'math' => 'FG = 4×MG + 2×M + 1.5×SP + 0.5×SVT + 1×F + 1×Ang',
            'sciences_exp' => 'FG = 4×MG + 1×M + 1.5×SP + 1.5×SVT + 1×F + 1×Ang',
            'eco_gestion' => 'FG = 4×MG + 1.5×Ec + 1.5×Ge + 0.5×M + 0.5×HG + 1×F + 1×Ang',
            'techniques' => 'FG = 4×MG + 1.5×TE + 1.5×M + 1×SP + 1×F + 1×Ang',
            'informatique' => 'FG = 4×MG + 1.5×M + 1.5×Algo + 0.5×SP + 0.5×STI + 1×F + 1×Ang',
            'sport' => 'FG = 4×MG + 1.5×SB + 1×SP_Sport + 0.5×EP + 0.5×SP + 0.5×PH + 1×F + 1×Ang'
        ];
        
        return $formules[$filiere] ?? '';
    }
    
    /**
     * Vérifie si une filière est valide
     */
    public function isValidFiliere(string $filiere): bool
    {
        return isset(self::COEFFICIENTS[$filiere]);
    }
    
    /**
     * Retourne toutes les filières disponibles
     */
    public function getAvailableFilieres(): array
    {
        return self::FILIERES_LABELS;
    }
}