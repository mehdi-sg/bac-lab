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
class DashboardController extends AbstractController
{
    public function __construct(
        private RessourceRepository $ressourceRepository,
        private EvaluationRessourceRepository $evaluationRepository
    ) {
    }

    #[Route('', name: 'app_dashboard')]
    public function index(): Response
    {
        $user = $this->getUser();
        
        // Récupérer les statistiques de l'utilisateur
        $allEvaluations = $this->evaluationRepository->findBy(['utilisateur' => $user]);
        
        $stats = [
            'totalEvaluations' => count(array_filter($allEvaluations, fn($e) => $e->hasRating())),
            'totalCommentaires' => count(array_filter($allEvaluations, fn($e) => $e->hasComment())),
            'totalFavoris' => count(array_filter($allEvaluations, fn($e) => $e->isEstFavori())),
        ];
        
        // Récupérer les dernières évaluations
        $recentEvaluations = array_slice(
            array_filter($allEvaluations, fn($e) => $e->hasRating()),
            0,
            5
        );
        
        // Récupérer les derniers commentaires
        $recentComments = array_slice(
            array_filter($allEvaluations, fn($e) => $e->hasComment()),
            0,
            5
        );
        
        // Récupérer les favoris récents
        $recentFavoris = array_slice(
            array_filter($allEvaluations, fn($e) => $e->isEstFavori()),
            0,
            6
        );

        return $this->render('dashboard/index.html.twig', [
            'stats' => $stats,
            'recentEvaluations' => $recentEvaluations,
            'recentComments' => $recentComments,
            'recentFavoris' => $recentFavoris,
        ]);
    }

    #[Route('/evaluations', name: 'app_dashboard_evaluations')]
    public function evaluations(): Response
    {
        $user = $this->getUser();
        
        // Récupérer toutes les évaluations de l'utilisateur
        $allEvaluations = $this->evaluationRepository->findBy(
            ['utilisateur' => $user],
            ['dateEvaluation' => 'DESC']
        );
        
        // Filtrer uniquement celles qui ont des notes
        $evaluations = array_filter($allEvaluations, fn($e) => $e->hasRating());

        return $this->render('dashboard/evaluations.html.twig', [
            'evaluations' => $evaluations,
        ]);
    }

    #[Route('/commentaires', name: 'app_dashboard_commentaires')]
    public function commentaires(): Response
    {
        $user = $this->getUser();
        
        // Récupérer toutes les évaluations avec commentaires de l'utilisateur
        $evaluations = $this->evaluationRepository->findBy(
            ['utilisateur' => $user],
            ['dateCommentaire' => 'DESC']
        );
        
        // Filtrer uniquement celles qui ont des commentaires
        $commentaires = array_filter($evaluations, fn($e) => $e->hasComment());

        return $this->render('dashboard/commentaires.html.twig', [
            'commentaires' => $commentaires,
        ]);
    }

    #[Route('/telechargements', name: 'app_dashboard_telechargements')]
    public function telechargements(): Response
    {
        // TODO: Implémenter l'historique des téléchargements
        // Pour l'instant, on affiche un message
        
        return $this->render('dashboard/telechargements.html.twig', [
            'message' => 'Fonctionnalité en cours de développement',
        ]);
    }

    #[Route('/favoris', name: 'app_dashboard_favoris')]
    public function favoris(): Response
    {
        $user = $this->getUser();
        
        // Récupérer toutes les évaluations marquées comme favoris
        $favorisEvaluations = $this->evaluationRepository->findBy(
            ['utilisateur' => $user, 'estFavori' => true],
            ['dateFavori' => 'DESC']
        );

        return $this->render('dashboard/favoris.html.twig', [
            'favoris' => $favorisEvaluations,
        ]);
    }
    
    #[Route('/ressources', name: 'app_dashboard_ressources')]
    public function ressources(): Response
    {
        // TODO: Implémenter la liste des ressources créées par l'utilisateur
        // Nécessite d'ajouter une relation ManyToOne vers Utilisateur dans l'entité Ressource
        
        return $this->render('dashboard/ressources.html.twig', [
            'message' => 'Fonctionnalité en cours de développement',
        ]);
    }
}
