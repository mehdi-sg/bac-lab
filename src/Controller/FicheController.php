<?php

namespace App\Controller;

use App\Entity\Fiche;
use App\Entity\FicheVersion;
use App\Entity\FicheModerateur;
use App\Entity\FicheJoinRequest;
use App\Entity\FicheFavori;
use App\Entity\Notification;
use App\Entity\Utilisateur;
use App\Form\FicheType;
use App\Repository\FicheRepository;
use App\Repository\FicheVersionRepository;
use App\Repository\FicheModerateurRepository;
use App\Repository\FicheJoinRequestRepository;
use App\Repository\FicheFavoriRepository;
use App\Repository\FiliereRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/fiche')]
class FicheController extends AbstractController
{
    // =============================
    // INDEX — Dashboard with filters
    // =============================
    #[Route('/', name: 'fiche_index', methods: ['GET'])]
    public function index(
        Request $request,
        FicheRepository $ficheRepository,
        FicheModerateurRepository $ficheModerateurRepository,
        FicheFavoriRepository $ficheFavoriRepository,
        FiliereRepository $filiereRepository
    ): Response {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();
        
        // Get filter parameters
        $filterType = $request->query->get('filter', 'all');
        $filiereId = $request->query->get('filiere');
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = 12;
        
        // Build filters array
        $filters = [
            'type' => $filterType,
            'filiere' => $filiereId ? (int) $filiereId : null,
        ];
        
        // Get user ID if logged in
        $userId = $user instanceof Utilisateur ? $user->getId() : null;
        
        // If user is not connected, only show public fiches
        if (!$userId) {
            $filters['public_only'] = true;
        }
        
        // Get filtered fiches with pagination
        [$fiches, $totalCount] = $ficheRepository->findByFiltersWithPagination($filters, $userId, $page, $limit);
        
        // Calculate pagination data
        $totalPages = (int) ceil($totalCount / $limit);
        
        // Get user's moderator fiches for UI indicators
        $userModerateurFiches = [];
        $userFavoriteFiches = [];
        
        if ($user instanceof Utilisateur) {
            $moderateurRecords = $ficheModerateurRepository->findByUtilisateur($user->getId());
            $userModerateurFiches = array_map(function ($record) {
                return $record->getFiche()->getId();
            }, $moderateurRecords);
            
            // Get user's favorite fiches
            $favoriteRecords = $ficheFavoriRepository->findByUtilisateur($user->getId());
            $userFavoriteFiches = array_map(function ($record) {
                return $record->getFiche()->getId();
            }, $favoriteRecords);
        }
        
        // Get all filieres for the filter dropdown
        $filieres = $filiereRepository->findActives();
        
        return $this->render('fiche/index.html.twig', [
            'fiches' => $fiches,
            'user_moderateur_fiches' => $userModerateurFiches,
            'user_favorite_fiches' => $userFavoriteFiches,
            'filieres' => $filieres,
            'current_filter' => $filterType,
            'current_filiere' => $filiereId ? (int) $filiereId : null,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_count' => $totalCount,
        ]);

    }


    // =============================
    // NEW — Create new fiche
    // =============================
    #[Route('/new', name: 'fiche_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $fiche = new Fiche();
        $fiche->setCreatedAt(new \DateTimeImmutable());
        $fiche->setUpdatedAt(new \DateTime());

        $form = $this->createForm(FicheType::class, $fiche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Add creator as moderateur (owner)
            /** @var Utilisateur|null $user */
            $user = $this->getUser();
            if ($user instanceof Utilisateur) {
                $moderateur = new FicheModerateur();
                $moderateur->setFiche($fiche);
                $moderateur->setUtilisateur($user);
                $moderateur->setIsOwner(true);
                $em->persist($moderateur);
            }

            $em->persist($fiche);
            $em->flush();

            return $this->redirectToRoute('fiche_index');
        }

