<?php

namespace App\Controller\Admin;

use App\Entity\EvaluationRessource;
use App\Repository\EvaluationRessourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/evaluation')]
#[IsGranted('ROLE_ADMIN')]
final class AdminEvaluationController extends AbstractController
{
    #[Route('', name: 'app_admin_evaluation_index', methods: ['GET'])]
    public function index(
        Request $request,
        EvaluationRessourceRepository $evaluationRepository
    ): Response {
        $filter = $request->query->get('filter', 'all'); // all, comments, ratings, favorites, reported
        
        $queryBuilder = $evaluationRepository->createQueryBuilder('e')
            ->leftJoin('e.ressource', 'r')
            ->leftJoin('e.utilisateur', 'u')
            ->addSelect('r', 'u')
            ->orderBy('e.dateEvaluation', 'DESC');
        
        switch ($filter) {
            case 'comments':
                $queryBuilder->andWhere('e.commentaire IS NOT NULL');
                break;
            case 'ratings':
                $queryBuilder->andWhere('e.note IS NOT NULL');
                break;
            case 'favorites':
                $queryBuilder->andWhere('e.estFavori = :favori')
                    ->setParameter('favori', true);
                break;
            case 'reported':
                $queryBuilder->andWhere('e.estSignale = :signale')
                    ->setParameter('signale', true);
                break;
        }
        
        $evaluations = $queryBuilder->getQuery()->getResult();
        
        // Calculate statistics
        $stats = [
            'total' => $evaluationRepository->count([]),
            'comments' => $evaluationRepository->createQueryBuilder('e')
                ->select('COUNT(e.id)')
                ->where('e.commentaire IS NOT NULL')
                ->getQuery()
                ->getSingleScalarResult(),
            'ratings' => $evaluationRepository->createQueryBuilder('e')
                ->select('COUNT(e.id)')
                ->where('e.note IS NOT NULL')
                ->getQuery()
                ->getSingleScalarResult(),
            'favorites' => $evaluationRepository->count(['estFavori' => true]),
            'reported' => $evaluationRepository->count(['estSignale' => true]),
        ];

        return $this->render('admin/evaluation/index.html.twig', [
            'evaluations' => $evaluations,
            'stats' => $stats,
            'current_filter' => $filter,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_evaluation_show', methods: ['GET'])]
    public function show(EvaluationRessource $evaluation): Response
    {
        return $this->render('admin/evaluation/show.html.twig', [
            'evaluation' => $evaluation,
        ]);
    }

    #[Route('/{id}/toggle-report', name: 'app_admin_evaluation_toggle_report', methods: ['POST'])]
    public function toggleReport(
        Request $request,
        EvaluationRessource $evaluation,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('toggle-report'.$evaluation->getId(), $request->getPayload()->getString('_token'))) {
            $evaluation->setEstSignale(!$evaluation->isEstSignale());
            $entityManager->flush();
            
            $status = $evaluation->isEstSignale() ? 'signalée' : 'non signalée';
            $this->addFlash('info', "Évaluation {$status}.");
        }

        return $this->redirectToRoute('app_admin_evaluation_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_admin_evaluation_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        EvaluationRessource $evaluation,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$evaluation->getId(), $request->getPayload()->getString('_token'))) {
            $ressource = $evaluation->getRessource();
            
            $entityManager->remove($evaluation);
            $entityManager->flush();
            
            // Refresh the resource to get updated evaluations
            $entityManager->refresh($ressource);
            
            // Update resource average rating
            $ressource->calculateAverageRating();
            $entityManager->flush();
            
            $this->addFlash('success', 'Évaluation supprimée avec succès.');
        }

        return $this->redirectToRoute('app_admin_evaluation_index', [], Response::HTTP_SEE_OTHER);
    }
}
