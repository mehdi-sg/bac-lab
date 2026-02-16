<?php

namespace App\Controller;

use App\Form\ScoreType;
use App\Service\ScoreCalculatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/score')]
#[IsGranted('ROLE_USER')]
class ScoreController extends AbstractController
{
    public function __construct(
        private ScoreCalculatorService $scoreCalculator
    ) {}

    #[Route('/calcul', name: 'score_calcul', methods: ['GET', 'POST'])]
    public function calcul(Request $request): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            $this->addFlash('info', 'Veuillez vous connecter pour accéder au calculateur de score BAC.');
            return $this->redirectToRoute('app_login');
        }
        
        $user = $this->getUser();
        
        // Vérifier si l'utilisateur a un profil avec une filière
        $filiere = null;
        if ($user && $user->getProfil() && $user->getProfil()->getFiliere()) {
            $filiere = $user->getProfil()->getFiliere()->getNom();
        }
        
        // Si pas de filière, rediriger vers le profil
        if (!$filiere) {
            $this->addFlash('warning', 'Veuillez compléter votre profil et sélectionner votre filière pour calculer votre score BAC.');
            return $this->redirectToRoute('app_profile');
        }
        
        // Normaliser le nom de la filière
        $filiereNormalized = $this->scoreCalculator->normalizeFiliere($filiere);
        
        if (!$filiereNormalized) {
            $this->addFlash('error', 'Filière non reconnue : ' . $filiere . '. Veuillez contacter l\'administrateur.');
            return $this->redirectToRoute('app_profile');
        }
        
        // Créer le formulaire avec la filière
        $form = $this->createForm(ScoreType::class, null, [
            'filiere' => $filiereNormalized
        ]);
        
        $form->handleRequest($request);
        $result = null;
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            try {
                // Calculer le score FG
                $fg = $this->scoreCalculator->computeFG($filiereNormalized, $data);
                
                // Préparer les détails du calcul
                $details = $this->scoreCalculator->getCalculationDetails($filiereNormalized, $data, $fg);
                
                $result = [
                    'fg' => $fg,
                    'details' => $details,
                    'filiere' => $filiereNormalized,
                    'filiereLabel' => $this->scoreCalculator->getFiliereLabel($filiereNormalized)
                ];
                
                // Save scores in session for orientation recommendations
                $request->getSession()->set('user_scores', array_merge(['FG' => $fg], $data));
                
                $this->addFlash('success', 'Score calculé avec succès ! Votre Formule Globale (FG) est de ' . $fg . '/20.');
                
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors du calcul : ' . $e->getMessage());
            }
        }
        
        return $this->render('score/calcul.html.twig', [
            'form' => $form->createView(),
            'filiere' => $filiereNormalized,
            'filiereLabel' => $this->scoreCalculator->getFiliereLabel($filiereNormalized),
            'matieres' => $this->scoreCalculator->getMatieresByFiliere($filiereNormalized),
            'result' => $result,
            'user' => $user
        ]);
    }
}