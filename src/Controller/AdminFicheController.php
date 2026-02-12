<?php

namespace App\Controller;

use App\Entity\Fiche;
use App\Entity\FicheModerateur;
use App\Entity\Utilisateur;
use App\Repository\FicheRepository;
use App\Repository\FicheModerateurRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/fiche')]
#[IsGranted('ROLE_ADMIN')]
class AdminFicheController extends AbstractController
{
    #[Route('/', name: 'admin_fiche_index', methods: ['GET'])]
    public function index(FicheRepository $ficheRepository, Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 20;
        
        $search = $request->query->get('search', '');
        $isPublic = $request->query->get('isPublic', '');
        
        $queryBuilder = $ficheRepository->createQueryBuilder('f')
            ->orderBy('f.createdAt', 'DESC');
        
        if ($search) {
            $queryBuilder->andWhere('f.title LIKE :search OR f.content LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }
        
        if ($isPublic !== '') {
            $queryBuilder->andWhere('f.isPublic = :isPublic')
                ->setParameter('isPublic', (bool) $isPublic);
        }
        
        $totalFiches = count($queryBuilder->getQuery()->getResult());
        $totalPages = ceil($totalFiches / $limit);
        
        $fiches = $queryBuilder
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
        
        return $this->render('admin/fiche/index.html.twig', [
            'fiches' => $fiches,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_fiches' => $totalFiches,
            'search' => $search,
            'isPublic' => $isPublic,
        ]);
    }
    
    #[Route('/{id}', name: 'admin_fiche_show', methods: ['GET'])]
    public function show(Fiche $fiche, FicheModerateurRepository $moderateurRepository): Response
    {
        $moderateurs = $moderateurRepository->findBy(['fiche' => $fiche]);
        
        return $this->render('admin/fiche/show.html.twig', [
            'fiche' => $fiche,
            'moderateurs' => $moderateurs,
        ]);
    }
    
    #[Route('/{id}/moderateurs', name: 'admin_fiche_moderateurs', methods: ['GET', 'POST'])]
    public function moderateurs(
        Fiche $fiche,
        FicheModerateurRepository $moderateurRepository,
        UtilisateurRepository $utilisateurRepository,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $moderateurs = $moderateurRepository->findBy(['fiche' => $fiche]);
        
        // Get all users for the add moderator form
        $allUsers = $utilisateurRepository->findAll();
        
        // Filter out users who are already moderators
        $moderateurUserIds = array_map(fn($m) => $m->getUtilisateur()?->getId(), $moderateurs);
        $availableUsers = array_filter($allUsers, fn($u) => !in_array($u->getId(), $moderateurUserIds));
        
        return $this->render('admin/fiche/moderateurs.html.twig', [
            'fiche' => $fiche,
            'moderateurs' => $moderateurs,
            'availableUsers' => $availableUsers,
        ]);
    }
    
    #[Route('/{id}/moderateurs/add', name: 'admin_fiche_add_moderateur', methods: ['POST'])]
    public function addModerateur(
        Fiche $fiche,
        Request $request,
        UtilisateurRepository $utilisateurRepository,
        FicheModerateurRepository $moderateurRepository,
        EntityManagerInterface $em
    ): Response {
        $userId = $request->request->get('utilisateur_id');
        
        if (!$userId) {
            $this->addFlash('error', 'Veuillez sélectionner un utilisateur.');
            return $this->redirectToRoute('admin_fiche_moderateurs', ['id' => $fiche->getId()]);
        }
        
        $utilisateur = $utilisateurRepository->find($userId);
        
        if (!$utilisateur) {
            $this->addFlash('error', 'Utilisateur introuvable.');
            return $this->redirectToRoute('admin_fiche_moderateurs', ['id' => $fiche->getId()]);
        }
        
        // Check if already moderator
        $existing = $moderateurRepository->findByFicheAndUtilisateur($fiche->getId(), $utilisateur->getId());
        if ($existing) {
            $this->addFlash('warning', 'Cet utilisateur est déjà modérateur de cette fiche.');
            return $this->redirectToRoute('admin_fiche_moderateurs', ['id' => $fiche->getId()]);
        }
        
        $moderateur = new FicheModerateur();
        $moderateur->setFiche($fiche);
        $moderateur->setUtilisateur($utilisateur);
        $moderateur->setIsOwner(false);
        
        $em->persist($moderateur);
        $em->flush();
        
        $this->addFlash('success', 'Modérateur ajouté avec succès.');
        
        return $this->redirectToRoute('admin_fiche_moderateurs', ['id' => $fiche->getId()]);
    }
    
    #[Route('/{ficheId}/moderateurs/{moderateurId}/remove', name: 'admin_fiche_remove_moderateur', methods: ['POST'])]
    public function removeModerateur(
        int $ficheId,
        int $moderateurId,
        FicheModerateurRepository $moderateurRepository,
        EntityManagerInterface $em
    ): Response {
        $moderateur = $moderateurRepository->find($moderateurId);
        
        if (!$moderateur || $moderateur->getFiche()->getId() !== $ficheId) {
            $this->addFlash('error', 'Modérateur introuvable.');
            return $this->redirectToRoute('admin_fiche_moderateurs', ['id' => $ficheId]);
        }
        
        if ($moderateur->isOwner()) {
            $this->addFlash('error', 'Impossible de retirer le propriétaire de la fiche.');
            return $this->redirectToRoute('admin_fiche_moderateurs', ['id' => $ficheId]);
        }
        
        $em->remove($moderateur);
        $em->flush();
        
        $this->addFlash('success', 'Modérateur retiré avec succès.');
        
        return $this->redirectToRoute('admin_fiche_moderateurs', ['id' => $ficheId]);
    }
    
    #[Route('/{ficheId}/moderateurs/{moderateurId}/toggle-owner', name: 'admin_fiche_toggle_owner', methods: ['POST'])]
    public function toggleOwner(
        int $ficheId,
        int $moderateurId,
        FicheModerateurRepository $moderateurRepository,
        EntityManagerInterface $em
    ): Response {
        $moderateur = $moderateurRepository->find($moderateurId);
        
        if (!$moderateur || $moderateur->getFiche()->getId() !== $ficheId) {
            $this->addFlash('error', 'Modérateur introuvable.');
            return $this->redirectToRoute('admin_fiche_moderateurs', ['id' => $ficheId]);
        }
        
        $moderateur->setIsOwner(!$moderateur->isOwner());
        $em->flush();
        
        $this->addFlash('success', 'Statut de propriétaire modifié avec succès.');
        
        return $this->redirectToRoute('admin_fiche_moderateurs', ['id' => $ficheId]);
    }
    
    #[Route('/{id}/toggle-public', name: 'admin_fiche_toggle_public', methods: ['POST'])]
    public function togglePublic(Fiche $fiche, EntityManagerInterface $em): Response
    {
        $fiche->setIsPublic(!$fiche->isPublic());
        $em->flush();
        
        $this->addFlash('success', 'Statut de visibilité modifié avec succès.');
        
        return $this->redirectToRoute('admin_fiche_index');
    }
    
    #[Route('/{id}/delete', name: 'admin_fiche_delete', methods: ['POST'])]
    public function delete(Request $request, Fiche $fiche, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_fiche_' . $fiche->getId(), $request->request->get('_token'))) {
            $em->remove($fiche);
            $em->flush();
            
            $this->addFlash('success', 'Fiche supprimée avec succès.');
        }
        
        return $this->redirectToRoute('admin_fiche_index');
    }
}
