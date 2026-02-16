<?php

namespace App\Controller;

use App\Entity\Chapitre;
use App\Entity\Matiere;
use App\Entity\Filiere;
use App\Entity\Quiz;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class ChapitreApiController extends AbstractController
{
    #[Route('/filieres', name: 'api_filieres', methods: ['GET'])]
    public function getAllFilieres(EntityManagerInterface $em): JsonResponse
    {
        $filieres = $em->getRepository(Filiere::class)->findBy(
            ['actif' => true],
            ['nom' => 'ASC']
        );

        $data = [];
        foreach ($filieres as $filiere) {
            $data[] = [
                'id' => $filiere->getId(),
                'nom' => $filiere->getNom(),
                'niveau' => $filiere->getNiveau(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/matieres', name: 'api_matieres', methods: ['GET'])]
    public function getAllMatieres(EntityManagerInterface $em): JsonResponse
    {
        $filiereId = $_GET['filiere'] ?? null;
        
        $criteria = ['actif' => true];
        if ($filiereId) {
            $criteria['filiere'] = $filiereId;
        }
        
        $matieres = $em->getRepository(Matiere::class)->findBy(
            $criteria,
            ['nom' => 'ASC']
        );

        $data = [];
        foreach ($matieres as $matiere) {
            $data[] = [
                'id' => $matiere->getId(),
                'nom' => $matiere->getNom(),
                'filiere' => $matiere->getFiliere()?->getNom(),
                'filiereId' => $matiere->getFiliere()?->getId(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/matieres/filiere/{id}', name: 'api_matieres_by_filiere', methods: ['GET'])]
    public function getMatieresByFiliere(int $id, EntityManagerInterface $em): JsonResponse
    {
        $matieres = $em->getRepository(Matiere::class)->findBy(
            ['filiere' => $id, 'actif' => true],
            ['nom' => 'ASC']
        );

        $data = [];
        foreach ($matieres as $matiere) {
            $data[] = [
                'id' => $matiere->getId(),
                'nom' => $matiere->getNom(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/chapitres/matiere/{id}', name: 'api_chapitres_by_matiere', methods: ['GET'])]
    public function getByMatiere(int $id, EntityManagerInterface $em): JsonResponse
    {
        $chapitres = $em->getRepository(Chapitre::class)->findBy(
            ['matiere' => $id, 'actif' => true],
            ['ordre' => 'ASC']
        );

        $data = [];
        foreach ($chapitres as $chapitre) {
            $data[] = [
                'id' => $chapitre->getId(),
                'titre' => $chapitre->getTitre(),
                'contenu' => $chapitre->getContenu(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/quiz/search', name: 'api_quiz_search', methods: ['GET'])]
    public function searchQuiz(EntityManagerInterface $em): JsonResponse
    {
        $matiereId = $_GET['matiere'] ?? null;
        $chapitreId = $_GET['chapitre'] ?? null;
        $niveau = $_GET['niveau'] ?? null;

        if (!$matiereId || !$chapitreId || !$niveau) {
            return $this->json([
                'found' => false,
                'message' => 'Paramètres manquants'
            ]);
        }

        // Search for quiz matching criteria (without etat constraint for flexibility)
        $quiz = $em->getRepository(Quiz::class)->findOneBy([
            'matiere' => $matiereId,
            'chapitre' => $chapitreId,
            'niveau' => $niveau
        ]);

        if (!$quiz) {
            return $this->json([
                'found' => false,
                'message' => 'Aucun quiz disponible pour ces critères'
            ]);
        }

        return $this->json([
            'found' => true,
            'quiz' => [
                'id' => $quiz->getId(),
                'titre' => $quiz->getTitre(),
                'nbQuestions' => $quiz->getNbQuestions(),
                'duree' => $quiz->getDuree(),
                'description' => $quiz->getDescription()
            ]
        ]);
    }
}
