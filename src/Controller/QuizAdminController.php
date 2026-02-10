<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Question;
use App\Entity\Choix;
use App\Entity\Chapitre;
use App\Entity\Matiere;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/quiz')]
class QuizAdminController extends AbstractController
{
    #[Route('/', name: 'admin_quiz_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $quizzes = $em->getRepository(Quiz::class)->findAll();
        
        return $this->render('Quiz/crud.html.twig', [
            'quizzes' => $quizzes,
        ]);
    }

    #[Route('/nouveau', name: 'admin_quiz_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $em): Response
{
    $quiz = new Quiz();
    
    if ($request->isMethod('POST')) {
        try {
            $quiz->setTitre($request->request->get('titre'));
            $quiz->setDescription($request->request->get('description'));
            $quiz->setNiveau($request->request->get('niveau'));
            $quiz->setDuree((int)$request->request->get('duree'));
            $quiz->setNbQuestions((int)$request->request->get('nb_questions'));
            $quiz->setDateCreation(new \DateTime());
            $quiz->setEtat(true);
            
            $em->persist($quiz);
            $em->flush();
            
            $this->addFlash('success', 'Quiz créé avec succès !');
            return $this->redirectToRoute('admin_quiz_index');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la création : ' . $e->getMessage());
        }
    }
    
    return $this->render('Quiz/form.html.twig', [
        'quiz' => $quiz,
        'edit' => false,
    ]);
}

    #[Route('/{id}/modifier', name: 'admin_quiz_edit')]
    public function edit(Quiz $quiz, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $quiz->setTitre($request->request->get('titre'));
            $quiz->setDescription($request->request->get('description'));
            $quiz->setNiveau($request->request->get('niveau'));
            $quiz->setDuree((int)$request->request->get('duree'));
            $quiz->setNbQuestions((int)$request->request->get('nb_questions'));
            
            $em->flush();
            
            $this->addFlash('success', 'Quiz modifié avec succès !');
            return $this->redirectToRoute('admin_quiz_index');
        }
        
        return $this->render('Quiz/form.html.twig', [
            'quiz' => $quiz,
            'edit' => true,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'admin_quiz_delete', methods: ['POST'])]
    public function delete(Quiz $quiz, EntityManagerInterface $em): Response
    {
        $em->remove($quiz);
        $em->flush();
        
        $this->addFlash('success', 'Quiz supprimé avec succès !');
        return $this->redirectToRoute('admin_quiz_index');
    }

    #[Route('/{id}/questions', name: 'admin_quiz_questions')]
    public function questions(Quiz $quiz, EntityManagerInterface $em): Response
    {
        $questions = $em->getRepository(Question::class)->findBy(['quiz' => $quiz]);
        $matieres = $em->getRepository(Matiere::class)->findAll();
        
        return $this->render('Quiz/questions.html.twig', [
            'quiz' => $quiz,
            'questions' => $questions,
            'matieres' => $matieres,
        ]);
    }

    #[Route('/{id}/question/nouvelle', name: 'admin_question_new')]
    public function newQuestion(Quiz $quiz, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $question = new Question();
            $question->setEnonce($request->request->get('enonce'));
            $question->setTypeQuestion($request->request->get('type_question'));
            $question->setScore((int)$request->request->get('score'));
            $question->setQuiz($quiz);
            
            $matiereId = $request->request->get('matiere_id');
            $chapitreId = $request->request->get('chapitre_id');
            
            $matiere = $em->getRepository(Matiere::class)->find($matiereId);
            $chapitre = $em->getRepository(Chapitre::class)->find($chapitreId);
            
            $question->setMatiere($matiere);
            $question->setChapitre($chapitre);
            
            $em->persist($question);
            
            // Ajouter les choix
            $choixLibelles = $request->request->all('choix_libelle');
            $choixCorrects = $request->request->all('choix_correct');
            
            foreach ($choixLibelles as $index => $libelle) {
                if (!empty($libelle)) {
                    $choix = new Choix();
                    $choix->setLibelle($libelle);
                    $choix->setEstCorrect(isset($choixCorrects[$index]));
                    $choix->setQuestion($question);
                    $em->persist($choix);
                }
            }
            
            $em->flush();
            
            $this->addFlash('success', 'Question ajoutée avec succès !');
            return $this->redirectToRoute('admin_quiz_questions', ['id' => $quiz->getId()]);
        }
        
        $matieres = $em->getRepository(Matiere::class)->findAll();
        
        return $this->render('Quiz/question_form.html.twig', [
            'quiz' => $quiz,
            'question' => null,
            'matieres' => $matieres,
            'edit' => false,
        ]);
    }

    #[Route('/question/{id}/supprimer', name: 'admin_question_delete', methods: ['POST'])]
    public function deleteQuestion(Question $question, EntityManagerInterface $em): Response
    {
        $quizId = $question->getQuiz()->getId();
        $em->remove($question);
        $em->flush();
        
        $this->addFlash('success', 'Question supprimée avec succès !');
        return $this->redirectToRoute('admin_quiz_questions', ['id' => $quizId]);
    }
}