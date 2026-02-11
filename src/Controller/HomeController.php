<?php

namespace App\Controller;

use App\Repository\RessourceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(
        RessourceRepository $ressourceRepository
    ): Response
    {
        // Get latest 6 resources for home page
        $ressources = $ressourceRepository->findPublicRessources(null, null, 6);

        return $this->render('Home/home.html.twig', [
            'ressources' => $ressources,
        ]);
    }
}
