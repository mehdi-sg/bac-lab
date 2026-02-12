<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('Home/home.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/filieres', name: 'app_filieres')]
    public function filieres(\App\Repository\FiliereRepository $filiereRepository): Response
    {
        $filieres = $filiereRepository->findBy(['actif' => true], ['nom' => 'ASC']);
        
        return $this->render('Home/filieres.html.twig', [
            'filieres' => $filieres,
        ]);
    }
}
