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
        $errors = [];
        $old = [];
        $chapitres = [];
        
        $matieres = $em->getRepository(Matiere::class)->findAll();
        
        if ($request->isMethod('POST')) {
            $old = $request->request->all();
            $action = (string)$request->request->get('action', 'save');
            
            $matiereId = (int)$request->request->get('matiere_id', 0);
            
            // Charger les chapitres si matière sélectionnée
            if ($matiereId > 0) {
                $chapitres = $em->getRepository(Chapitre::class)->findBy(
                    ['matiere' => $matiereId],
                    ['titre' => 'ASC']
                );
            }
            
            // Rafraîchir chapitres
            if ($action === 'refresh_chapitres') {
                return $this->render('Quiz/form.html.twig', [
                    'quiz' => $quiz,
                    'edit' => false,
                    'errors' => $errors,
                    'old' => $old,
                    'matieres' => $matieres,
                    'chapitres' => $chapitres,
                ]);
            }
            
            try {
                $titre = trim((string)$request->request->get('titre'));
                $niveau = (string)$request->request->get('niveau');
                $duree = (int)$request->request->get('duree', 0);
                $description = (string)$request->request->get('description');
                $matiereId = (int)$request->request->get('matiere_id', 0);
                $chapitreId = (int)$request->request->get('chapitre_id', 0);

                // Validation
                if ($titre === '') {
                    $errors[] = "Le titre est obligatoire.";
                }
                if (!in_array($niveau, ['Facile', 'Moyen', 'Difficile'], true)) {
                    $errors[] = "Le niveau est obligatoire.";
                }
                if ($duree < 1 || $duree > 180) {
                    $errors[] = "La durée doit être entre 1 et 180 minutes.";
                }
                if ($matiereId <= 0) {
                    $errors[] = "La matière est obligatoire.";
                }
                if ($chapitreId <= 0) {
                    $errors[] = "Le chapitre est obligatoire.";
                }

                $matiere = $matiereId > 0 ? $em->getRepository(Matiere::class)->find($matiereId) : null;
                $chapitre = $chapitreId > 0 ? $em->getRepository(Chapitre::class)->find($chapitreId) : null;

                if ($matiere && $chapitre && method_exists($chapitre, 'getMatiere') && $chapitre->getMatiere()?->getId() !== $matiere->getId()) {
                    $errors[] = "Ce chapitre ne correspond pas à la matière choisie.";
                }

                if (!empty($errors)) {
                    return $this->render('Quiz/form.html.twig', [
                        'quiz' => $quiz,
                        'edit' => false,
                        'errors' => $errors,
                        'old' => $old,
                        'matieres' => $matieres,
                        'chapitres' => $chapitres,
                    ]);
                }

                $quiz->setTitre($titre);
                $quiz->setDescription($description);
                $quiz->setNiveau($niveau);
                $quiz->setDuree($duree);
                $quiz->setNbQuestions(0);
                $quiz->setDateCreation(new \DateTime());
                $quiz->setEtat(true);
                $quiz->setMatiere($matiere);
                $quiz->setChapitre($chapitre);
                
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
            'errors' => $errors,
            'old' => $old,
            'matieres' => $matieres,
            'chapitres' => $chapitres,
        ]);
    }

    #[Route('/{id}/modifier', name: 'admin_quiz_edit', methods: ['GET', 'POST'])]
    public function edit(Quiz $quiz, Request $request, EntityManagerInterface $em): Response
    {
        $errors = [];
        $old = [];
        $chapitres = [];
        
        $matieres = $em->getRepository(Matiere::class)->findAll();
        
        // Charger les chapitres de la matière du quiz
        if ($quiz->getMatiere()) {
            $chapitres = $em->getRepository(Chapitre::class)->findBy(
                ['matiere' => $quiz->getMatiere()],
                ['titre' => 'ASC']
            );
        }
        
        if ($request->isMethod('POST')) {
            $old = $request->request->all();
            $action = (string)$request->request->get('action', 'save');
            
            $matiereId = (int)$request->request->get('matiere_id', 0);
            
            // Charger les chapitres si matière sélectionnée
            if ($matiereId > 0) {
                $chapitres = $em->getRepository(Chapitre::class)->findBy(
                    ['matiere' => $matiereId],
                    ['titre' => 'ASC']
                );
            }
            
            // Rafraîchir chapitres
            if ($action === 'refresh_chapitres') {
                return $this->render('Quiz/form.html.twig', [
                    'quiz' => $quiz,
                    'edit' => true,
                    'errors' => $errors,
                    'old' => $old,
                    'matieres' => $matieres,
                    'chapitres' => $chapitres,
                ]);
            }
            
            try {
                $titre = trim((string)$request->request->get('titre'));
                $niveau = (string)$request->request->get('niveau');
                $duree = (int)$request->request->get('duree', 0);
                $description = (string)$request->request->get('description');
                $matiereId = (int)$request->request->get('matiere_id', 0);
                $chapitreId = (int)$request->request->get('chapitre_id', 0);

                // Validation
                if ($titre === '') {
                    $errors[] = "Le titre est obligatoire.";
                }
                if (!in_array($niveau, ['Facile', 'Moyen', 'Difficile'], true)) {
                    $errors[] = "Le niveau est obligatoire.";
                }
                if ($duree < 1 || $duree > 180) {
                    $errors[] = "La durée doit être entre 1 et 180 minutes.";
                }
                if ($matiereId <= 0) {
                    $errors[] = "La matière est obligatoire.";
                }
                if ($chapitreId <= 0) {
                    $errors[] = "Le chapitre est obligatoire.";
                }

                $matiere = $matiereId > 0 ? $em->getRepository(Matiere::class)->find($matiereId) : null;
                $chapitre = $chapitreId > 0 ? $em->getRepository(Chapitre::class)->find($chapitreId) : null;

                if ($matiere && $chapitre && method_exists($chapitre, 'getMatiere') && $chapitre->getMatiere()?->getId() !== $matiere->getId()) {
                    $errors[] = "Ce chapitre ne correspond pas à la matière choisie.";
                }

                if (!empty($errors)) {
                    return $this->render('Quiz/form.html.twig', [
                        'quiz' => $quiz,
                        'edit' => true,
                        'errors' => $errors,
                        'old' => $old,
                        'matieres' => $matieres,
                        'chapitres' => $chapitres,
                    ]);
                }

                $quiz->setTitre($titre);
                $quiz->setDescription($description);
                $quiz->setNiveau($niveau);
                $quiz->setDuree($duree);
                $quiz->setMatiere($matiere);
                $quiz->setChapitre($chapitre);
                
                $em->flush();
                
                $this->addFlash('success', 'Quiz modifié avec succès !');
                return $this->redirectToRoute('admin_quiz_index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la modification : ' . $e->getMessage());
            }
        }
        
        return $this->render('Quiz/form.html.twig', [
            'quiz' => $quiz,
            'edit' => true,
            'errors' => $errors,
            'old' => $old,
            'matieres' => $matieres,
            'chapitres' => $chapitres,
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

    // La route admin_question_new a été déplacée vers QuestionAdminController.php
    // pour centraliser la logique de validation
}
