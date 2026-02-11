<?php

namespace App\Controller;

use App\Entity\EvaluationRessource;
use App\Entity\Ressource;
use App\Repository\EvaluationRessourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/evaluation')]
#[IsGranted('ROLE_USER')]
final class EvaluationRessourceController extends AbstractController
{
    #[Route('/favoris/toggle/{id}', name: 'app_evaluation_favoris_toggle', methods: ['POST'])]
    public function toggleFavoris(
        Ressource $ressource,
        EvaluationRessourceRepository $evaluationRepo,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();
        
        // Chercher une évaluation existante
        $evaluation = $evaluationRepo->findOneBy([
            'ressource' => $ressource,
            'utilisateur' => $user
        ]);
        
        if (!$evaluation) {
            // Créer une nouvelle évaluation
            $evaluation = new EvaluationRessource();
            $evaluation->setRessource($ressource);
            $evaluation->setUtilisateur($user);
            $evaluation->setEstFavori(true);
            $evaluation->setDateFavori(new \DateTime());
            $em->persist($evaluation);
            $message = 'Ressource ajoutée aux favoris';
        } else {
            // Toggle le statut favori
            $evaluation->setEstFavori(!$evaluation->isEstFavori());
            if ($evaluation->isEstFavori()) {
                $evaluation->setDateFavori(new \DateTime());
                $message = 'Ressource ajoutée aux favoris';
            } else {
                $evaluation->setDateFavori(null);
                $message = 'Ressource retirée des favoris';
            }
        }
        
        $em->flush();
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('app_bibliotheque_show', ['id' => $ressource->getId()]);
    }
    
    #[Route('/note/{id}', name: 'app_evaluation_note', methods: ['POST'])]
    public function addNote(
        Ressource $ressource,
        Request $request,
        EvaluationRessourceRepository $evaluationRepo,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();
        $note = (int) $request->request->get('note');
        
        if ($note < 1 || $note > 5) {
            $this->addFlash('error', 'La note doit être entre 1 et 5');
            return $this->redirectToRoute('app_bibliotheque_show', ['id' => $ressource->getId()]);
        }
        
        // Chercher une évaluation existante
        $evaluation = $evaluationRepo->findOneBy([
            'ressource' => $ressource,
            'utilisateur' => $user
        ]);
        
        if (!$evaluation) {
            $evaluation = new EvaluationRessource();
            $evaluation->setRessource($ressource);
            $evaluation->setUtilisateur($user);
            $em->persist($evaluation);
        }
        
        $evaluation->setNote($note);
        $evaluation->setDateEvaluation(new \DateTime());
        
        $em->flush();
        
        // Refresh pour recalculer la moyenne
        $em->refresh($ressource);
        $ressource->calculateAverageRating();
        $em->flush();
        
        $this->addFlash('success', 'Votre note a été enregistrée');
        return $this->redirectToRoute('app_bibliotheque_show', ['id' => $ressource->getId()]);
    }
    
    #[Route('/commentaire/{id}', name: 'app_evaluation_commentaire', methods: ['POST'])]
    public function addCommentaire(
        Ressource $ressource,
        Request $request,
        EvaluationRessourceRepository $evaluationRepo,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();
        $commentaire = trim($request->request->get('commentaire'));
        
        if (empty($commentaire)) {
            $this->addFlash('error', 'Le commentaire ne peut pas être vide');
            return $this->redirectToRoute('app_bibliotheque_show', ['id' => $ressource->getId()]);
        }
        
        // Chercher une évaluation existante
        $evaluation = $evaluationRepo->findOneBy([
            'ressource' => $ressource,
            'utilisateur' => $user
        ]);
        
        if (!$evaluation) {
            $evaluation = new EvaluationRessource();
            $evaluation->setRessource($ressource);
            $evaluation->setUtilisateur($user);
            $em->persist($evaluation);
        }
        
        $evaluation->setCommentaire($commentaire);
        $evaluation->setDateCommentaire(new \DateTime());
        
        $em->flush();
        
        $this->addFlash('success', 'Votre commentaire a été ajouté');
        return $this->redirectToRoute('app_bibliotheque_show', ['id' => $ressource->getId()]);
    }
}
