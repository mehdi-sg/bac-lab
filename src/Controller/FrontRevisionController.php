<?php

namespace App\Controller;

use App\Entity\Filiere;
use App\Entity\Matiere;
use App\Entity\Chapitre;
use App\Repository\FiliereRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontRevisionController extends AbstractController
{
    #[Route('/revision/cours', name: 'app_revision_filieres')]
    public function listFilieres(Request $request, FiliereRepository $repo): Response
    {
        $query = trim((string) $request->query->get('q', ''));
        $filieres = $query === '' ? $repo->findAll() : $repo->findBy(['nom' => $query]);

        return $this->render('front/revision/filieres.html.twig', [
            'filieres' => $filieres,
            'query' => $query,
        ]);
    }

    #[Route('/revision/cours/filiere/{id}', name: 'app_revision_matieres')]
    public function listMatieres(Filiere $filiere, ManagerRegistry $doctrine): Response
    {
        $matieres = $doctrine->getRepository(Matiere::class)->findBy(['filiere' => $filiere]);

        return $this->render('front/revision/matieres.html.twig', [
            'filiere' => $filiere,
            'matieres' => $matieres,
        ]);
    }

    #[Route('/revision/cours/matiere/{id}', name: 'app_revision_chapitres')]
    public function listChapitres(Matiere $matiere, ManagerRegistry $doctrine): Response
    {
        $chapitres = $doctrine->getRepository(Chapitre::class)->findBy(['matiere' => $matiere]);

        return $this->render('front/revision/chapitres.html.twig', [
            'matiere' => $matiere,
            'chapitres' => $chapitres,
        ]);
    }
}