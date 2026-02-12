<?php

namespace App\Controller;

use App\Entity\EvaluationRessource;
use App\Entity\Ressource;
use App\Form\EvaluationRessourceType;
use App\Repository\EvaluationRessourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/evaluation')]
#[IsGranted('ROLE_USER')]
final class EvaluationController extends AbstractController
{
    #[Route('/mes-evaluations', name: 'app_evaluation_index', methods: ['GET'])]
    public function index(EvaluationRessourceRepository $evaluationRepository): Response
    {
        // Get current user's evaluations
        $user = $this->getUser();
        $evaluations = $evaluationRepository->findBy(
            ['utilisateur' => $user],
            ['dateEvaluation' => 'DESC']
        );

        return $this->render('evaluation/index.html.twig', [
            'evaluations' => $evaluations,
        ]);
    }

    #[Route('/ressource/{id}/evaluer', name: 'app_evaluation_create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        Ressource $ressource,
        EntityManagerInterface $entityManager,
        EvaluationRessourceRepository $evaluationRepository
    ): Response {
        $user = $this->getUser();
        
        // Check if user already evaluated this resource
        $existingEvaluation = $evaluationRepository->findOneBy([
            'ressource' => $ressource,
            'utilisateur' => $user
        ]);
        
        if ($existingEvaluation) {
            $this->addFlash('warning', 'Vous avez déjà évalué cette ressource. Vous pouvez la modifier.');
            return $this->redirectToRoute('app_evaluation_edit', ['id' => $existingEvaluation->getId()]);
        }

        // Handle direct form submission (from resource page)
        if ($request->isMethod('POST')) {
            $note = $request->request->get('note');
            $commentaire = $request->request->get('commentaire');
            
            if ($note) {
                $evaluation = new EvaluationRessource();
                $evaluation->setRessource($ressource);
                $evaluation->setUtilisateur($user);
                $evaluation->setNote((int)$note);
                
                if ($commentaire) {
                    $evaluation->setCommentaire($commentaire);
                }
                
                $entityManager->persist($evaluation);
                $entityManager->flush();
                
                // Refresh the resource to get updated evaluations
                $entityManager->refresh($ressource);
                
                // Update resource average rating
                $ressource->calculateAverageRating();
                $entityManager->flush();

                $this->addFlash('success', 'Votre évaluation a été enregistrée avec succès.');
                return $this->redirectToRoute('app_bibliotheque_show', ['id' => $ressource->getId()]);
            }
        }

        // Show form page
        $evaluation = new EvaluationRessource();
        $evaluation->setRessource($ressource);
        $evaluation->setUtilisateur($user);
        
        $form = $this->createForm(EvaluationRessourceType::class, $evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($evaluation);
            $entityManager->flush();
            
            // Refresh the resource to get updated evaluations
            $entityManager->refresh($ressource);
            
            // Update resource average rating
            $ressource->calculateAverageRating();
            $entityManager->flush();

            $this->addFlash('success', 'Votre évaluation a été enregistrée avec succès.');
            return $this->redirectToRoute('app_bibliotheque_show', ['id' => $ressource->getId()]);
        }

        return $this->render('evaluation/create.html.twig', [
            'evaluation' => $evaluation,
            'ressource' => $ressource,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_evaluation_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        EvaluationRessource $evaluation,
        EntityManagerInterface $entityManager
    ): Response {
        // Check if user owns this evaluation
        if ($evaluation->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cette évaluation.');
        }

        $form = $this->createForm(EvaluationRessourceType::class, $evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ressource = $evaluation->getRessource();
            
            $entityManager->flush();
            
            // Refresh the resource to get updated evaluations
            $entityManager->refresh($ressource);
            
            // Update resource average rating
            $ressource->calculateAverageRating();
            $entityManager->flush();

            $this->addFlash('success', 'Votre évaluation a été modifiée avec succès.');
            return $this->redirectToRoute('app_evaluation_index');
        }

        return $this->render('evaluation/edit.html.twig', [
            'evaluation' => $evaluation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evaluation_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        EvaluationRessource $evaluation,
        EntityManagerInterface $entityManager
    ): Response {
        // Check if user owns this evaluation
        if ($evaluation->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer cette évaluation.');
        }

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

        return $this->redirectToRoute('app_evaluation_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/toggle-favori', name: 'app_evaluation_toggle_favori', methods: ['POST'])]
    public function toggleFavori(
        Request $request,
        EvaluationRessource $evaluation,
        EntityManagerInterface $entityManager
    ): Response {
        // Check if user owns this evaluation
        if ($evaluation->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('toggle-favori'.$evaluation->getId(), $request->getPayload()->getString('_token'))) {
            $evaluation->setEstFavori(!$evaluation->isEstFavori());
            $entityManager->flush();
            
            $status = $evaluation->isEstFavori() ? 'ajoutée aux favoris' : 'retirée des favoris';
            $this->addFlash('success', "Ressource {$status}.");
        }

        return $this->redirectToRoute('app_evaluation_index', [], Response::HTTP_SEE_OTHER);
    }
}
