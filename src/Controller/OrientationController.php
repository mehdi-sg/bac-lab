<?php

namespace App\Controller;

use App\Service\OrientationRecommenderService;
use App\Service\ScoreCalculatorService;
use App\Service\EngagementScoringService;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/orientation')]
#[IsGranted('ROLE_USER')]
class OrientationController extends AbstractController
{
    public function __construct(
        private OrientationRecommenderService $recommenderService,
        private ScoreCalculatorService $scoreCalculator,
        private EngagementScoringService $engagementScoring,
        private ProgramRepository $programRepository
    ) {}

    #[Route('/recommendations', name: 'orientation_recommendations', methods: ['GET', 'POST'])]
    public function recommendations(Request $request): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            $this->addFlash('info', 'Veuillez vous connecter pour accéder aux recommandations d\'orientation.');
            return $this->redirectToRoute('app_login');
        }

        // Check if user has profile and filière
        if (!$user->getProfil() || !$user->getProfil()->getFiliere()) {
            $this->addFlash('warning', 'Veuillez compléter votre profil et sélectionner votre filière.');
            return $this->redirectToRoute('app_profile');
        }

        $filiere = $user->getProfil()->getFiliere()->getNom();
        $filiereNormalized = $this->scoreCalculator->normalizeFiliere($filiere);
        
        if (!$filiereNormalized) {
            $this->addFlash('error', 'Filière non reconnue. Veuillez contacter l\'administrateur.');
            return $this->redirectToRoute('app_profile');
        }

        // Get user scores from session or redirect to score calculator
        $userScores = $request->getSession()->get('user_scores');
        if (!$userScores || !isset($userScores['FG'])) {
            $this->addFlash('info', 'Veuillez d\'abord calculer votre score BAC pour obtenir des recommandations.');
            return $this->redirectToRoute('score_calcul');
        }

        // Get filters from request
        $filters = [
            'university' => $request->query->get('university'),
            'domain' => $request->query->get('domain'),
            'minCutoff' => $request->query->get('minCutoff'),
            'maxCutoff' => $request->query->get('maxCutoff')
        ];

        $geographicBonus = $request->query->getBoolean('geographic_bonus', false);
        $limit = (int) $request->query->get('limit', 10);

        // Get recommendations
        $recommendations = $this->recommenderService->getRecommendations($user, $userScores, [
            'limit' => $limit,
            'geographicBonus' => $geographicBonus,
            'filters' => array_filter($filters)
        ]);

        // Get filter options
        $universities = $this->programRepository->getUniversities();
        $domains = $this->programRepository->getDomains();
        $cutoffStats = $this->programRepository->getCutoffStats();

        // Get user engagement stats
        $engagementStats = $this->engagementScoring->getUserEngagementStats($user);
        $topSubjects = $this->engagementScoring->getTopSubjectsByInterest($user, 5);

        return $this->render('orientation/recommendations.html.twig', [
            'user' => $user,
            'filiere' => $filiereNormalized,
            'filiereLabel' => $this->scoreCalculator->getFiliereLabel($filiereNormalized),
            'userScores' => $userScores,
            'recommendations' => $recommendations,
            'filters' => $filters,
            'universities' => $universities,
            'domains' => $domains,
            'cutoffStats' => $cutoffStats,
            'geographicBonus' => $geographicBonus,
            'limit' => $limit,
            'engagementStats' => $engagementStats,
            'topSubjects' => $topSubjects
        ]);
    }

    #[Route('/what-if', name: 'orientation_what_if', methods: ['POST'])]
    public function whatIf(Request $request): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        // Get base scores from session
        $baseScores = $request->getSession()->get('user_scores');
        if (!$baseScores) {
            return new JsonResponse(['error' => 'No base scores found'], 400);
        }

        // Get modifications from request
        $modifications = $request->request->all();
        
        // Remove non-score fields
        unset($modifications['_token']);
        
        // Convert to float
        foreach ($modifications as $key => $value) {
            if (is_numeric($value)) {
                $modifications[$key] = (float) $value;
            }
        }

        // Get simulation results
        $simulation = $this->recommenderService->getWhatIfSimulation($user, $baseScores, $modifications);

        return new JsonResponse([
            'success' => true,
            'improvements' => $simulation['improvements'],
            'newRecommendations' => array_slice($simulation['simulated'], 0, 5)
        ]);
    }

    #[Route('/simulate-engagement', name: 'orientation_simulate_engagement', methods: ['POST'])]
    public function simulateEngagement(): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        // Simulate engagement data for demo purposes
        $this->engagementScoring->simulateEngagementData($user);

        return new JsonResponse([
            'success' => true,
            'message' => 'Données d\'engagement simulées avec succès'
        ]);
    }

    #[Route('/program/{id}', name: 'orientation_program_detail', methods: ['GET'])]
    public function programDetail(int $id): Response
    {
        $program = $this->programRepository->find($id);
        
        if (!$program) {
            throw $this->createNotFoundException('Programme non trouvé');
        }

        $user = $this->getUser();
        $userScores = $this->getUser() ? $request->getSession()->get('user_scores') : null;
        
        $evaluation = null;
        if ($user && $userScores) {
            // Get detailed evaluation for this program
            $recommendations = $this->recommenderService->getRecommendations($user, $userScores, [
                'limit' => 1000 // Get all to find this specific program
            ]);
            
            foreach ($recommendations as $rec) {
                if ($rec['program']->getId() === $id) {
                    $evaluation = $rec;
                    break;
                }
            }
        }

        return $this->render('orientation/program_detail.html.twig', [
            'program' => $program,
            'evaluation' => $evaluation,
            'user' => $user
        ]);
    }
}