<?php

namespace App\Controller\Admin;

use App\Entity\Groupe;
use App\Entity\MembreGroupe;
use App\Entity\Utilisateur;
use App\Repository\GroupeRepository;
use App\Repository\MembreGroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/membres-groupe')]
#[IsGranted('ROLE_ADMIN')]
final class AdminMembreGroupeController extends AbstractController
{
    // List all pending membership requests across all groups
    #[Route('/demandes', name: 'admin_membre_groupe_demandes', methods: ['GET'])]
    public function pendingRequests(
        MembreGroupeRepository $membreGroupeRepository,
        GroupeRepository $groupeRepository
    ): Response {
        // Get all pending requests (statut = 'PENDING')
        $pendingRequests = $membreGroupeRepository->findBy([
            'statut' => 'PENDING',
        ], ['dateJoint' => 'DESC']);

        // Get all groups for filter dropdown
        $groupes = $groupeRepository->findAll();

        // Calculate statistics
        $today = new \DateTimeImmutable();
        $stats = [
            'total' => count($pendingRequests),
            'today' => count(array_filter($pendingRequests, function($m) use ($today) {
                return $m->getDateJoint()->format('Y-m-d') === $today->format('Y-m-d');
            })),
        ];

        return $this->render('admin/membre_groupe/demandes.html.twig', [
            'demandes' => $pendingRequests,
            'groupes' => $groupes,
            'stats' => $stats,
        ]);
    }

    // Accept a membership request
    #[Route('/accepter/{id}', name: 'admin_membre_groupe_accepter', methods: ['POST'])]
    public function acceptRequest(
        MembreGroupe $membre,
        EntityManagerInterface $entityManager
    ): Response {
        if ($membre->getStatut() !== 'PENDING') {
            $this->addFlash('error', 'Cette demande a déjà été traitée.');
            return $this->redirectToRoute('admin_membre_groupe_demandes');
        }

        $membre->setStatut('ACCEPTED');
        $entityManager->flush();

        $this->addFlash('success', 'La demande d\'adhésion a été acceptée avec succès.');
        return $this->redirectToRoute('admin_membre_groupe_demandes');
    }

    // Reject a membership request
    #[Route('/rejeter/{id}', name: 'admin_membre_groupe_rejeter', methods: ['POST'])]
    public function rejectRequest(
        MembreGroupe $membre,
        EntityManagerInterface $entityManager
    ): Response {
        if ($membre->getStatut() !== 'PENDING') {
            $this->addFlash('error', 'Cette demande a déjà été traitée.');
            return $this->redirectToRoute('admin_membre_groupe_demandes');
        }

        $membre->setStatut('REJECTED');
        $entityManager->flush();

        $this->addFlash('success', 'La demande d\'adhésion a été rejetée.');
        return $this->redirectToRoute('admin_membre_groupe_demandes');
    }

    // List all accepted members across all groups
    #[Route('/membres', name: 'admin_membre_groupe_membres', methods: ['GET'])]
    public function allMembers(
        MembreGroupeRepository $membreGroupeRepository,
        GroupeRepository $groupeRepository
    ): Response {
        // Get all accepted members
        $membres = $membreGroupeRepository->findBy([
            'statut' => 'ACCEPTED',
        ], ['dateJoint' => 'DESC']);

        // Get all groups for filter dropdown
        $groupes = $groupeRepository->findAll();

        // Calculate statistics
        $today = new \DateTimeImmutable();
        $stats = [
            'total' => count($membres),
            'today' => count(array_filter($membres, function($m) use ($today) {
                return $m->getDateJoint()->format('Y-m-d') === $today->format('Y-m-d');
            })),
        ];

        return $this->render('admin/membre_groupe/membres.html.twig', [
            'membres' => $membres,
            'groupes' => $groupes,
            'stats' => $stats,
        ]);
    }

    // Remove a member from a group
    #[Route('/supprimer/{id}', name: 'admin_membre_groupe_supprimer', methods: ['POST'])]
    public function removeMember(
        MembreGroupe $membre,
        EntityManagerInterface $entityManager
    ): Response {
        $groupeId = $membre->getGroupe()->getId();
        $entityManager->remove($membre);
        $entityManager->flush();

        $this->addFlash('success', 'Le membre a été supprimé du groupe avec succès.');
        
        // Redirect to groupe show page if coming from there, otherwise to members list
        return $this->redirectToRoute('admin_groupe_show', ['id' => $groupeId]);
    }

    // Promote a member to admin/animateur
    #[Route('/promouvoir/{id}/{role}', name: 'admin_membre_groupe_promouvoir', methods: ['POST'])]
    public function promoteMember(
        MembreGroupe $membre,
        string $role,
        EntityManagerInterface $entityManager
    ): Response {
        if (!in_array($role, ['ADMIN', 'ANIMATEUR', 'MEMBRE'])) {
            $this->addFlash('error', 'Rôle invalide.');
            return $this->redirectToRoute('admin_groupe_show', ['id' => $membre->getGroupe()->getId()]);
        }

        $membre->setRoleMembre($role);
        $entityManager->flush();

        $this->addFlash('success', 'Le rôle du membre a été mis à jour avec succès.');
        return $this->redirectToRoute('admin_groupe_show', ['id' => $membre->getGroupe()->getId()]);
    }

    // View all members of a specific group
    #[Route('/groupe/{id}/membres', name: 'admin_membre_groupe_groupe', methods: ['GET'])]
    public function groupMembers(
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

        return $this->render('admin/membre_groupe/groupe_membres.html.twig', [
            'groupe' => $groupe,
            'membres' => $membres,
            'demandes' => $pendingRequests,
        ]);
    }
}
