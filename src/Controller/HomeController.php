<?php

namespace App\Controller;

use App\Repository\RessourceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(
        Request $request,
        RessourceRepository $ressourceRepository
    ): Response
    {
        $q = $request->query->get('q');
        $filiere = $request->query->get('filiere');
        $matiere = $request->query->get('matiere');
        $typeFichier = $request->query->get('typeFichier');
        $categorie = $request->query->get('categorie');

        // Get filtered resources or latest 6 if no filters
        $ressources = $ressourceRepository->findPublicRessources(
            $q,
            $filiere,
            $matiere,
            $typeFichier,
            $categorie,
            6
        );

        return $this->render('Home/home.html.twig', [
            'ressources' => $ressources,
            'q' => $q,
            'filiere' => $filiere,
            'matiere' => $matiere,
            'typeFichier' => $typeFichier,
            'categorie' => $categorie,
        ]);
    }
}
