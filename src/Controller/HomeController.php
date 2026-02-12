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
    #[Route('/', name: 'app_index')]
    public function index(
        Request $request,
        RessourceRepository $ressourceRepository
    ): Response
    {
        // Get filter parameters
        $searchQuery = $request->query->get('q');
        $typeFichier = $request->query->get('typeFichier');
        $categorie = $request->query->get('categorie');

        // If filters are applied, get filtered results, otherwise get latest 6
        if ($searchQuery || $typeFichier || $categorie) {
            $ressources = $ressourceRepository->findPublicRessources($searchQuery, $typeFichier, $categorie, 12);
        } else {
            $ressources = $ressourceRepository->findPublicRessources(null, null, null, 6);
        }

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
