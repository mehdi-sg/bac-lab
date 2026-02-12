<?php

namespace App\Controller\Admin;

use App\Entity\Groupe;
use App\Entity\MembreGroupe;
use App\Repository\GroupeRepository;
use App\Repository\MembreGroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/groupes')]
#[IsGranted('ROLE_ADMIN')]
final class AdminGroupeController extends AbstractController
{
    #[Route('', name: 'admin_groupes', methods: ['GET'])]
    public function index(GroupeRepository $groupeRepository): Response
    {
        // Admin sees ALL groups
        $groupes = $groupeRepository->findBy([], ['id' => 'DESC']);
        
        // Calculate statistics
        $stats = [
            'total' => count($groupes),
            'publics' => count(array_filter($groupes, fn($g) => $g->isPublic())),
            'prives' => count(array_filter($groupes, fn($g) => !$g->isPublic())),
        ];

        return $this->render('admin/groupe/index.html.twig', [
            'groupes' => $groupes,
            'stats' => $stats,
        ]);
    }

    #[Route('/{id}', name: 'admin_groupe_show', methods: ['GET'])]
    public function show(
        Groupe $groupe,
        MembreGroupeRepository $membreGroupeRepository
    ): Response {
        $membres = $membreGroupeRepository->findBy([
            'groupe' => $groupe,
            'statut' => 'ACCEPTED',
        ]);
        
        $pendingRequests = $membreGroupeRepository->findBy([
            'groupe' => $groupe,
            'statut' => 'PENDING',
        ]);

        return $this->render('admin/groupe/show.html.twig', [
            'groupe' => $groupe,
            'membres' => $membres,
            'pendingRequests' => $pendingRequests,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_groupe_delete', methods: ['POST'])]
    public function delete(
        Groupe $groupe,
        EntityManagerInterface $entityManager
    ): Response {
        $entityManager->remove($groupe);
        $entityManager->flush();

        $this->addFlash('success', 'Groupe supprimé avec succès.');
        return $this->redirectToRoute('admin_groupes');
    }

    #[Route('/{id}/supprimer-membre/{membreId}', name: 'admin_groupe_remove_member', methods: ['POST'])]
    public function removeMember(
        Groupe $groupe,
        int $membreId,
        EntityManagerInterface $entityManager,
        MembreGroupeRepository $membreGroupeRepository
    ): Response {
        $membre = $membreGroupeRepository->find($membreId);
        
        if (!$membre || $membre->getGroupe() !== $groupe) {
            return $this->json(['error' => 'Membre non trouvé'], 404);
        }

        $entityManager->remove($membre);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Membre supprimé du groupe',
        ]);
    }
}
