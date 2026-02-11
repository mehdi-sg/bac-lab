<?php
// src/Controller/QuizController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class QuizController extends AbstractController
{
    #[Route('/quiz', name: 'app_quiz')]
    public function index(): Response
    {
        return $this->render('Quiz/quiz.html.twig');
    }

    #[Route('/quiz/start', name: 'app_quiz_start')]
    public function start(Request $request): Response
    {
        // Récupérer les paramètres
        $matiere = $request->query->get('matiere');
        $chapitre = $request->query->get('chapitre');
        $niveau = $request->query->get('niveau');

        // Validation basique
        if (!$matiere || !$chapitre || !$niveau) {
            $this->addFlash('error', 'Veuillez sélectionner tous les paramètres du quiz.');
            return $this->redirectToRoute('app_quiz');
        }

        // TODO: Récupérer les vraies questions depuis la base de données
        // Pour l'instant, on utilise des questions d'exemple
        $questions = $this->getExampleQuestions($matiere, $chapitre, $niveau);

        return $this->render('Quiz/start.html.twig', [
            'matiere' => $matiere,
            'chapitre' => $chapitre,
            'niveau' => $niveau,
            'questions' => $questions,
            'totalQuestions' => count($questions),
        ]);
    }

    #[Route('/quiz/submit', name: 'app_quiz_submit', methods: ['GET', 'POST'])]
public function submit(Request $request): Response
{
    // Récupérer toutes les réponses
    $userAnswers = $request->request->all();
    
    // Récupérer les questions
    $questions = $this->getExampleQuestions('mathematiques', 'Limites', 'facile');
    
    // Calculer le score
    $score = 0;
    $total = count($questions);
    $details = [];
    
    foreach ($questions as $question) {
        $questionKey = 'question_' . $question['id'];
        $userAnswerId = $userAnswers[$questionKey] ?? null;
        
        // Trouver la bonne réponse
        $correctAnswerId = null;
        foreach ($question['choix'] as $choix) {
            if ($choix['est_correct']) {
                $correctAnswerId = $choix['id'];
                break;
            }
        }
        
        // Vérifier si la réponse est correcte
        $isCorrect = ($userAnswerId == $correctAnswerId);
        if ($isCorrect) {
            $score++;
        }
        
        $details[] = [
            'question' => $question['enonce'],
            'user_answer' => $userAnswerId,
            'correct_answer' => $correctAnswerId,
            'is_correct' => $isCorrect,
        ];
    }
    
    // Calculer le pourcentage
    $percentage = ($total > 0) ? round(($score / $total) * 100) : 0;
    
    return $this->render('Quiz/result.html.twig', [
        'score' => $score,
        'total' => $total,
        'percentage' => $percentage,
        'details' => $details,
    ]);
}

    private function getExampleQuestions(string $matiere, string $chapitre, string $niveau): array
    {
        // Questions d'exemple - À remplacer par des vraies requêtes en base de données
        return [
            [
                'id' => 1,
                'enonce' => 'Quelle est la limite de (1/x) quand x tend vers l\'infini ?',
                'type' => 'QCM',
                'choix' => [
                    ['id' => 1, 'libelle' => '0', 'est_correct' => true],
                    ['id' => 2, 'libelle' => '1', 'est_correct' => false],
                    ['id' => 3, 'libelle' => '+∞', 'est_correct' => false],
                    ['id' => 4, 'libelle' => 'La limite n\'existe pas', 'est_correct' => false],
                ]
            ],
            [
                'id' => 2,
                'enonce' => 'Une fonction est continue sur un intervalle si elle est dérivable sur cet intervalle.',
                'type' => 'VRAI_FAUX',
                'choix' => [
                    ['id' => 5, 'libelle' => 'Vrai', 'est_correct' => false],
                    ['id' => 6, 'libelle' => 'Faux', 'est_correct' => true],
                ]
            ],
            [
                'id' => 3,
                'enonce' => 'La dérivée de ln(x) est :',
                'type' => 'QCM',
                'choix' => [
                    ['id' => 7, 'libelle' => '1/x', 'est_correct' => true],
                    ['id' => 8, 'libelle' => 'x', 'est_correct' => false],
                    ['id' => 9, 'libelle' => 'e^x', 'est_correct' => false],
                    ['id' => 10, 'libelle' => '1/x²', 'est_correct' => false],
                ]
            ],
            [
                'id' => 4,
                'enonce' => 'Le théorème des valeurs intermédiaires s\'applique uniquement aux fonctions continues.',
                'type' => 'VRAI_FAUX',
                'choix' => [
                    ['id' => 11, 'libelle' => 'Vrai', 'est_correct' => true],
                    ['id' => 12, 'libelle' => 'Faux', 'est_correct' => false],
                ]
            ],
            [
                'id' => 5,
                'enonce' => 'Si f\'(a) = 0, alors f admet un extremum local en a.',
                'type' => 'VRAI_FAUX',
                'choix' => [
                    ['id' => 13, 'libelle' => 'Vrai', 'est_correct' => false],
                    ['id' => 14, 'libelle' => 'Faux', 'est_correct' => true],
                ]
            ],
            [
                'id' => 6,
                'enonce' => 'La primitive de cos(x) est :',
                'type' => 'QCM',
                'choix' => [
                    ['id' => 15, 'libelle' => 'sin(x) + C', 'est_correct' => true],
                    ['id' => 16, 'libelle' => '-sin(x) + C', 'est_correct' => false],
                    ['id' => 17, 'libelle' => 'tan(x) + C', 'est_correct' => false],
                    ['id' => 18, 'libelle' => '-cos(x) + C', 'est_correct' => false],
                ]
            ],
            [
                'id' => 7,
                'enonce' => 'Une suite croissante et majorée est toujours convergente.',
                'type' => 'VRAI_FAUX',
                'choix' => [
                    ['id' => 19, 'libelle' => 'Vrai', 'est_correct' => true],
                    ['id' => 20, 'libelle' => 'Faux', 'est_correct' => false],
                ]
            ],
            [
                'id' => 8,
                'enonce' => 'La dérivée de e^(2x) est :',
                'type' => 'QCM',
                'choix' => [
                    ['id' => 21, 'libelle' => '2e^(2x)', 'est_correct' => true],
                    ['id' => 22, 'libelle' => 'e^(2x)', 'est_correct' => false],
                    ['id' => 23, 'libelle' => '2x·e^(2x)', 'est_correct' => false],
                    ['id' => 24, 'libelle' => 'e^(2x)/2', 'est_correct' => false],
                ]
            ],
            [
                'id' => 9,
                'enonce' => 'L\'intégrale définie ∫[0,1] x dx est égale à :',
                'type' => 'QCM',
                'choix' => [
                    ['id' => 25, 'libelle' => '1/2', 'est_correct' => true],
                    ['id' => 26, 'libelle' => '1', 'est_correct' => false],
                    ['id' => 27, 'libelle' => '0', 'est_correct' => false],
                    ['id' => 28, 'libelle' => '2', 'est_correct' => false],
                ]
            ],
            [
                'id' => 10,
                'enonce' => 'Toute fonction dérivable est continue.',
                'type' => 'VRAI_FAUX',
                'choix' => [
                    ['id' => 29, 'libelle' => 'Vrai', 'est_correct' => true],
                    ['id' => 30, 'libelle' => 'Faux', 'est_correct' => false],
                ]
            ],
        ];
    }
}