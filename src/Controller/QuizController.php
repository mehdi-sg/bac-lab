<?php
// src/Controller/QuizController.php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
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
    public function start(Request $request, EntityManagerInterface $em): Response
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

        // Rechercher le quiz correspondant dans la base de données
        $quiz = null;
        
        // First, try to find the quiz by exact match (by ID or by name)
        
        // If matiere and chapitre are numeric, try as IDs
        if (is_numeric($matiere) && is_numeric($chapitre)) {
            $quiz = $em->getRepository(Quiz::class)->findOneBy([
                'matiere' => $matiere,
                'chapitre' => $chapitre,
                'niveau' => $niveau
            ]);
        }

        // If not found, try by name
        if (!$quiz) {
            // Chercher la matière par nom
            $matiereEntity = $em->getRepository(\App\Entity\Matiere::class)->findOneBy(['nom' => $matiere]);
            
            // Chercher le chapitre par titre ou partiellement
            $chapitreEntity = $em->getRepository(\App\Entity\Chapitre::class)->findOneBy(['titre' => $chapitre]);
            
            // If not found by exact title, try partial match
            if (!$chapitreEntity) {
                $chapitres = $em->getRepository(\App\Entity\Chapitre::class)->createQueryBuilder('c')
                    ->where('c.titre LIKE :titre')
                    ->setParameter('titre', '%' . $chapitre . '%')
                    ->getQuery()
                    ->getResult();
                if (!empty($chapitres)) {
                    $chapitreEntity = $chapitres[0];
                }
            }
            
            if ($matiereEntity && $chapitreEntity) {
                $quiz = $em->getRepository(Quiz::class)->findOneBy([
                    'matiere' => $matiereEntity,
                    'chapitre' => $chapitreEntity,
                    'niveau' => $niveau
                ]);
                
                // If still not found, try any level for this chapter
                if (!$quiz) {
                    $quiz = $em->getRepository(Quiz::class)->findOneBy([
                        'matiere' => $matiereEntity,
                        'chapitre' => $chapitreEntity
                    ]);
                }
            }
        }

        // If still not found, try to find ANY quiz for this chapter (any matiere, any niveau)
        if (!$quiz) {
            $chapitreEntity = $em->getRepository(\App\Entity\Chapitre::class)->findOneBy(['titre' => $chapitre]);
            if (!$chapitreEntity) {
                $chapitres = $em->getRepository(\App\Entity\Chapitre::class)->createQueryBuilder('c')
                    ->where('c.titre LIKE :titre')
                    ->setParameter('titre', '%' . $chapitre . '%')
                    ->getQuery()
                    ->getResult();
                if (!empty($chapitres)) {
                    $chapitreEntity = $chapitres[0];
                }
            }
            
            if ($chapitreEntity) {
                $quiz = $em->getRepository(Quiz::class)->findOneBy([
                    'chapitre' => $chapitreEntity
                ]);
            }
        }

        // ULTIMATE FALLBACK: If still no quiz, just get the FIRST quiz in the database
        if (!$quiz) {
            $allQuizzes = $em->getRepository(Quiz::class)->findAll();
            if (!empty($allQuizzes)) {
                $quiz = $allQuizzes[0];
            }
        }

        // Check if quiz was found
        if (!$quiz) {
            $this->addFlash('error', 'Aucun quiz trouvé pour ces critères: ' . $matiere . ' - ' . $chapitre . ' (' . $niveau . '). Veuillez sélectionner une autre matière, chapitre ou niveau.');
            return $this->redirectToRoute('app_quiz');
        }

        // Vérifier que le quiz a des questions
        $questions = $quiz->getQuestions();
        if ($questions->isEmpty()) {
            $this->addFlash('error', 'Ce quiz n\'a pas de questions. Veuillez sélectionner un autre quiz.');
            return $this->redirectToRoute('app_quiz');
        }
        
        // Convertir les questions en tableau pour le template
        $questionsData = [];
        foreach ($questions as $question) {
            $choixData = [];
            $correctCount = 0;
            foreach ($question->getChoix() as $choix) {
                $choixData[] = [
                    'id' => $choix->getId(),
                    'libelle' => $choix->getLibelle(),
                    'est_correct' => $choix->isEstCorrect()
                ];
                if ($choix->isEstCorrect()) {
                    $correctCount++;
                }
            }
            $questionsData[] = [
                'id' => $question->getId(),
                'enonce' => $question->getEnonce(),
                'type' => $question->getTypeQuestion(),
                'choix' => $choixData,
                'multiple_correct' => ($correctCount > 1)
            ];
        }

        // Durée en secondes
        $duree = $quiz->getDuree() * 60;

        return $this->render('Quiz/start.html.twig', [
            'matiere' => $matiere,
            'chapitre' => $chapitre,
            'niveau' => $niveau,
            'quiz' => $quiz,
            'quizId' => $quiz->getId(),
            'questions' => $questionsData,
            'totalQuestions' => count($questionsData),
            'duree' => $duree,
        ]);
    }

    #[Route('/quiz/submit', name: 'app_quiz_submit', methods: ['GET', 'POST'])]
    public function submit(Request $request, EntityManagerInterface $em): Response
    {
        // Récupérer toutes les réponses
        $userAnswers = $request->request->all();
        
        // Récupérer les paramètres pour trouver le quiz
        $quizId = $request->request->get('quiz_id');
        $matiere = $request->request->get('matiere');
        $chapitre = $request->request->get('chapitre');
        $niveau = $request->request->get('niveau');
        
        // Try to find quiz by ID first (most reliable)
        $quiz = null;
        
        if ($quizId) {
            $quiz = $em->getRepository(Quiz::class)->find($quizId);
        }
        
        // Fallback: try by parameters
        if (!$quiz) {
            // First try by ID
            if (is_numeric($matiere) && is_numeric($chapitre)) {
                $quiz = $em->getRepository(Quiz::class)->findOneBy([
                    'matiere' => $matiere,
                    'chapitre' => $chapitre,
                    'niveau' => $niveau
                ]);
            }

            // Try by name
            if (!$quiz) {
                $matiereEntity = $em->getRepository(\App\Entity\Matiere::class)->findOneBy(['nom' => $matiere]);
                $chapitreEntity = $em->getRepository(\App\Entity\Chapitre::class)->findOneBy(['titre' => $chapitre]);
                
                if (!$chapitreEntity) {
                    $chapitres = $em->getRepository(\App\Entity\Chapitre::class)->createQueryBuilder('c')
                        ->where('c.titre LIKE :titre')
                        ->setParameter('titre', '%' . $chapitre . '%')
                        ->getQuery()
                        ->getResult();
                    if (!empty($chapitres)) {
                        $chapitreEntity = $chapitres[0];
                    }
                }
                
                if ($matiereEntity && $chapitreEntity) {
                    $quiz = $em->getRepository(Quiz::class)->findOneBy([
                        'matiere' => $matiereEntity,
                        'chapitre' => $chapitreEntity,
                        'niveau' => $niveau
                    ]);
                    
                    if (!$quiz) {
                        $quiz = $em->getRepository(Quiz::class)->findOneBy([
                            'matiere' => $matiereEntity,
                            'chapitre' => $chapitreEntity
                        ]);
                    }
                }
            }

            // Try any quiz for this chapter
            if (!$quiz) {
                $chapitreEntity = $em->getRepository(\App\Entity\Chapitre::class)->findOneBy(['titre' => $chapitre]);
                if (!$chapitreEntity) {
                    $chapitres = $em->getRepository(\App\Entity\Chapitre::class)->createQueryBuilder('c')
                        ->where('c.titre LIKE :titre')
                        ->setParameter('titre', '%' . $chapitre . '%')
                        ->getQuery()
                        ->getResult();
                    if (!empty($chapitres)) {
                        $chapitreEntity = $chapitres[0];
                    }
                }
                
                if ($chapitreEntity) {
                    $quiz = $em->getRepository(Quiz::class)->findOneBy([
                        'chapitre' => $chapitreEntity
                    ]);
                }
            }

            // Ultimate fallback
            if (!$quiz) {
                $allQuizzes = $em->getRepository(Quiz::class)->findAll();
                if (!empty($allQuizzes)) {
                    $quiz = $allQuizzes[0];
                }
            }
        }

        // Debug: Log what was found
        error_log("Quiz submit - quiz_id: " . $quizId . ", Matiere: " . $matiere . ", Chapitre: " . $chapitre . ", Niveau: " . $niveau);
        error_log("Quiz found: " . ($quiz ? $quiz->getId() . ' - ' . $quiz->getTitre() : 'NULL'));

        // Récupérer les questions du quiz
        if ($quiz) {
            $questions = $quiz->getQuestions();
            $questionsData = [];
            foreach ($questions as $question) {
                $choixData = [];
                $correctCount = 0;
                foreach ($question->getChoix() as $choix) {
                    $choixData[] = [
                        'id' => $choix->getId(),
                        'libelle' => $choix->getLibelle(),
                        'est_correct' => $choix->isEstCorrect()
                    ];
                    if ($choix->isEstCorrect()) {
                        $correctCount++;
                    }
                }
                $questionsData[] = [
                    'id' => $question->getId(),
                    'enonce' => $question->getEnonce(),
                    'type' => $question->getTypeQuestion(),
                    'choix' => $choixData,
                    'multiple_correct' => ($correctCount > 1)
                ];
            }
            $questions = $questionsData;
        } else {
            // Only use example questions if absolutely no quiz exists
            $questions = [];
        }
        
        // Calculer le score
        $score = 0;
        $total = count($questions);
        $details = [];
    
    foreach ($questions as $question) {
        $questionKey = 'question_' . $question['id'];
        $userAnswer = $userAnswers[$questionKey] ?? null;
        
        // Check if this is a multiple choice question (QCM) or single choice (VRAI_FAUX)
        $isMultipleChoice = ($question['type'] == 'QCM');
        
        if ($isMultipleChoice) {
            // Multiple choice: userAnswer is an array of selected choice IDs
            $userAnswerIds = is_array($userAnswer) ? $userAnswer : [];
            
            // Get all correct choice IDs
            $correctAnswerIds = [];
            $correctAnswerLabels = [];
            foreach ($question['choix'] as $choix) {
                if ($choix['est_correct']) {
                    $correctAnswerIds[] = $choix['id'];
                    $correctAnswerLabels[] = $choix['libelle'];
                }
            }
            
            // Get user's selected choice labels
            $userAnswerLabels = [];
            foreach ($question['choix'] as $choix) {
                if (in_array($choix['id'], $userAnswerIds)) {
                    $userAnswerLabels[] = $choix['libelle'];
                }
            }
            
            // Check if user answered or not
            $hasAnswered = !empty($userAnswerIds);
            
            // Check if user selected exactly the correct answers (only if answered)
            $isCorrect = false;
            if ($hasAnswered) {
                $isCorrect = (count($userAnswerIds) == count($correctAnswerIds)) && 
                             empty(array_diff($userAnswerIds, $correctAnswerIds));
            }
            
            if ($isCorrect) {
                $score++;
            }
            
            $details[] = [
                'question' => $question['enonce'],
                'type' => $question['type'],
                'multiple_correct' => $question['multiple_correct'] ?? false,
                'choix' => $question['choix'],
                'user_answer' => implode(', ', $userAnswerLabels) ?: 'Sans réponse',
                'user_answer_id' => $userAnswerIds,
                'correct_answer' => implode(', ', $correctAnswerLabels),
                'correct_answer_id' => $correctAnswerIds,
                'is_correct' => $hasAnswered ? $isCorrect : null,
            ];
        } else {
            // Single choice: userAnswer is a single value
            $userAnswerId = $userAnswer;
            
            // Check if user answered or not
            $hasAnswered = $userAnswerId !== null && $userAnswerId !== '';
            
            // Trouver la bonne réponse
            $correctAnswerId = null;
            $correctAnswerLabel = null;
            $userAnswerLabel = null;
            foreach ($question['choix'] as $choix) {
                if ($choix['est_correct']) {
                    $correctAnswerId = $choix['id'];
                    $correctAnswerLabel = $choix['libelle'];
                }
                if ($choix['id'] == $userAnswerId) {
                    $userAnswerLabel = $choix['libelle'];
                }
            }
            
            // Vérifier si la réponse est correcte (only if answered)
            $isCorrect = false;
            if ($hasAnswered) {
                $isCorrect = ($userAnswerId == $correctAnswerId);
            }
            
            if ($isCorrect) {
                $score++;
            }
            
            $details[] = [
                'question' => $question['enonce'],
                'type' => $question['type'],
                'multiple_correct' => $question['multiple_correct'] ?? false,
                'choix' => $question['choix'],
                'user_answer' => $userAnswerLabel ?: 'Sans réponse',
                'user_answer_id' => $userAnswerId,
                'correct_answer' => $correctAnswerLabel,
                'correct_answer_id' => $correctAnswerId,
                'is_correct' => $hasAnswered ? $isCorrect : null,
            ];
        }
    }
    
    // Calculer le pourcentage
    $percentage = ($total > 0) ? round(($score / $total) * 100) : 0;
    
    // Store details in session for the correction page
    $request->getSession()->set('quiz_details', $details);
    $request->getSession()->set('quiz_score', $score);
    $request->getSession()->set('quiz_total', $total);
    $request->getSession()->set('quiz_percentage', $percentage);
    $request->getSession()->set('quiz_id', $quizId);
    
    return $this->render('Quiz/result.html.twig', [
        'score' => $score,
        'total' => $total,
        'percentage' => $percentage,
        'details' => $details,
    ]);
}

    #[Route('/quiz/correction', name: 'app_quiz_correction', methods: ['GET', 'POST'])]
    public function correction(Request $request, EntityManagerInterface $em): Response
    {
        // Get details from session
        $details = $request->getSession()->get('quiz_details', []);
        $score = $request->getSession()->get('quiz_score', 0);
        $total = $request->getSession()->get('quiz_total', 0);
        $percentage = $request->getSession()->get('quiz_percentage', 0);
        
        if (empty($details)) {
            $this->addFlash('error', 'Aucune correction disponible. Veuillez refaire le quiz.');
            return $this->redirectToRoute('app_quiz');
        }
        
        return $this->render('Quiz/correction.html.twig', [
            'score' => $score,
            'total' => $total,
            'percentage' => $percentage,
            'details' => $details,
        ]);
    }
}
