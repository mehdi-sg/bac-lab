<?php

namespace App\Controller\Admin;

use App\Repository\EvaluationRessourceRepository;
use App\Repository\RessourceRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
final class AdminDashboardController extends AbstractController
{
    #[Route('', name: 'app_admin_dashboard', methods: ['GET'])]
    #[Route('/dashboard', name: 'app_admin_dashboard_alt', methods: ['GET'])]
    public function index(
        RessourceRepository $ressourceRepository,
        EvaluationRessourceRepository $evaluationRepository,
        UtilisateurRepository $utilisateurRepository
    ): Response {
        // Get all resources
        $allRessources = $ressourceRepository->findAll();
        
        // Calculate statistics
        $stats = [
            'total_users' => $utilisateurRepository->count([]),
            'total_ressources' => count($allRessources),
            'ressources_en_attente' => count(array_filter($allRessources, fn($r) => $r->getStatut() === 'EN_ATTENTE')),
            'ressources_validees' => count(array_filter($allRessources, fn($r) => $r->getStatut() === 'VALIDEE')),
            'ressources_rejetees' => count(array_filter($allRessources, fn($r) => $r->getStatut() === 'REJETEE')),
            'total_evaluations' => $evaluationRepository->count([]),
            'total_comments' => $evaluationRepository->createQueryBuilder('e')
                ->select('COUNT(e.id)')
                ->where('e.commentaire IS NOT NULL')
                ->getQuery()
                ->getSingleScalarResult(),
            'total_favorites' => $evaluationRepository->count(['estFavori' => true]),
            'reported_evaluations' => $evaluationRepository->count(['estSignale' => true]),
        ];
        
        // Get recent resources
        $recentRessources = $ressourceRepository->findBy([], ['dateAjout' => 'DESC'], 5);
        
        // Get pending resources
        $pendingRessources = $ressourceRepository->findBy(
            ['statut' => 'EN_ATTENTE'],
            ['dateAjout' => 'DESC'],
            5
        );
        
        // Get recent evaluations
        $recentEvaluations = $evaluationRepository->findBy([], ['dateEvaluation' => 'DESC'], 5);
        
        // Get reported evaluations
        $reportedEvaluations = $evaluationRepository->findBy(
            ['estSignale' => true],
            ['dateEvaluation' => 'DESC'],
            5
        );
        
        // Calculate resource type distribution
        $typeDistribution = [
            'PDF' => 0,
            'VIDEO' => 0,
            'LIEN' => 0,
        ];
        foreach ($allRessources as $ressource) {
            $type = $ressource->getTypeFichier();
            if (isset($typeDistribution[$type])) {
                $typeDistribution[$type]++;
            }
        }
        
        // Calculate category distribution
        $categoryDistribution = [];
        foreach ($allRessources as $ressource) {
            $category = $ressource->getCategorie() ?? 'Non catégorisé';
            if (!isset($categoryDistribution[$category])) {
                $categoryDistribution[$category] = 0;
            }
            $categoryDistribution[$category]++;
        }

        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $stats,
            'recent_ressources' => $recentRessources,
            'pending_ressources' => $pendingRessources,
            'recent_evaluations' => $recentEvaluations,
            'reported_evaluations' => $reportedEvaluations,
            'type_distribution' => $typeDistribution,
            'category_distribution' => $categoryDistribution,
        ]);
    }
}
