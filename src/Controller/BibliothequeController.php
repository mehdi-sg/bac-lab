<?php

namespace App\Controller;

use App\Entity\Ressource;
use App\Repository\RessourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/bibliotheque')]
class BibliothequeController extends AbstractController
{
    #[Route('', name: 'app_bibliotheque_index', methods: ['GET'])]
    public function index(
        Request $request,
        RessourceRepository $ressourceRepository
    ): Response {
        // Get all filter parameters
        $q = $request->query->get('q');
        $typeFichier = $request->query->get('typeFichier');
        $categorie = $request->query->get('categorie');
        $filiere = $request->query->get('filiere');
        $matiere = $request->query->get('matiere');

        // Find resources with filters (correct parameter order)
        $ressources = $ressourceRepository->findPublicRessources($q, $filiere, $matiere, $typeFichier, $categorie);

        return $this->render('bibliotheque/index.html.twig', [
            'ressources' => $ressources,
            'q' => $q,
            'typeFichier' => $typeFichier,
            'categorie' => $categorie,
            'filiere' => $filiere,
            'matiere' => $matiere,
        ]);
    }

    #[Route('/{id}', name: 'app_bibliotheque_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Ressource $ressource, EntityManagerInterface $em): Response
    {
        // Sécurité: seulement les ressources publiques
        if (!$ressource->isEstActive() || $ressource->getStatut() !== 'VALIDEE') {
            throw $this->createNotFoundException('Ressource non disponible.');
        }

        // +1 vue
        $ressource->incrementVues();
        $em->flush();

        return $this->render('bibliotheque/show.html.twig', [
            'ressource' => $ressource,
        ]);
    }

    #[Route('/{id}/telecharger', name: 'app_bibliotheque_telecharger', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function telecharger(Ressource $ressource, EntityManagerInterface $em): Response
    {
        if (!$ressource->isEstActive() || $ressource->getStatut() !== 'VALIDEE') {
            throw $this->createNotFoundException('Ressource non disponible.');
        }

        $url = $ressource->getUrlFichier();
        if (!$url) {
            $this->addFlash('danger', "Aucun fichier/lien n'est disponible pour cette ressource.");
            return $this->redirectToRoute('app_bibliotheque_show', ['id' => $ressource->getId()]);
        }

    
        $ressource->incrementTelechargements();
        $em->flush();

        return $this->redirect($url);
    }
}
