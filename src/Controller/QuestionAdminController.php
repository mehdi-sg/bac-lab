<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Question;
use App\Entity\Choix;
use App\Form\QuestionType;
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

    #[Route('/{id}/questions', name: 'admin_quiz_questions')]
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
        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        // Récupérer les données du POST pour les choix
        $old = [];
        $errors = [];
        $choiceCount = 2;
        
        if ($request->isMethod('POST')) {
            $old = $request->request->all();
            $action = (string) $request->request->get('action', 'save');
            $choiceCount = (int) $request->request->get('choix_count', 2);
            $choiceCount = max(2, min(5, $choiceCount));
            
            // Action: changer le type de question
            if ($action === 'change_type') {
                $choiceCount = 2; // Réinitialiser à 2 choix
                return $this->render('Quiz/question_form.html.twig', [
                    'quiz' => $quiz,
                    'edit' => false,
                    'form' => $form,
                    'old' => $old,
                    'errors' => $errors,
                    'choiceCount' => $choiceCount,
                ]);
            }
            
            // Action: ajouter un choix
            if ($action === 'add_choice') {
                $type = $old['typeQuestion'] ?? '';
                if ($type === 'QCM' && $choiceCount < 5) {
                    $choiceCount++;
                }
                return $this->render('Quiz/question_form.html.twig', [
                    'quiz' => $quiz,
                    'edit' => false,
                    'form' => $form,
                    'old' => $old,
                    'errors' => $errors,
                    'choiceCount' => $choiceCount,
                ]);
            }
            
            // Action: supprimer un choix
            if ($action === 'remove_choice') {
                $type = $old['typeQuestion'] ?? '';
                if ($type === 'QCM' && $choiceCount > 2) {
                    $choiceCount--;
                }
                return $this->render('Quiz/question_form.html.twig', [
                    'quiz' => $quiz,
                    'edit' => false,
                    'form' => $form,
                    'old' => $old,
                    'errors' => $errors,
                    'choiceCount' => $choiceCount,
                ]);
            }
        }

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $typeQuestion = $form->get('typeQuestion')->getData();
            
            // Sauvegarder la question
            $question->setQuiz($quiz);
            $em->persist($question);
            $em->flush();

            // Créer les choix selon le type
            if ($typeQuestion === 'VRAI_FAUX') {
                $vfCorrect = (int) ($old['choix_correct_vf'] ?? 0);
                
                $vrai = new Choix();
                $vrai->setLibelle('Vrai');
                $vrai->setEstCorrect($vfCorrect === 0);
                $vrai->setQuestion($question);
                $em->persist($vrai);

                $faux = new Choix();
                $faux->setLibelle('Faux');
                $faux->setEstCorrect($vfCorrect === 1);
                $faux->setQuestion($question);
                $em->persist($faux);
            } else {
                // QCM
                for ($i = 0; $i < $choiceCount; $i++) {
                    $libelle = $old["choix_libelle_$i"] ?? '';
                    if (trim($libelle) === '') continue;
                    
                    $choix = new Choix();
                    $choix->setLibelle(trim($libelle));
                    $choix->setEstCorrect(isset($old["choix_correct_$i"]) && $old["choix_correct_$i"] === '1');
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

        // GET ou erreur de validation
        return $this->render('Quiz/question_form.html.twig', [
            'quiz' => $quiz,
            'edit' => false,
            'form' => $form,
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
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        // Récupérer les données du POST pour les choix
        $old = [];
        $errors = [];
        $choiceCount = 2;
        
        // Charger les choix existants pour l'affichage initial
        if ($question->getTypeQuestion() === 'QCM') {
            $choiceCount = count($question->getChoix());
        }

        if ($request->isMethod('POST')) {
            $old = $request->request->all();
            $action = (string) $request->request->get('action', 'save');
            $choiceCount = (int) $request->request->get('choix_count', $choiceCount);
            $choiceCount = max(2, min(5, $choiceCount));
            
            // Action: changer le type de question
            if ($action === 'change_type') {
                $choiceCount = 2;
                return $this->render('Quiz/modifier_question.html.twig', [
                    'quiz' => $quiz,
                    'question' => $question,
                    'edit' => true,
                    'form' => $form,
                    'old' => $old,
                    'errors' => $errors,
                    'choiceCount' => $choiceCount,
                ]);
            }
            
            // Action: ajouter un choix
            if ($action === 'add_choice') {
                $type = $old['typeQuestion'] ?? $question->getTypeQuestion();
                if ($type === 'QCM' && $choiceCount < 5) {
                    $choiceCount++;
                }
                return $this->render('Quiz/modifier_question.html.twig', [
                    'quiz' => $quiz,
                    'question' => $question,
                    'edit' => true,
                    'form' => $form,
                    'old' => $old,
                    'errors' => $errors,
                    'choiceCount' => $choiceCount,
                ]);
            }
            
            // Action: supprimer un choix
            if ($action === 'remove_choice') {
                $type = $old['typeQuestion'] ?? $question->getTypeQuestion();
                if ($type === 'QCM' && $choiceCount > 2) {
                    $choiceCount--;
                }
                return $this->render('Quiz/modifier_question.html.twig', [
                    'quiz' => $quiz,
                    'question' => $question,
                    'edit' => true,
                    'form' => $form,
                    'old' => $old,
                    'errors' => $errors,
                    'choiceCount' => $choiceCount,
                ]);
            }
        }

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $typeQuestion = $form->get('typeQuestion')->getData();
            
            // Supprimer les anciens choix
            foreach ($question->getChoix() as $oldChoix) {
                $em->remove($oldChoix);
            }
            $em->flush();

            // Créer les nouveaux choix selon le type
            if ($typeQuestion === 'VRAI_FAUX') {
                $vfCorrect = (int) ($old['choix_correct_vf'] ?? 0);
                
                $vrai = new Choix();
                $vrai->setLibelle('Vrai');
                $vrai->setEstCorrect($vfCorrect === 0);
                $vrai->setQuestion($question);
                $em->persist($vrai);

                $faux = new Choix();
                $faux->setLibelle('Faux');
                $faux->setEstCorrect($vfCorrect === 1);
                $faux->setQuestion($question);
                $em->persist($faux);
            } else {
                // QCM
                for ($i = 0; $i < $choiceCount; $i++) {
                    $libelle = $old["choix_libelle_$i"] ?? '';
                    if (trim($libelle) === '') continue;
                    
                    $choix = new Choix();
                    $choix->setLibelle(trim($libelle));
                    $choix->setEstCorrect(isset($old["choix_correct_$i"]) && $old["choix_correct_$i"] === '1');
                    $choix->setQuestion($question);
                    $em->persist($choix);
                }
            }

            $em->flush();

            $this->addFlash('success', 'Question modifiée avec succès !');
            return $this->redirectToRoute('admin_quiz_questions', ['id' => $quiz->getId()]);
        }

        // GET ou erreur de validation
        return $this->render('Quiz/modifier_question.html.twig', [
            'quiz' => $quiz,
            'question' => $question,
            'edit' => true,
            'form' => $form,
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