        return $this->render('fiche/new.html.twig', [
            'form' => $form,
        ]);
    }

    // =============================
    // SHOW — Reading mode
    // =============================
    #[Route('/{id}', name: 'fiche_show', methods: ['GET'])]
    public function show(
        Fiche $fiche,
        FicheModerateurRepository $ficheModerateurRepository,
        FicheFavoriRepository $ficheFavoriRepository,
        FicheJoinRequestRepository $ficheJoinRequestRepository,
        EntityManagerInterface $em
    ): Response {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();
        
        // If user is not connected and fiche is not public, deny access
        if (!$user && !$fiche->isPublic()) {
            $this->addFlash('warning', 'Cette fiche est privée. Veuillez vous connecter.');
            return $this->redirectToRoute('app_login');
        }
        
        $isModerateur = false;
        $isOwner = false;
        $isFavorited = false;
        
        if ($user instanceof Utilisateur) {
            $moderateur = $ficheModerateurRepository->findByFicheAndUtilisateur($fiche->getId(), $user->getId());
            if ($moderateur) {
                $isModerateur = true;
                $isOwner = $moderateur->isOwner();
            }
            $isFavorited = $ficheFavoriRepository->isFavorited($fiche->getId(), $user->getId());
        }
        
        // Eagerly load moderators with their utilisateurs and profiles
        $moderateurs = $ficheModerateurRepository->createQueryBuilder('m')
            ->leftJoin('m.utilisateur', 'u')
            ->leftJoin('u.profil', 'p')
            ->addSelect('u', 'p')
            ->andWhere('m.fiche = :fiche')
            ->setParameter('fiche', $fiche)
            ->getQuery()
            ->getResult();
        
        // Filter out moderators with deleted users
        $validModerateurs = array_filter($moderateurs, function ($m) {
            return $m->getUtilisateur() !== null;
        });
        
        // Eagerly load join requests with their utilisateurs and profiles
        $joinRequests = $ficheJoinRequestRepository->createQueryBuilder('jr')
            ->leftJoin('jr.utilisateur', 'u')
            ->leftJoin('u.profil', 'p')
            ->addSelect('u', 'p')
            ->andWhere('jr.fiche = :fiche')
            ->andWhere('jr.status = :status')
            ->setParameter('fiche', $fiche)
            ->setParameter('status', FicheJoinRequest::STATUS_PENDING)
            ->orderBy('jr.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
        
        // Filter out requests with deleted users
        $validJoinRequests = array_filter($joinRequests, function ($r) {
            return $r->getUtilisateur() !== null;
        });
        
        return $this->render('fiche/show.html.twig', [
            'fiche' => $fiche,
            'is_moderateur' => $isModerateur,
            'is_owner' => $isOwner,
            'is_favorited' => $isFavorited,
            'moderateurs_list' => $validModerateurs,
            'join_requests_list' => $validJoinRequests,
        ]);
    }

    // =============================
    // EDIT — Co-edition + versioning (moderateurs only for private, all users for public)
    // =============================
    #[Route('/{id}/edit', name: 'fiche_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Fiche $fiche,
        FicheModerateurRepository $ficheModerateurRepository,
        EntityManagerInterface $em
    ): Response {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();
        
        // Check if user is logged in
        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('app_login');
        }
        
        // For public fiches, all logged-in users can edit
        // For private fiches, only moderators can edit
        if (!$fiche->isPublic()) {
            $moderateur = $ficheModerateurRepository->findByFicheAndUtilisateur($fiche->getId(), $user->getId());
            if (!$moderateur) {
                $this->addFlash('danger', 'Vous n\'êtes pas autorisé à modifier cette fiche privée.');
                return $this->redirectToRoute('fiche_show', ['id' => $fiche->getId()]);
            }
        }
        
        // Save old content BEFORE edit
        $oldContent = $fiche->getContent();

        $form = $this->createForm(FicheType::class, $fiche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Create fiche version (history)
            $version = new FicheVersion();
            $version->setContent($oldContent);
            $version->setEditedAt(new \DateTimeImmutable());
            
            // Get editor name
            $editorName = 'Utilisateur';
            if ($user->getProfil() !== null) {
                $editorName = $user->getProfil()->getNom() . ' ' . $user->getProfil()->getPrenom();
            } else {
                $editorName = $user->getEmail();
            }
            $version->setEditorName($editorName);
            $version->setFiche($fiche);

            $em->persist($version);
            $em->flush();

            $this->addFlash('success', 'La fiche a été modifiée avec succès.');

            return $this->redirectToRoute('fiche_show', [
                'id' => $fiche->getId()
            ]);
        }

        return $this->render('fiche/edit.html.twig', [
            'form' => $form,
            'fiche' => $fiche,
        ]);
    }

    // =============================
    // HISTORY — Version timeline (moderateurs only)
    // =============================
    #[Route('/{id}/history', name: 'fiche_history', methods: ['GET'])]
    public function history(
        Fiche $fiche,
        FicheVersionRepository $ficheVersionRepository,
        FicheModerateurRepository $ficheModerateurRepository
    ): Response {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();
        
        // Check if user is moderateur of this fiche
        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('app_login');
        }
        
        $moderateur = $ficheModerateurRepository->findByFicheAndUtilisateur($fiche->getId(), $user->getId());
        if (!$moderateur) {
            $this->addFlash('danger', 'Vous n\'êtes pas autorisé à voir l\'historique de cette fiche.');
            return $this->redirectToRoute('fiche_show', ['id' => $fiche->getId()]);
        }
        
        return $this->render('fiche/history.html.twig', [
            'fiche' => $fiche,
            'versions' => $ficheVersionRepository->findBy(
                ['fiche' => $fiche],
                ['editedAt' => 'DESC']
            ),
        ]);
    }

    // =============================
    // DELETE — Delete a fiche (owner only)
    // =============================
    #[Route('/{id}/delete', name: 'fiche_delete', methods: ['POST'])]
    public function delete(Request $request, Fiche $fiche, FicheModerateurRepository $ficheModerateurRepository, EntityManagerInterface $em): Response
    {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();
        
        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('app_login');
        }
        
        // Check if user is owner of this fiche
        $moderateur = $ficheModerateurRepository->findByFicheAndUtilisateur($fiche->getId(), $user->getId());
        if (!$moderateur || !$moderateur->isOwner()) {
            $this->addFlash('danger', 'Vous devez être le propriétaire pour supprimer cette fiche.');
            return $this->redirectToRoute('fiche_show', ['id' => $fiche->getId()]);
        }
        
        if ($this->isCsrfTokenValid('delete_fiche_'.$fiche->getId(), $request->request->get('_token'))) {
            $em->remove($fiche);
            $em->flush();
            $this->addFlash('success', 'La fiche a été supprimée avec succès.');
        }
        return $this->redirectToRoute('fiche_index');
    }

    // =============================
    // RESTORE — Restore a fiche version (moderateurs only)
    // =============================
    #[Route('/{id}/restore/{versionId}', name: 'fiche_restore_version', methods: ['POST'])]
    public function restoreVersion(
        Request $request,
        Fiche $fiche,
        int $versionId,
        FicheVersionRepository $ficheVersionRepository,
        FicheModerateurRepository $ficheModerateurRepository,
        EntityManagerInterface $em
    ): Response {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();
        
        // Check if user is moderateur of this fiche
        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('app_login');
        }
        
        $moderateur = $ficheModerateurRepository->findByFicheAndUtilisateur($fiche->getId(), $user->getId());
        if (!$moderateur) {
            $this->addFlash('danger', 'Vous n\'êtes pas autorisé à restaurer des versions de cette fiche.');
            return $this->redirectToRoute('fiche_show', ['id' => $fiche->getId()]);
        }
        
        // CSRF check
        if (!$this->isCsrfTokenValid('restore_version_' . $versionId, $request->request->get('_token'))) {
            $this->addFlash('danger', 'Token CSRF invalide.');
            return $this->redirectToRoute('fiche_history', ['id' => $fiche->getId()]);
        }

        // Find version AND ensure it belongs to this fiche
        $version = $ficheVersionRepository->findOneBy([
            'id' => $versionId,
            'fiche' => $fiche
        ]);

        if (!$version) {
            throw $this->createNotFoundException('Version introuvable pour cette fiche.');
        }

        $currentContent = $fiche->getContent();
        $targetContent  = $version->getContent();

        // If same content, no need to restore
        if ($currentContent === $targetContent) {
            $this->addFlash('info', 'Cette version est déjà la version actuelle.');
            return $this->redirectToRoute('fiche_history', ['id' => $fiche->getId()]);
        }

        // ✅ Save current content as a new version BEFORE restoring (so restore is tracked)
        $backup = new FicheVersion();
        $backup->setFiche($fiche);
        $backup->setContent($currentContent);
        $backup->setEditedAt(new \DateTimeImmutable());

        // Editor name from logged user if exists
        /** @var Utilisateur|null $user */
        $user = $this->getUser();
        $editorName = 'Utilisateur';
        if ($user instanceof Utilisateur && $user->getProfil() !== null) {
            $editorName = $user->getProfil()->getNom();
        }
        $backup->setEditorName($editorName . ' (restore)');

        $em->persist($backup);

        // Restore fiche content
        $fiche->setContent($targetContent);
        $fiche->setUpdatedAt(new \DateTime());

        $em->flush();

        $this->addFlash('success', 'Version restaurée avec succès ✅');

        return $this->redirectToRoute('fiche_show', ['id' => $fiche->getId()]);
    }

    // =============================
    // JOIN REQUEST — Request to join as moderateur
    // =============================
    #[Route('/{id}/join-request', name: 'fiche_join_request', methods: ['POST'])]
    public function joinRequest(
        Request $request,
        Fiche $fiche,
        FicheJoinRequestRepository $ficheJoinRequestRepository,
        FicheModerateurRepository $ficheModerateurRepository,
        EntityManagerInterface $em
    ): Response {
        // CSRF check
        if (!$this->isCsrfTokenValid('fiche_join_request_' . $fiche->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Token CSRF invalide.');
            return $this->redirectToRoute('fiche_show', ['id' => $fiche->getId()]);
        }

        /** @var Utilisateur|null $user */
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            $this->addFlash('warning', 'Vous devez être connecté pour effectuer cette action.');
            return $this->redirectToRoute('app_login');
        }

        // Check if user is already a moderateur
        if ($fiche->isModerateur($user)) {
            $this->addFlash('info', 'Vous êtes déjà modérateur de cette fiche.');
            return $this->redirectToRoute('fiche_show', ['id' => $fiche->getId()]);
        }

        // Check if user already has a pending request
        if ($ficheJoinRequestRepository->hasPendingRequest($fiche->getId(), $user->getId())) {
            $this->addFlash('info', 'Vous avez déjà une demande en attente pour cette fiche.');
            return $this->redirectToRoute('fiche_show', ['id' => $fiche->getId()]);
        }

        // Create join request
        $joinRequest = new FicheJoinRequest();
        $joinRequest->setFiche($fiche);
        $joinRequest->setUtilisateur($user);
        $joinRequest->setMessage($request->request->get('message', ''));

        $em->persist($joinRequest);
        $em->flush();

        // Create notification for owners
        $ficheTitle = $fiche->getTitle();
        $requesterName = $user->getProfil() ? $user->getProfil()->getNom() : $user->getEmail();
        
        $owners = $ficheModerateurRepository->findOwnersByFiche($fiche->getId());
        foreach ($owners as $owner) {
            $notification = new Notification();
            $notification->setUtilisateur($owner->getUtilisateur());
            $notification->setType('fiche_join_request');
            $notification->setTitle('Nouvelle demande de modération');
            $notification->setMessage($requesterName . ' demande à rejoindre la fiche "' . $ficheTitle . '" en tant que modérateur.');
            $notification->setLink('/fiche/' . $fiche->getId());
            $notification->setFicheJoinRequest($joinRequest);
            $em->persist($notification);
        }

        $em->flush();

        $this->addFlash('success', 'Votre demande pour rejoindre cette fiche a été envoyée au propriétaire.');

        return $this->redirectToRoute('fiche_show', ['id' => $fiche->getId()]);
    }

    // =============================
    // APPROVE REQUEST — Approve a join request
    // =============================
    #[Route('/{ficheId}/approve-request/{requestId}', name: 'fiche_approve_request', methods: ['POST'])]
    public function approveRequest(
        Request $request,
        int $ficheId,
        int $requestId,
        FicheJoinRequestRepository $ficheJoinRequestRepository,
        FicheModerateurRepository $ficheModerateurRepository,
        EntityManagerInterface $em
    ): Response {
        // CSRF check
        if (!$this->isCsrfTokenValid('approve_request_' . $requestId, $request->request->get('_token'))) {
            $this->addFlash('danger', 'Token CSRF invalide.');
            return $this->redirectToRoute('fiche_show', ['id' => $ficheId]);
        }

        /** @var Utilisateur|null $user */
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('app_login');
        }

        // Find the join request
        $joinRequest = $ficheJoinRequestRepository->find($requestId);
        if (!$joinRequest || $joinRequest->getFiche()->getId() !== $ficheId) {
            throw $this->createNotFoundException('Demande introuvable.');
        }

        // Check if user is owner of the fiche
        $moderateur = $ficheModerateurRepository->findByFicheAndUtilisateur($ficheId, $user->getId());
        if (!$moderateur || !$moderateur->isOwner()) {
            $this->addFlash('danger', 'Vous n\'êtes pas autorisé à approuver cette demande.');
            return $this->redirectToRoute('fiche_show', ['id' => $ficheId]);
        }

        // Approve the request
        $joinRequest->setStatus(FicheJoinRequest::STATUS_APPROVED);
        $joinRequest->setProcessedBy($user);
        $joinRequest->setProcessedAt(new \DateTimeImmutable());

        // Add user as moderateur
        $newModerateur = new FicheModerateur();
        $newModerateur->setFiche($joinRequest->getFiche());
        $newModerateur->setUtilisateur($joinRequest->getUtilisateur());
        $newModerateur->setIsOwner(false);
        $em->persist($newModerateur);

        // Create notification for the requester
        $ficheTitle = $joinRequest->getFiche()->getTitle();
        $notification = new Notification();
        $notification->setUtilisateur($joinRequest->getUtilisateur());
        $notification->setType('fiche_join_approved');
        $notification->setTitle('Demande acceptée');
        $notification->setMessage('Votre demande pour rejoindre la fiche "' . $ficheTitle . '" en tant que modérateur a été acceptée.');
        $notification->setLink('/fiche/' . $ficheId);
        $notification->setFicheJoinRequest($joinRequest);
        $em->persist($notification);

        $em->flush();

        $this->addFlash('success', 'La demande a été approuvée. L\'utilisateur est maintenant modérateur de cette fiche.');

        return $this->redirectToRoute('fiche_show', ['id' => $ficheId]);
    }

    // =============================
    // REJECT REQUEST — Reject a join request
    // =============================
    #[Route('/{ficheId}/reject-request/{requestId}', name: 'fiche_reject_request', methods: ['POST'])]
    public function rejectRequest(
        Request $request,
        int $ficheId,
        int $requestId,
        FicheJoinRequestRepository $ficheJoinRequestRepository,
        FicheModerateurRepository $ficheModerateurRepository,
        EntityManagerInterface $em
    ): Response {
        // CSRF check
        if (!$this->isCsrfTokenValid('reject_request_' . $requestId, $request->request->get('_token'))) {
            $this->addFlash('danger', 'Token CSRF invalide.');
            return $this->redirectToRoute('fiche_show', ['id' => $ficheId]);
        }

        /** @var Utilisateur|null $user */
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('app_login');
        }

        // Find the join request
        $joinRequest = $ficheJoinRequestRepository->find($requestId);
        if (!$joinRequest || $joinRequest->getFiche()->getId() !== $ficheId) {
            throw $this->createNotFoundException('Demande introuvable.');
        }

        // Check if user is owner of the fiche
        $moderateur = $ficheModerateurRepository->findByFicheAndUtilisateur($ficheId, $user->getId());
        if (!$moderateur || !$moderateur->isOwner()) {
            $this->addFlash('danger', 'Vous n\'êtes pas autorisé à rejeter cette demande.');
            return $this->redirectToRoute('fiche_show', ['id' => $ficheId]);
        }

        // Reject the request
        $joinRequest->setStatus(FicheJoinRequest::STATUS_REJECTED);
        $joinRequest->setProcessedBy($user);
        $joinRequest->setProcessedAt(new \DateTimeImmutable());

        // Create notification for the requester
        $ficheTitle = $joinRequest->getFiche()->getTitle();
        $notification = new Notification();
        $notification->setUtilisateur($joinRequest->getUtilisateur());
        $notification->setType('fiche_join_rejected');
        $notification->setTitle('Demande refusée');
        $notification->setMessage('Votre demande pour rejoindre la fiche "' . $ficheTitle . '" en tant que modérateur a été refusée.');
        $notification->setLink('/fiche/' . $ficheId);
        $notification->setFicheJoinRequest($joinRequest);
        $em->persist($notification);

        $em->flush();

        $this->addFlash('success', 'La demande a été rejetée.');

        return $this->redirectToRoute('fiche_show', ['id' => $ficheId]);
    }

    // =============================
    // TOGGLE FAVORITE — Add/remove fiche from favorites
    // =============================
    #[Route('/{id}/toggle-favorite', name: 'fiche_toggle_favorite', methods: ['POST'])]
    public function toggleFavorite(
        Request $request,
        Fiche $fiche,
        FicheFavoriRepository $ficheFavoriRepository,
        EntityManagerInterface $em
    ): Response {
        // CSRF check
        if (!$this->isCsrfTokenValid('toggle_favorite_' . $fiche->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Token CSRF invalide.');
            return $this->redirectToRoute('fiche_index');
        }

        /** @var Utilisateur|null $user */
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            $this->addFlash('warning', 'Vous devez être connecté pour effectuer cette action.');
            return $this->redirectToRoute('app_login');
        }

        try {
            // Check if already favorited
            $existingFavori = $ficheFavoriRepository->findOneByFicheAndUtilisateur($fiche->getId(), $user->getId());

            if ($existingFavori) {
                // Remove from favorites
                $em->remove($existingFavori);
                $em->flush();
                $this->addFlash('success', 'La fiche a été retirée de vos favoris.');
            } else {
                // Add to favorites
                $favori = new FicheFavori();
                $favori->setFiche($fiche);
                $favori->setUtilisateur($user);
                $em->persist($favori);
                $em->flush();
                $this->addFlash('success', 'La fiche a été ajoutée à vos favoris.');
            }
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Une erreur est survenue. Veuillez réessayer.');
            // Log the error for debugging
            error_log('Toggle favorite error: ' . $e->getMessage());
        }

        // Redirect back to the referring page or index page
        $referer = $request->headers->get('referer');
        if ($referer && str_contains($referer, $request->getSchemeAndHttpHost())) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('fiche_index');
    }

    // =============================
    // MY FICHES — List fiches where user is moderateur
    // =============================
    #[Route('/my-fiches', name: 'fiche_my_fiches', methods: ['GET'])]
    public function myFiches(FicheModerateurRepository $ficheModerateurRepository): Response
    {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('app_login');
        }

        $moderateurRecords = $ficheModerateurRepository->findByUtilisateur($user->getId());
        $fiches = array_map(function ($record) {
            return $record->getFiche();
        }, $moderateurRecords);

        return $this->render('fiche/my_fiches.html.twig', [
            'fiches' => $fiches,
        ]);
    }

}
