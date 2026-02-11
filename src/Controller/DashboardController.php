<?php

namespace App\Controller;

use App\Repository\RessourceRepository;
use App\Repository\EvaluationRessourceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard')]
#[IsGranted('ROLE_USER')]
final class DashboardController extends AbstractController
{
    #[Route('', name: 'app_dashboard', methods: ['GET'])]
    public function index(
        RessourceRepository $ressourceRepository,
        EvaluationRessourceRepository $evaluationRepository
    ): Response {
        $user = $this->getUser();
        
        // Get user evaluations
        $myEvaluations = $evaluationRepository->findBy(['utilisateur' => $user]);
        
        // Count favoris
        $favorisCount = $evaluationRepository->count([
            'utilisateur' => $user,
            'estFavori' => true
        ]);
        
        // Count commentaires
        $commentairesCount = 0;
        foreach ($myEvaluations as $eval) {
            if ($eval->getCommentaire()) {
                $commentairesCount++;
            }
        }
        
        // Recent evaluations (5 dernières)
        $recentEvaluations = $evaluationRepository->findBy(
            ['utilisateur' => $user],
            ['dateEvaluation' => 'DESC'],
            5
        );
        
        // Favoris (5 derniers)
        $favoris = $evaluationRepository->findBy(
            ['utilisateur' => $user, 'estFavori' => true],
            ['dateFavori' => 'DESC'],
            5
        );
        
        return $this->render('dashboard/index.html.twig', [
            'totalRessources' => 0, // Not available without user relation
            'totalEvaluations' => count($myEvaluations),
            'totalFavoris' => $favorisCount,
            'totalCommentaires' => $commentairesCount,
            'recentRessources' => [], // Not available without user relation
            'recentEvaluations' => $recentEvaluations,
            'favoris' => $favoris,
        ]);
    }
}
