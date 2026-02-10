<?php

namespace App\Controller;

use App\Entity\Filiere;
use App\Entity\Matiere;
use App\Entity\Chapitre;
use App\Repository\FiliereRepository;
use Doctrine\Persistence\ManagerRegistry; // Utilisation du ManagerRegistry pour plus de sécurité
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontRevisionController extends AbstractController
{
    // Étape 1 : Liste des Filières
    #[Route('/revision/cours', name: 'app_revision_filieres')]
    public function listFilieres(Request $request, FiliereRepository $repo): Response
    {
        $query = trim((string) $request->query->get('q', ''));
        $filieres = $query === ''
            ? $repo->findActives()
            : $repo->searchActives($query);

        return $this->render('front/revision/filieres.html.twig', [
            'filieres' => $filieres,
            'query' => $query,
        ]);
    }

    // Étape 2 : Matières d'une filière
    #[Route('/revision/cours/filiere/{id}', name: 'app_revision_matieres')]
    public function listMatieres(Filiere $filiere, ManagerRegistry $doctrine): Response
    {
        // On récupère le repository de Matiere via le manager de doctrine
        $matieres = $doctrine->getRepository(Matiere::class)->findBy(['filiere' => $filiere]);

        return $this->render('front/revision/matieres.html.twig', [
            'filiere' => $filiere,
            'matieres' => $matieres,
        ]);
    }

    // Étape 3 : PDF d'une matière
    #[Route('/revision/cours/matiere/{id}', name: 'app_revision_pdf')]
    public function listPdf(Matiere $matiere, ManagerRegistry $doctrine): Response
    {
        // On récupère le repository de Chapitre via le manager de doctrine
        $chapitres = $doctrine->getRepository(Chapitre::class)->findBy(['matiere' => $matiere]);

        return $this->render('front/revision/chapitres.html.twig', [
            'matiere' => $matiere,
            'chapitres' => $chapitres,
        ]);
    }
}
