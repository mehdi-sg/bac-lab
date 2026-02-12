<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Question;
use App\Entity\Choix;
use App\Entity\Chapitre;
use App\Entity\Matiere;
use App\Form\QuizType;
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
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $quiz->setNbQuestions(0);
            $quiz->setDateCreation(new \DateTime());
            $quiz->setEtat(true);
            
            $em->persist($quiz);
            $em->flush();
            
            $this->addFlash('success', 'Quiz créé avec succès !');
            return $this->redirectToRoute('admin_quiz_index');
        }
        
        return $this->render('Quiz/form.html.twig', [
            'quiz' => $quiz,
            'edit' => false,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/modifier', name: 'admin_quiz_edit', methods: ['GET', 'POST'])]
    public function edit(Quiz $quiz, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            
            $this->addFlash('success', 'Quiz modifié avec succès !');
            return $this->redirectToRoute('admin_quiz_index');
        }
        
        return $this->render('Quiz/form.html.twig', [
            'quiz' => $quiz,
            'edit' => true,
            'form' => $form,
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
        
        return $this->render('Quiz/questions.html.twig', [
            'quiz' => $quiz,
            'questions' => $questions,
        ]);
    }
}
