<?php

namespace App\Controller;

use App\Entity\Fiche;
use App\Entity\Utilisateur;
use App\Entity\Filiere;
use App\Entity\Chapitre;
use App\Entity\Ressource;
use App\Entity\Quiz;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(EntityManagerInterface $em): Response
    {
        // 🔹 Utilisateurs
        $totalUsers = $em->getRepository(Utilisateur::class)->count([]);
        $activeUsers = $em->getRepository(Utilisateur::class)->count([
            'isActive' => true
        ]);

        // 🔹 quiz
        // $totalquiz = $em->getRepository(Quiz::class)->count([]);

        // 🔹 Ressources
        // $totalRessources = $em->getRepository(Ressource::class)->count([]);

        // 🔹 fiches
        $totalfiches = $em->getRepository(Fiche::class)->count([]);

        return $this->render('admin/dashboard.html.twig', [
            'user' => $this->getUser(),

            // utilisateurs
            'activeUsers' => $activeUsers,
            'totalUsers' => $totalUsers,

            

            // fiches
            'totalFiches' => $totalfiches,

           
        ]);
    }
}
