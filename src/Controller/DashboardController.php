<?php

namespace App\Controller;

use App\Repository\EvaluationRessourceRepository;
use App\Repository\RessourceRepository;
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
        
        // Get user's resources (TODO: filter by user when utilisateur field is added)
        $userRessources = $ressourceRepository->findBy([], ['dateAjout' => 'DESC'], 5);
        
        // Get user's evaluations
        $userEvaluations = $evaluationRepository->findBy(
            ['utilisateur' => $user],
            ['dateEvaluation' => 'DESC'],
            5
        );
        
        // Get user's favorites
        $userFavorites = $evaluationRepository->findBy(
            ['utilisateur' => $user, 'estFavori' => true],
            ['dateFavori' => 'DESC'],
            5
        );
        
        // Calculate statistics
        $stats = [
            'total_ressources' => count($ressourceRepository->findAll()), // TODO: filter by user
            'total_evaluations' => $evaluationRepository->count(['utilisateur' => $user]),
            'total_favorites' => $evaluationRepository->count(['utilisateur' => $user, 'estFavori' => true]),
            'total_comments' => $evaluationRepository->createQueryBuilder('e')
                ->select('COUNT(e.id)')
                ->where('e.utilisateur = :user')
                ->andWhere('e.commentaire IS NOT NULL')
                ->setParameter('user', $user)
                ->getQuery()
                ->getSingleScalarResult(),
        ];

        return $this->render('dashboard/index.html.twig', [
            'stats' => $stats,
            'recent_ressources' => $userRessources,
            'recent_evaluations' => $userEvaluations,
            'favorites' => $userFavorites,
        ]);
    }
}
