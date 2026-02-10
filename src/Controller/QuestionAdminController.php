<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Question;
use App\Entity\Choix;
use App\Entity\Chapitre;
use App\Entity\Matiere;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/nouveau', name: 'admin_quiz_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $quiz = new Quiz();

        if ($request->isMethod('POST')) {
            try {
                $quiz->setTitre((string)$request->request->get('titre'));
                $quiz->setDescription((string)$request->request->get('description'));
                $quiz->setNiveau((string)$request->request->get('niveau'));
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

    #[Route('/{id}/modifier', name: 'admin_quiz_edit', methods: ['GET', 'POST'])]
    public function edit(Quiz $quiz, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $quiz->setTitre((string)$request->request->get('titre'));
            $quiz->setDescription((string)$request->request->get('description'));
            $quiz->setNiveau((string)$request->request->get('niveau'));
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

    #[Route('/{id}/questions', name: 'admin_quiz_questions', methods: ['GET'])]
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

    // ==========================
    // NOUVELLE QUESTION + CHOIX
    // ==========================
    #[Route('/{id}/question/nouvelle', name: 'admin_question_new', methods: ['GET', 'POST'])]
    public function newQuestion(Quiz $quiz, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
    
            $enonce = trim((string)$request->request->get('enonce'));
            $type = (string)$request->request->get('type_question');
            $score = (int)$request->request->get('score', 1);
    
            $matiereId = (int)$request->request->get('matiere_id');
            $chapitreId = (int)$request->request->get('chapitre_id');
    
            $choixLibelles = $request->request->all('choix_libelle');
            $choixCorrects = $request->request->all('choix_correct');
    
            // Validations minimales
            if ($enonce === '' || $type === '' || $matiereId <= 0 || $chapitreId <= 0) {
                $this->addFlash('error', 'Veuillez remplir tous les champs obligatoires.');
                return $this->redirectToRoute('admin_question_new', ['id' => $quiz->getId()]);
            }
    
            if (empty($choixLibelles)) {
                $this->addFlash('error', 'Ajoutez au moins un choix.');
                return $this->redirectToRoute('admin_question_new', ['id' => $quiz->getId()]);
            }
    
            if (empty($choixCorrects)) {
                $this->addFlash('error', 'Sélectionnez au moins une bonne réponse.');
                return $this->redirectToRoute('admin_question_new', ['id' => $quiz->getId()]);
            }
    
            $matiere = $em->getRepository(Matiere::class)->find($matiereId);
            $chapitre = $em->getRepository(Chapitre::class)->find($chapitreId);
    
            if (!$matiere || !$chapitre) {
                $this->addFlash('error', 'Matière ou chapitre invalide.');
                return $this->redirectToRoute('admin_question_new', ['id' => $quiz->getId()]);
            }
    
            // 1) Enregistrer Question
            $question = new Question();
            $question->setEnonce($enonce);
            $question->setTypeQuestion($type);
            $question->setScore($score);
            $question->setQuiz($quiz);
            $question->setMatiere($matiere);
            $question->setChapitre($chapitre);
    
            $em->persist($question);
            $em->flush(); // crée l'id_question
    
            // 2) Enregistrer Choix
            foreach ($choixLibelles as $index => $libelle) {
                $libelle = trim((string)$libelle);
                if ($libelle === '') continue;
    
                $choix = new Choix();
                $choix->setLibelle($libelle);
                $choix->setEstCorrect(in_array((string)$index, $choixCorrects, true));
                $choix->setQuestion($question);
    
                $em->persist($choix);
            }
    
            $em->flush();
    
            $this->addFlash('success', 'Question ajoutée avec succès !');
    
            // ✅ Redirection vers /admin/quiz/{id}/questions
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
    

    // ==========================
    // SUPPRIMER QUESTION
    // ==========================
    #[Route('/question/{id}/supprimer', name: 'admin_question_delete', methods: ['POST'])]
    public function deleteQuestion(Question $question, EntityManagerInterface $em): Response
    {
        $quizId = $question->getQuiz()->getId();

        // Si tu n'as pas cascade remove/orphanRemoval, supprimer les choix avant
        foreach ($question->getChoix() as $c) {
            $em->remove($c);
        }

        $em->remove($question);
        $em->flush();

        $this->addFlash('success', 'Question supprimée avec succès !');
        return $this->redirectToRoute('admin_quiz_questions', ['id' => $quizId]);
    }

    // ==========================
    // API : chapitres par matière
    // ==========================
    #[Route('/api/chapitres/matiere/{matiereId}', name: 'api_chapitres_matiere', methods: ['GET'])]
    public function chapitresByMatiere(EntityManagerInterface $em, int $matiereId): JsonResponse
    {
        $chapitres = $em->getRepository(Chapitre::class)->findBy(
            ['matiere' => $matiereId],
            ['titre' => 'ASC']
        );

        $data = [];
        foreach ($chapitres as $chapitre) {
            $data[] = [
                'id' => $chapitre->getId(),
                'titre' => $chapitre->getTitre(),
            ];
        }

        return $this->json($data);
    }
}
