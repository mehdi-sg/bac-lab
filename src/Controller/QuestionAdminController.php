<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Question;
use App\Entity\Choix;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/quiz')]
class QuestionAdminController extends AbstractController
{
    #[Route('/', name: 'admin_quiz_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $quizzes = $em->getRepository(Quiz::class)->findAll();

        return $this->render('Quiz/crud.html.twig', [
            'quizzes' => $quizzes,
        ]);
    }

    #[Route('/{id}/questions', name: 'admin_quiz_questions', methods: ['GET'])]
    public function questions(Quiz $quiz, EntityManagerInterface $em): Response
    {
        $questions = $em->getRepository(Question::class)->findBy(['quiz' => $quiz]);

        return $this->render('Quiz/questions.html.twig', [
            'quiz' => $quiz,
            'questions' => $questions,
        ]);
    }

    // ==========================
    // NOUVELLE QUESTION
    // ==========================
    #[Route('/{id}/question/nouvelle', name: 'admin_question_new', methods: ['GET', 'POST'])]
    public function newQuestion(Quiz $quiz, Request $request, EntityManagerInterface $em): Response
    {
        $errors = [];
        $old = [];
        $choiceCount = 2;

        if ($request->isMethod('POST')) {
            $old = $request->request->all();
            $action = (string) $request->request->get('action', 'save');
            $type = (string) $request->request->get('type_question', '');
            $choiceCount = (int) $request->request->get('choix_count', 2);
            $choiceCount = max(2, min(5, $choiceCount));

            // 1) Ajouter un choix
            if ($action === 'add_choice') {
                if ($type === 'VRAI_FAUX') {
                    return $this->render('Quiz/question_form.html.twig', [
                        'quiz' => $quiz,
                        'edit' => false,
                        'old' => $old,
                        'errors' => $errors,
                        'choiceCount' => $choiceCount,
                    ]);
                }
                
                if ($type === 'QCM' && $choiceCount < 5) {
                    $choiceCount++;
                }

                return $this->render('Quiz/question_form.html.twig', [
                    'quiz' => $quiz,
                    'edit' => false,
                    'old' => $old,
                    'errors' => $errors,
                    'choiceCount' => $choiceCount,
                ]);
            }

            // 2) Supprimer un choix
            if ($action === 'remove_choice') {
                $removeIndex = (int) $request->request->get('remove_index', 0);
                if ($type === 'QCM' && $choiceCount > 2 && $removeIndex >= 0 && $removeIndex < $choiceCount) {
                    $choiceCount--;
                }

                return $this->render('Quiz/question_form.html.twig', [
                    'quiz' => $quiz,
                    'edit' => false,
                    'old' => $old,
                    'errors' => $errors,
                    'choiceCount' => $choiceCount,
                ]);
            }

            // 3) Changer le type de question
            if ($action === 'change_type') {
                $choiceCount = 2;
                
                return $this->render('Quiz/question_form.html.twig', [
                    'quiz' => $quiz,
                    'edit' => false,
                    'old' => $old,
                    'errors' => $errors,
                    'choiceCount' => $choiceCount,
                ]);
            }

            // 4) SAVE
            $enonce = trim((string)$request->request->get('enonce'));
            $score = (int)$request->request->get('score', 1);
            $choiceCount = (int)$request->request->get('choix_count', 2);

            // Récupérer les choix selon le type
            if ($type === 'VRAI_FAUX') {
                $choixCorrectsRaw = $request->request->get('choix_correct_vf');
                $choixLibelles = [0 => 'Vrai', 1 => 'Faux'];
            } else {
                $choixLibelles = [];
                $choixCorrects = [];
                
                for ($i = 0; $i < $choiceCount; $i++) {
                    $choixLibelles[$i] = $request->request->get('choix_libelle_' . $i, '');
                    $choixCorrects[$i] = $request->request->get('choix_correct_' . $i) === '1' ? '1' : '0';
                }
            }

            // Validations
            if ($enonce === '') $errors[] = "L'énoncé est obligatoire.";
            if (!in_array($type, ['QCM', 'VRAI_FAUX'], true)) $errors[] = "Le type est invalide.";
            if ($score < 1 || $score > 20) $errors[] = "Le score doit être entre 1 et 20.";

            if ($type === 'VRAI_FAUX') {
                if ($choixCorrectsRaw === null) {
                    $errors[] = "Cochez la bonne réponse.";
                }
            } elseif ($type === 'QCM') {
                $hasChoice = false;
                $nonEmptyCount = 0;
                $correctCount = 0;
                
                foreach ($choixLibelles as $index => $lib) {
                    $libTrimmed = trim((string)$lib);
                    if ($libTrimmed !== '') { 
                        $hasChoice = true; 
                        $nonEmptyCount++;
                    }
                    if (isset($choixCorrects[$index]) && $choixCorrects[$index] === '1') {
                        $correctCount++;
                    }
                }
                
                if (!$hasChoice) $errors[] = "Ajoutez au moins un choix non vide.";
                if ($nonEmptyCount < 2) $errors[] = "Le QCM doit avoir au moins 2 réponses.";
                if ($correctCount === 0) $errors[] = "Cochez au moins une bonne réponse.";
                if ($correctCount >= $nonEmptyCount) $errors[] = "Vous ne pouvez pas cocher toutes les réponses comme correctes.";
            }

            if (!empty($errors)) {
                return $this->render('Quiz/question_form.html.twig', [
                    'quiz' => $quiz,
                    'edit' => false,
                    'old' => $old,
                    'errors' => $errors,
                    'choiceCount' => $choiceCount,
                ]);
            }

            // Persist Question
            $question = new Question();
            $question->setEnonce($enonce);
            $question->setTypeQuestion($type);
            $question->setScore($score);
            $question->setQuiz($quiz);

            $em->persist($question);

            // Persist Choix
            if ($type === 'VRAI_FAUX') {
                $vrai = new Choix();
                $vrai->setLibelle('Vrai');
                $vrai->setEstCorrect($choixCorrectsRaw === '0');
                $vrai->setQuestion($question);
                $em->persist($vrai);

                $faux = new Choix();
                $faux->setLibelle('Faux');
                $faux->setEstCorrect($choixCorrectsRaw === '1');
                $faux->setQuestion($question);
                $em->persist($faux);
            } else {
                foreach ($choixLibelles as $index => $libelle) {
                    if (trim((string)$libelle) === '') continue;
                    
                    $choix = new Choix();
                    $choix->setLibelle(trim((string)$libelle));
                    $choix->setEstCorrect(isset($choixCorrects[$index]) && $choixCorrects[$index] === '1');
                    $choix->setQuestion($question);
                    $em->persist($choix);
                }
            }

            $em->flush();

            // Incrémenter le nombre de questions du quiz
            $quiz->setNbQuestions($quiz->getNbQuestions() + 1);
            $em->flush();

            $this->addFlash('success', 'Question ajoutée avec succès !');
            return $this->redirectToRoute('admin_quiz_questions', ['id' => $quiz->getId()]);
        }

        // GET
        return $this->render('Quiz/question_form.html.twig', [
            'quiz' => $quiz,
            'edit' => false,
            'old' => $old,
            'errors' => $errors,
            'choiceCount' => $choiceCount,
        ]);
    }

    // ==========================
    // MODIFIER QUESTION
    // ==========================
    #[Route('/question/{id}/modifier', name: 'admin_question_edit', methods: ['GET', 'POST'])]
    public function editQuestion(Question $question, Request $request, EntityManagerInterface $em): Response
    {
        $quiz = $question->getQuiz();
        $errors = [];
        $choiceCount = 2;

        // Préparer les données de la question pour le formulaire
        $old = [
            'enonce' => $question->getEnonce(),
            'type_question' => $question->getTypeQuestion(),
            'score' => $question->getScore(),
        ];

        // Charger les choix
        $choix = $question->getChoix();

        if ($question->getTypeQuestion() === 'VRAI_FAUX') {
            $choiceCount = 2;
            foreach ($choix as $index => $c) {
                if ($c->isEstCorrect()) {
                    $old['choix_correct_vf'] = (string)$index;
                    break;
                }
            }
        } else {
            $choiceCount = count($choix);
            foreach ($choix as $index => $c) {
                $old['choix_libelle_' . $index] = $c->getLibelle();
                $old['choix_correct_' . $index] = $c->isEstCorrect() ? '1' : '0';
            }
        }

        $old['choix_count'] = $choiceCount;

        if ($request->isMethod('POST')) {
            $old = $request->request->all();
            $action = (string) $request->request->get('action', 'save');
            $type = (string) $request->request->get('type_question', '');
            $choiceCount = (int) $request->request->get('choix_count', 2);
            $choiceCount = max(2, min(5, $choiceCount));

            // Actions
            if ($action === 'add_choice') {
                if ($type === 'QCM' && $choiceCount < 5) {
                    $choiceCount++;
                }
                return $this->render('Quiz/modifier_question.html.twig', [
                    'quiz' => $quiz,
                    'question' => $question,
                    'edit' => true,
                    'old' => $old,
                    'errors' => $errors,
                    'choiceCount' => $choiceCount,
                ]);
            }

            if ($action === 'remove_choice') {
                if ($type === 'QCM' && $choiceCount > 2) {
                    $choiceCount--;
                }
                return $this->render('Quiz/modifier_question.html.twig', [
                    'quiz' => $quiz,
                    'question' => $question,
                    'edit' => true,
                    'old' => $old,
                    'errors' => $errors,
                    'choiceCount' => $choiceCount,
                ]);
            }

            if ($action === 'change_type') {
                $choiceCount = 2;
                foreach ($old as $key => $value) {
                    if (strpos($key, 'choix_libelle_') === 0 || strpos($key, 'choix_correct_') === 0) {
                        unset($old[$key]);
                    }
                }
                return $this->render('Quiz/modifier_question.html.twig', [
                    'quiz' => $quiz,
                    'question' => $question,
                    'edit' => true,
                    'old' => $old,
                    'errors' => $errors,
                    'choiceCount' => $choiceCount,
                ]);
            }

            // SAVE
            $enonce = trim((string)$request->request->get('enonce'));
            $score = (int)$request->request->get('score', 1);
            $choiceCount = (int)$request->request->get('choix_count', 2);

            if ($type === 'VRAI_FAUX') {
                $choixCorrectsRaw = $request->request->get('choix_correct_vf');
                $choixLibelles = [0 => 'Vrai', 1 => 'Faux'];
            } else {
                $choixLibelles = [];
                $choixCorrects = [];
                
                for ($i = 0; $i < $choiceCount; $i++) {
                    $choixLibelles[$i] = $request->request->get('choix_libelle_' . $i, '');
                    $choixCorrects[$i] = $request->request->get('choix_correct_' . $i) === '1' ? '1' : '0';
                }
            }

            // Validations
            if ($enonce === '') $errors[] = "L'énoncé est obligatoire.";
            if (!in_array($type, ['QCM', 'VRAI_FAUX'], true)) $errors[] = "Le type est invalide.";
            if ($score < 1 || $score > 20) $errors[] = "Le score doit être entre 1 et 20.";

            if ($type === 'VRAI_FAUX') {
                if ($choixCorrectsRaw === null) {
                    $errors[] = "Cochez la bonne réponse.";
                }
            } elseif ($type === 'QCM') {
                $hasChoice = false;
                $nonEmptyCount = 0;
                $correctCount = 0;
                
                foreach ($choixLibelles as $index => $lib) {
                    $libTrimmed = trim((string)$lib);
                    if ($libTrimmed !== '') { 
                        $hasChoice = true; 
                        $nonEmptyCount++;
                    }
                    if (isset($choixCorrects[$index]) && $choixCorrects[$index] === '1') {
                        $correctCount++;
                    }
                }
                
                if (!$hasChoice) $errors[] = "Ajoutez au moins un choix non vide.";
                if ($nonEmptyCount < 2) $errors[] = "Le QCM doit avoir au moins 2 réponses.";
                if ($correctCount === 0) $errors[] = "Cochez au moins une bonne réponse.";
                if ($correctCount >= $nonEmptyCount) $errors[] = "Vous ne pouvez pas cocher toutes les réponses comme correctes.";
            }

            if (!empty($errors)) {
                return $this->render('Quiz/modifier_question.html.twig', [
                    'quiz' => $quiz,
                    'question' => $question,
                    'edit' => true,
                    'old' => $old,
                    'errors' => $errors,
                    'choiceCount' => $choiceCount,
                ]);
            }

            // Update Question
            $question->setEnonce($enonce);
            $question->setTypeQuestion($type);
            $question->setScore($score);

            // Supprimer les anciens choix
            foreach ($question->getChoix() as $oldChoix) {
                $em->remove($oldChoix);
            }
            $em->flush();

            // Créer les nouveaux choix
            if ($type === 'VRAI_FAUX') {
                $vrai = new Choix();
                $vrai->setLibelle('Vrai');
                $vrai->setEstCorrect($choixCorrectsRaw === '0');
                $vrai->setQuestion($question);
                $em->persist($vrai);

                $faux = new Choix();
                $faux->setLibelle('Faux');
                $faux->setEstCorrect($choixCorrectsRaw === '1');
                $faux->setQuestion($question);
                $em->persist($faux);
            } else {
                foreach ($choixLibelles as $index => $libelle) {
                    if (trim((string)$libelle) === '') continue;
                    
                    $choix = new Choix();
                    $choix->setLibelle(trim((string)$libelle));
                    $choix->setEstCorrect(isset($choixCorrects[$index]) && $choixCorrects[$index] === '1');
                    $choix->setQuestion($question);
                    $em->persist($choix);
                }
            }

            $em->flush();

            $this->addFlash('success', 'Question modifiée avec succès !');
            return $this->redirectToRoute('admin_quiz_questions', ['id' => $quiz->getId()]);
        }

        return $this->render('Quiz/modifier_question.html.twig', [
            'quiz' => $quiz,
            'question' => $question,
            'edit' => true,
            'old' => $old,
            'errors' => $errors,
            'choiceCount' => $choiceCount,
        ]);
    }

    // ==========================
    // SUPPRIMER QUESTION
    // ==========================
    #[Route('/question/{id}/supprimer', name: 'admin_question_delete', methods: ['POST'])]
    public function deleteQuestion(Question $question, EntityManagerInterface $em): Response
    {
        $quiz = $question->getQuiz();
        $quizId = $quiz->getId();
        
        foreach ($question->getChoix() as $choix) {
            $em->remove($choix);
        }
        
        $em->remove($question);
        $em->flush();

        $quiz->setNbQuestions(max(0, $quiz->getNbQuestions() - 1));
        $em->flush();

        $this->addFlash('success', 'Question supprimée avec succès !');
        return $this->redirectToRoute('admin_quiz_questions', ['id' => $quizId]);
    }
}

