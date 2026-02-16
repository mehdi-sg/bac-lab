<?php
namespace App\Controller;

use App\Entity\Fiche;
use App\Entity\Groupe;
use App\Entity\Message;
use App\Entity\MembreGroupe;
use App\Entity\Notification;
use App\Entity\Utilisateur;
use App\Form\GroupeType;
use App\Repository\FicheRepository;
use App\Repository\GroupeRepository;
use App\Repository\MessageRepository;
use App\Repository\MembreGroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class GroupeController extends AbstractController
{
    #[Route('/groupes', name: 'groupe_index')]
    public function index(GroupeRepository $groupeRepository, MembreGroupeRepository $membreGroupeRepository, Request $request): Response
    {
        $user = $this->getUser();
        
        if (!$user instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }
        
        $filter = $request->query->get('filter', 'all');
        
        // Check if user is admin
        $isAdmin = false;
        if ($user instanceof Utilisateur && $user->isAdmin()) {
            $isAdmin = true;
        }
        
        // Build query to filter groups
        $queryBuilder = $groupeRepository->createQueryBuilder('g');
        
        // Filter by user's filiere for non-admin users
        if ($user instanceof Utilisateur && !$isAdmin) {
            $userFiliere = $user->getProfil()?->getFiliere();
            if ($userFiliere) {
                $queryBuilder->andWhere('g.filiere = :filiere')
                            ->setParameter('filiere', $userFiliere);
            } else {
                // If user has no filiere, show empty results
                $queryBuilder->andWhere('1 = 0');
            }
        }
        
        // Filter by visibility
        if ($filter === 'public') {
            $queryBuilder->andWhere('g.isPublic = true');
        } elseif ($filter === 'private') {
            $queryBuilder->andWhere('g.isPublic = false');
        }
        
        // For non-logged in users, only show public groups
        // Admins can see all groups (already filtered by filiere above for non-admins)
        if (!$user instanceof UserInterface) {
            $queryBuilder->andWhere('g.isPublic = true');
        }
        
        $queryBuilder->orderBy('g.id', 'DESC');
        $groupes = $queryBuilder->getQuery()->getResult();
        
        // Get user's memberships for each group
        $userMemberships = [];
        $userGroups = [];
        if ($user instanceof UserInterface) {
            $membres = $membreGroupeRepository->findBy(['utilisateur' => $user]);
            foreach ($membres as $membre) {
                $userMemberships[$membre->getGroupe()->getId()] = $membre;
                $userGroups[] = $membre->getGroupe()->getId();
            }
        }
        
        // Get featured/hottest groups (most members)
        $featuredQueryBuilder = $groupeRepository->createQueryBuilder('g')
            ->leftJoin('g.membres', 'm')
            ->groupBy('g.id')
            ->orderBy('COUNT(m.id)', 'DESC')
            ->setMaxResults(3);
        
        // Filter featured groups by user's filiere for non-admin users
        if ($user instanceof Utilisateur && !$isAdmin) {
            $userFiliere = $user->getProfil()?->getFiliere();
            if ($userFiliere) {
                $featuredQueryBuilder->andWhere('g.filiere = :filiere')
                                    ->setParameter('filiere', $userFiliere);
            } else {
                $featuredQueryBuilder->andWhere('1 = 0');
            }
        }
        
        $featuredGroups = $featuredQueryBuilder->getQuery()->getResult();
        
        // Get filieres for filtering
        $filieres = ['Mathématiques', 'Sciences', 'Physique', 'Chimie', 'Technologie'];
        
        return $this->render('groupe/index.html.twig', [
            'groupes' => $groupes,
            'userMemberships' => $userMemberships,
            'currentFilter' => $filter,
            'featuredGroups' => $featuredGroups,
            'filieres' => $filieres,
            'isAdmin' => $isAdmin,
        ]);
    }

    #[Route('/groupes/nouveau', name: 'groupe_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
        if (!$user instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        $groupe = new Groupe();
        $groupe->setCreateur($user);

        $form = $this->createForm(GroupeType::class, $groupe);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                // Add flash message with form errors
                $errors = [];
                foreach ($form->getErrors(true, true) as $error) {
                    $errors[] = $error->getMessage();
                }
                if (!empty($errors)) {
                    $this->addFlash('error', implode(' ', $errors));
                }
            } else {
                $entityManager->persist($groupe);
                $entityManager->flush();

                // Add creator as admin member
                $membre = new MembreGroupe();
                $membre->setUtilisateur($user);
                $membre->setGroupe($groupe);
                $membre->setRoleMembre('ADMIN');
                $membre->setStatut('ACCEPTED');
                $membre->setDateJoint(new \DateTimeImmutable());
                
                $entityManager->persist($membre);
                $entityManager->flush();

                $this->addFlash('success', 'Votre groupe a été créé avec succès !');
                
                return $this->redirectToRoute('chat_groupe', ['id' => $groupe->getId()]);
            }
        }

        return $this->render('groupe/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/groupes/{id}', name: 'groupe_show')]
    public function show(Groupe $groupe, MembreGroupeRepository $membreGroupeRepository): Response
    {
        // Only show accepted members
        $membres = $membreGroupeRepository->findBy([
            'groupe' => $groupe,
            'statut' => 'ACCEPTED'
        ]);
        
        return $this->render('groupe/show.html.twig', [
            'groupe' => $groupe,
            'membres' => $membres,
        ]);
    }

    #[Route('/groupes/{id}/modifier', name: 'groupe_edit', methods: ['GET', 'POST'])]
    public function edit(
        Groupe $groupe,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        
        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('app_login');
        }

        // Only owner can edit
        if ($groupe->getCreateur() !== $user) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier ce groupe');
            return $this->redirectToRoute('groupe_show', ['id' => $groupe->getId()]);
        }

        $form = $this->createForm(GroupeType::class, $groupe, [
            'is_edit' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $errors = [];
                foreach ($form->getErrors(true, true) as $error) {
                    $errors[] = $error->getMessage();
                }
                if (!empty($errors)) {
                    $this->addFlash('error', implode(' ', $errors));
                }
            } else {
                $entityManager->flush();
                $this->addFlash('success', 'Le groupe a été modifié avec succès !');
                return $this->redirectToRoute('groupe_show', ['id' => $groupe->getId()]);
            }
        }

        return $this->render('groupe/edit.html.twig', [
            'groupe' => $groupe,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/groupes/{id}', name: 'groupe_update', methods: ['POST', 'PUT'])]
    public function update(
        Request $request,
        Groupe $groupe,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        
        if (!$user instanceof Utilisateur) {
            return $this->json(['error' => 'Vous devez être connecté'], 401);
        }

        // Only owner can update
        if ($groupe->getCreateur() !== $user) {
            return $this->json(['error' => 'Vous ne pouvez pas modifier ce groupe'], 403);
        }

        $form = $this->createForm(GroupeType::class, $groupe, [
            'is_edit' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->json([
                'success' => true,
                'message' => 'Le groupe a été modifié avec succès',
            ]);
        }

        $errors = [];
        foreach ($form->getErrors(true, true) as $error) {
            $errors[] = $error->getMessage();
        }

        return $this->json([
            'success' => false,
            'errors' => $errors,
        ], 400);
    }

    #[Route('/groupes/{id}/rejoindre', name: 'groupe_join', methods: ['POST'])]
    public function joinGroupe(
        Groupe $groupe,
        EntityManagerInterface $entityManager,
        MembreGroupeRepository $membreGroupeRepository
    ): Response {
        $user = $this->getUser();
        
        if (!$user instanceof UserInterface) {
            return $this->json(['error' => 'Vous devez être connecté'], 401);
        }

        /** @var Utilisateur $user */

        // Check if already a member
        $existingMembre = $membreGroupeRepository->findOneBy([
            'utilisateur' => $user,
            'groupe' => $groupe,
        ]);

        if ($existingMembre) {
            if ($existingMembre->isAccepted()) {
                return $this->json([
                    'success' => false,
                    'message' => 'Vous êtes déjà membre de ce groupe',
                ]);
            } elseif ($existingMembre->isPending()) {
                return $this->json([
                    'success' => false,
                    'message' => 'Votre demande est en attente de validation',
                ]);
            } else {
                // Rejected before, allow to request again
                $existingMembre->setStatut('PENDING');
                $entityManager->flush();
                return $this->json([
                    'success' => true,
                    'message' => 'Votre demande de rejoindre a été renouvelée',
                ]);
            }
        }

        // For public groups, auto-accept
        if ($groupe->isPublic()) {
            $membre = new MembreGroupe();
            $membre->setUtilisateur($user);
            $membre->setGroupe($groupe);
            $membre->setRoleMembre('MEMBRE');
            $membre->setStatut('ACCEPTED');
            $membre->setDateJoint(new \DateTimeImmutable());

            $entityManager->persist($membre);
            $entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Vous avez rejoint le groupe avec succès !',
            ]);
        }

        // For private groups, create a pending request
        $membre = new MembreGroupe();
        $membre->setUtilisateur($user);
        $membre->setGroupe($groupe);
        $membre->setRoleMembre('MEMBRE');
        $membre->setStatut('PENDING');
        $membre->setDateJoint(new \DateTimeImmutable());

        $entityManager->persist($membre);
        
        // Create notification for the group owner
        $owner = $groupe->getCreateur();
        if ($owner && $owner !== $user) {
            $notification = new Notification();
            $notification->setUtilisateur($owner);
            $notification->setType('join_request');
            $notification->setTitle('Demande de rejoindre');
            $userName = $user->getProfil()?->getNom() ?? explode('@', $user->getEmail())[0];
            $notification->setMessage($userName . ' demande à rejoindre le groupe "' . $groupe->getNom() . '"');
            $notification->setLink('/chat/groupe/' . $groupe->getId());
            $notification->setMembre($membre);
            $entityManager->persist($notification);
        }
        
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Votre demande de rejoindre a été envoyée. En attente de validation par le propriétaire.',
        ]);
    }

    #[Route('/groupes/{id}/quitter', name: 'groupe_leave', methods: ['POST'])]
    public function leaveGroupe(
        Groupe $groupe,
        EntityManagerInterface $entityManager,
        MembreGroupeRepository $membreGroupeRepository
    ): Response {
        $user = $this->getUser();
        
        if (!$user instanceof UserInterface) {
            return $this->json(['error' => 'Vous devez être connecté'], 401);
        }

        // Check if user is the creator (can't leave own group)
        if ($groupe->getCreateur() === $user) {
            return $this->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas quitter un groupe que vous avez créé. Supprimez-le plutôt.',
            ]);
        }

        // Find and remove membership
        $membre = $membreGroupeRepository->findOneBy([
            'utilisateur' => $user,
            'groupe' => $groupe,
        ]);

        if (!$membre) {
            return $this->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas membre de ce groupe',
            ]);
        }

        $entityManager->remove($membre);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Vous avez quitté le groupe',
        ]);
    }

    #[Route('/groupes/{id}/demandes', name: 'groupe_requests', methods: ['GET'])]
    public function showRequests(
        Groupe $groupe,
        MembreGroupeRepository $membreGroupeRepository
    ): Response {
        $user = $this->getUser();
        
        // Only owner can see requests
        if ($groupe->getCreateur() !== $user) {
            return $this->json(['error' => 'Non autorisé'], 403);
        }

        $pendingRequests = $membreGroupeRepository->findBy([
            'groupe' => $groupe,
            'statut' => 'PENDING'
        ]);

        $requestsData = [];
        foreach ($pendingRequests as $request) {
            $requestsData[] = [
                'id' => $request->getId(),
                'utilisateur' => [
                    'id' => $request->getUtilisateur()->getId(),
                    'nom' => $request->getUtilisateur()->getProfil()?->getNom() ?? $request->getUtilisateur()->getEmail(),
                ],
                'date' => $request->getDateJoint()->format('d/m/Y H:i'),
            ];
        }

        return $this->json([
            'success' => true,
            'requests' => $requestsData,
        ]);
    }

    #[Route('/groupes/{id}/membres', name: 'groupe_membres', methods: ['GET'])]
    public function getMembres(
        Groupe $groupe,
        MembreGroupeRepository $membreGroupeRepository
    ): Response {
        $user = $this->getUser();
        
        // Get accepted members
        $membres = $membreGroupeRepository->findBy([
            'groupe' => $groupe,
            'statut' => 'ACCEPTED'
        ]);

        $membresData = [];
        foreach ($membres as $membre) {
            $membresData[] = [
                'id' => $membre->getId(),
                'utilisateur' => [
                    'id' => $membre->getUtilisateur()->getId(),
                    'nom' => $membre->getUtilisateur()->getProfil()?->getNom() ?? $membre->getUtilisateur()->getEmail(),
                ],
                'role' => $membre->getRoleMembre(),
                'date' => $membre->getDateJoint()->format('d/m/Y H:i'),
            ];
        }

        return $this->json([
            'success' => true,
            'requests' => $membresData,
        ]);
    }

    #[Route('/groupes/{id}/accepter/{membreId}', name: 'groupe_accept_member', methods: ['POST'])]
    public function acceptMember(
        Groupe $groupe,
        int $membreId,
        EntityManagerInterface $entityManager,
        MembreGroupeRepository $membreGroupeRepository
    ): Response {
        $user = $this->getUser();
        
        // Only owner or admin can accept members
        $isAdmin = ($user instanceof Utilisateur && $user->isAdmin());
        if ($groupe->getCreateur() !== $user && !$isAdmin) {
            return $this->json(['error' => 'Non autorisé'], 403);
        }

        $membre = $membreGroupeRepository->find($membreId);
        
        if (!$membre || $membre->getGroupe() !== $groupe) {
            return $this->json(['error' => 'Membre non trouvé'], 404);
        }

        $membre->setStatut('ACCEPTED');
        
        // Create notification for the accepted user
        $requester = $membre->getUtilisateur();
        $notification = new Notification();
        $notification->setUtilisateur($requester);
        $notification->setType('join_accepted');
        $notification->setTitle('Demande acceptée');
        $notification->setMessage('Votre demande de rejoindre le groupe "' . $groupe->getNom() . '" a été acceptée');
        $notification->setLink('/chat/groupe/' . $groupe->getId());
        $entityManager->persist($notification);
        
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Membre accepté',
        ]);
    }

    #[Route('/groupes/{id}/rejeter/{membreId}', name: 'groupe_reject_member', methods: ['POST'])]
    public function rejectMember(
        Groupe $groupe,
        int $membreId,
        EntityManagerInterface $entityManager,
        MembreGroupeRepository $membreGroupeRepository
    ): Response {
        $user = $this->getUser();
        
        // Only owner or admin can reject members
        $isAdmin = ($user instanceof Utilisateur && $user->isAdmin());
        if ($groupe->getCreateur() !== $user && !$isAdmin) {
            return $this->json(['error' => 'Non autorisé'], 403);
        }

        $membre = $membreGroupeRepository->find($membreId);
        
        if (!$membre || $membre->getGroupe() !== $groupe) {
            return $this->json(['error' => 'Membre non trouvé'], 404);
        }

        $membre->setStatut('REJECTED');
        
        // Create notification for the rejected user
        $requester = $membre->getUtilisateur();
        $notification = new Notification();
        $notification->setUtilisateur($requester);
        $notification->setType('join_rejected');
        $notification->setTitle('Demande refusée');
        $notification->setMessage('Votre demande de rejoindre le groupe "' . $groupe->getNom() . '" a été refusée');
        $entityManager->persist($notification);
        
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Demande refusée',
        ]);
    }

    #[Route('/groupes/{id}/supprimer-membre/{membreId}', name: 'groupe_remove_member', methods: ['POST'])]
    public function removeMember(
        Groupe $groupe,
        int $membreId,
        EntityManagerInterface $entityManager,
        MembreGroupeRepository $membreGroupeRepository
    ): Response {
        $user = $this->getUser();
        
        // Only owner or admin can remove members
        $isAdmin = ($user instanceof Utilisateur && $user->isAdmin());
        if ($groupe->getCreateur() !== $user && !$isAdmin) {
            return $this->json(['error' => 'Non autorisé'], 403);
        }

        $membre = $membreGroupeRepository->find($membreId);
        
        if (!$membre || $membre->getGroupe() !== $groupe) {
            return $this->json(['error' => 'Membre non trouvé'], 404);
        }

        // Can't remove yourself (owner)
        if ($membre->getUtilisateur() === $user) {
            return $this->json(['error' => 'Vous ne pouvez pas vous supprimer vous-même'], 400);
        }

        $entityManager->remove($membre);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Membre supprimé du groupe',
        ]);
    }

    #[Route('/chat', name: 'chat_index')]
    public function chatIndex(
        GroupeRepository $groupeRepository
    ): Response {
        $user = $this->getUser();
        
        if (!$user instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }
        
        // Get user's joined groups + owned groups
        $groupes = $groupeRepository->findJoinedGroupsByUser($user);
        $ownedGroups = $groupeRepository->findBy(['createur' => $user]);
        
        // Merge owned groups
        $seenIds = [];
        foreach ($groupes as $g) {
            $seenIds[] = $g->getId();
        }
        foreach ($ownedGroups as $ownedGroup) {
            if (!in_array($ownedGroup->getId(), $seenIds)) {
                $groupes[] = $ownedGroup;
                $seenIds[] = $ownedGroup->getId();
            }
        }
        
        if (!empty($groupes)) {
            return $this->redirectToRoute('chat_groupe', ['id' => $groupes[0]->getId()]);
        }
        
        // If no groups, redirect to group list
        return $this->redirectToRoute('groupe_index');
    }

    #[Route('/chat/groupe/{id}', name: 'chat_groupe')]
    public function chatGroupe(
        Groupe $groupe,
        MessageRepository $messageRepository,
        MembreGroupeRepository $membreGroupeRepository,
        GroupeRepository $groupeRepository
    ): Response {
        $user = $this->getUser();
        $isMember = false;
        $isOwner = false;
        $isAdmin = false;
        $membership = null;
        
        if ($user instanceof UserInterface) {
            // Check if user is admin
            if ($user instanceof Utilisateur && $user->isAdmin()) {
                $isAdmin = true;
            }
            
            // Check if user is owner
            if ($groupe->getCreateur() === $user) {
                $isOwner = true;
                $isMember = true;
            } else {
                // Check if user has any membership (accepted, pending, or null)
                $membership = $membreGroupeRepository->findOneBy([
                    'utilisateur' => $user,
                    'groupe' => $groupe,
                ]);
                
                if ($membership) {
                    // Has a membership record - check if accepted
                    if ($membership->isAccepted()) {
                        $isMember = true;
                    } elseif ($membership->isPending()) {
                        // Pending member - not allowed to chat yet
                        $isMember = false;
                    } elseif ($membership->getStatut() === null) {
                        // Old membership without statut - allow access
                        $isMember = true;
                    }
                }
            }
        }

        // Check pending requests count for owner
        $pendingRequestsCount = 0;
        if ($isOwner) {
            $pendingRequestsCount = $membreGroupeRepository->count([
                'groupe' => $groupe,
                'statut' => 'PENDING'
            ]);
        }

        // Only show messages to members
        if ($isMember || $isOwner || $isAdmin) {
            $messages = $messageRepository->findMessagesForGroupe($groupe, 'recent', 50);
        } else {
            $messages = [];
        }
        
        // Get user's groups for sidebar
        $userGroups = [];
        if ($user instanceof UserInterface) {
            // Get groups user is member of
            $userMemberships = $membreGroupeRepository->findBy(['utilisateur' => $user, 'statut' => 'ACCEPTED']);
            foreach ($userMemberships as $membre) {
                $userGroups[] = $membre->getGroupe();
            }
            // Also add groups user owns
            $ownedGroups = $groupeRepository->findBy(['createur' => $user]);
            foreach ($ownedGroups as $ownedGroup) {
                $alreadyAdded = false;
                foreach ($userGroups as $g) {
                    if ($g->getId() === $ownedGroup->getId()) {
                        $alreadyAdded = true;
                        break;
                    }
                }
                if (!$alreadyAdded) {
                    $userGroups[] = $ownedGroup;
                }
            }
        }

        return $this->render('chat/groupe.html.twig', [
            'groupe' => $groupe,
            'messages' => $messages,
            'isMember' => $isMember,
            'isOwner' => $isOwner,
            'isAdmin' => $isAdmin,
            'groupes' => $userGroups,
            'pendingRequestsCount' => $pendingRequestsCount,
            'membership' => $membership,
        ]);
    }

    #[Route('/chat/groupe/{id}/envoyer', name: 'chat_send_message', methods: ['POST'])]
    public function sendMessage(
        Request $request,
        Groupe $groupe,
        MessageRepository $messageRepository,
        MembreGroupeRepository $membreGroupeRepository,
        EntityManagerInterface $entityManager,
        \Symfony\Component\Validator\Validator\ValidatorInterface $validator
    ): Response {
        $user = $this->getUser();
        
        if (!$user instanceof UserInterface) {
            return $this->json(['error' => 'Vous devez être connecté'], 401);
        }

        // Check if user is member, owner, or admin
        $isOwner = ($groupe->getCreateur() === $user);
        $isAdmin = ($user instanceof Utilisateur && $user->isAdmin());
        
        if (!$isOwner && !$isAdmin) {
            $membership = $membreGroupeRepository->findOneBy([
                'utilisateur' => $user,
                'groupe' => $groupe,
            ]);
            
            if (!$membership || !$membership->isAccepted()) {
                return $this->json(['error' => 'Vous devez être membre du groupe pour envoyer des messages'], 403);
            }
        }

        $contenu = $request->request->get('contenu', '');
        $replyTo = $request->request->get('replyTo', null);
        $ficheId = $request->request->get('ficheId', null);
        
        // Handle file upload
        $file = $request->files->get('file');
        $filePath = null;
        $fileName = null;
        $typeMessage = 'TEXTE';
        $fiche = null;
        
        if ($file) {
            $typeMessage = 'IMAGE';
            // Check if it's an image or other file type
            $mimeType = $file->getMimeType() ?? 'application/octet-stream';
            if (strpos($mimeType, 'image/') === 0) {
                $typeMessage = 'IMAGE';
            } elseif ($mimeType === 'application/pdf') {
                $typeMessage = 'PDF';
            }
            
            // Generate unique filename
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $newFilename = uniqid() . '.' . $extension;
            
            // Move file to uploads directory
            $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/chat';
            if (!file_exists($uploadsDir)) {
                mkdir($uploadsDir, 0777, true);
            }
            
            $file->move($uploadsDir, $newFilename);
            $filePath = '/uploads/chat/' . $newFilename;
            $fileName = $originalName;
            
            // If no text content, use filename as content
            if (empty($contenu)) {
                $contenu = $originalName;
            }
        }
        
        // Handle fiche sharing
        $fiche = null;
        if (!empty($ficheId) && empty($contenu)) {
            $fiche = $entityManager->getRepository(Fiche::class)->find((int) $ficheId);
            if ($fiche) {
                $contenu = 'Fiche: ' . $fiche->getTitle();
            } else {
                // Fiche not found, use default content
                $contenu = 'Partage de fiche';
            }
        }
        
        $message = new Message();
        $message->setContenu($contenu);
        $message->setExpediteur($user);
        $message->setGroupe($groupe);
        $message->setCreatedAt(new \DateTimeImmutable());
        $message->setTypeMessage($typeMessage);
        
        if ($filePath) {
            $message->setFilePath($filePath);
            $message->setFileName($fileName);
        }
        
        if ($fiche) {
            $message->setFiche($fiche);
        }
        
        if ($replyTo) {
            $parentMessage = $messageRepository->find($replyTo);
            if ($parentMessage) {
                $message->setParentMessage($parentMessage);
            }
        }

        // Validate using Symfony's validator
        $errors = $validator->validate($message);
        
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['error' => implode(', ', $errorMessages)], 400);
        }

        $entityManager->persist($message);
        $entityManager->flush();

        // Get user name for response
        if ($user instanceof Utilisateur) {
            $profil = $user->getProfil();
            if ($profil !== null && $profil->getNom()) {
                $userName = $profil->getNom();
            } else {
                $userName = explode('@', $user->getEmail())[0];
            }
        } else {
            // Fallback for other UserInterface implementations
            $userName = $user->getUserIdentifier();
        }

        return $this->json([
            'success' => true,
            'message' => [
                'id' => $message->getId(),
                'contenu' => $message->getContenu(),
                'typeMessage' => $message->getTypeMessage(),
                'createdAt' => $message->getCreatedAt()->format('d/m/Y H:i'),
                'parentMessage' => $message->getParentMessage() ? [
                    'id' => $message->getParentMessage()->getId(),
                    'contenu' => substr($message->getParentMessage()->getContenu(), 0, 50) . '...',
                ] : null,
                'expediteur' => [
                    'nom' => $userName,
                ],
                'file' => $message->getFilePath() ? [
                    'path' => $message->getFilePath(),
                    'name' => $message->getFileName(),
                ] : null,
            ],
        ]);
    }

    #[Route('/chat/groupe/{id}/messages', name: 'chat_messages', methods: ['GET'])]
    public function getMessages(
        Groupe $groupe,
        MessageRepository $messageRepository,
        MembreGroupeRepository $membreGroupeRepository
    ): Response {
        $user = $this->getUser();
        
        // Check if user is member or owner
        $isOwner = ($user && $groupe->getCreateur() === $user);
        
        if (!$isOwner && $user) {
            $membership = $membreGroupeRepository->findOneBy([
                'utilisateur' => $user,
                'groupe' => $groupe,
            ]);
            
            if (!$membership || !$membership->isAccepted()) {
                return $this->json(['error' => 'Non autorisé'], 403);
            }
        } elseif (!$user) {
            return $this->json(['error' => 'Non autorisé'], 403);
        }

        $messages = $messageRepository->findMessagesForGroupe($groupe, 'recent', 50);

        $messagesData = [];
        foreach ($messages as $message) {
            $expediteur = $message->getExpediteur();
            $profil = $expediteur->getProfil();
            $userName = '';
            if ($profil !== null && $profil->getNom()) {
                $userName = $profil->getNom();
            } else {
                $userName = explode('@', $expediteur->getEmail())[0];
            }

            $messagesData[] = [
                'id' => $message->getId(),
                'contenu' => $message->getContenu(),
                'createdAt' => $message->getCreatedAt()->format('d/m/Y H:i'),
                'expediteur' => [
                    'id' => $expediteur->getId(),
                    'nom' => $userName,
                ],
                'replyTo' => $message->getParentMessage() ? [
                    'id' => $message->getParentMessage()->getId(),
                    'contenu' => substr($message->getParentMessage()->getContenu(), 0, 50) . '...',
                ] : null,
            ];
        }

        return $this->json([
            'success' => true,
            'messages' => $messagesData,
        ]);
    }

    #[Route('/chat/message/{id}/supprimer', name: 'chat_delete_message', methods: ['POST'])]
    public function deleteMessage(
        Message $message,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        
        if (!$user instanceof Utilisateur) {
            return $this->json(['error' => 'Vous devez être connecté'], 401);
        }

        // Check if user is the sender or is admin
        $isSender = $message->getExpediteur() === $user;
        $isAdmin = $user->isAdmin();
        
        // Check if user is owner of the group containing this message
        $isGroupOwner = false;
        $groupe = $message->getGroupe();
        if ($groupe && $groupe->getCreateur() === $user) {
            $isGroupOwner = true;
        }
        
        if (!$isSender && !$isAdmin && !$isGroupOwner) {
            return $this->json(['error' => 'Vous ne pouvez pas supprimer ce message'], 403);
        }

        // Soft delete the message
        $message->setDeletedAt(new \DateTimeImmutable());
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Message supprimé',
        ]);
    }

    #[Route('/chat/message/{id}/editer', name: 'chat_edit_message', methods: ['POST'])]
    public function editMessage(
        Request $request,
        Message $message,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        
        if (!$user instanceof Utilisateur) {
            return $this->json(['error' => 'Vous devez être connecté'], 401);
        }

        // Check if user is the sender
        if ($message->getExpediteur() !== $user) {
            return $this->json(['error' => 'Vous ne pouvez pas modifier ce message'], 403);
        }

        $contenu = $request->request->get('contenu', '');
        
        if (empty(trim($contenu))) {
            return $this->json(['error' => 'Le message ne peut pas être vide'], 400);
        }

        $message->setContenu($contenu);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => [
                'id' => $message->getId(),
                'contenu' => $message->getContenu(),
            ],
        ]);
    }

    #[Route('/groupes/{id}/supprimer', name: 'groupe_delete', methods: ['POST'])]
    public function deleteGroupe(
        Groupe $groupe,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        
        if (!$user instanceof Utilisateur) {
            return $this->json(['error' => 'Vous devez être connecté'], 401);
        }

        // Check if user is the creator (owner) of the group or is admin
        $isOwner = $groupe->getCreateur() === $user;
        $isAdmin = $user->isAdmin();
        
        if (!$isOwner && !$isAdmin) {
            return $this->json(['error' => 'Vous ne pouvez pas supprimer ce groupe'], 403);
        }

        // Delete related entities first
        // Delete messages
        foreach ($groupe->getMessages() as $message) {
            $entityManager->remove($message);
        }

        // Delete members
        foreach ($groupe->getMembres() as $membre) {
            $entityManager->remove($membre);
        }

        // Delete the group
        $entityManager->remove($groupe);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'redirect' => $this->generateUrl('groupe_index'),
        ]);
    }

    // ========== EMOJI API ==========
    #[Route('/chat/emojis', name: 'chat_emojis', methods: ['GET'])]
    public function getEmojis(Request $request): Response
    {
        $category = $request->query->get('category', 'frequent');
        
        // Emojis as HTML entities for better compatibility
        $emojis = [
            'frequent' => [
                '😀', '😂', '😊', '😍', '👍', '👋', '❤', '🎉', '✨', '🙌', 
                '💪', '🔥', '💯', '🙏', '🎊', '✅', '💙', '🤔', '😎', '🥰'
            ],
            'smileys' => [
                '😀', '😃', '😄', '😁', '😆', '😅', '🤣', '😂', '🙂', '😊', 
                '😇', '🥰', '😍', '🤩', '😘', '😗', '😚', '😙', '😋', '😛', 
                '😜', '🤪', '😝', '🤑', '🤗', '🤭', '🤫', '🤔', '🤐', '🤨', 
                '😐', '😑', '😶', '😏', '😒', '🙄', '😬', '🤥', '😌', '😔', 
                '😪', '🤤', '😴', '😷', '🤒', '🤕', '🤢', '🤮', '🤧', '🥵', 
                '🥶', '🥴', '😵', '🤯', '🤠', '🥳', '😎', '🤓', '🧐', '😕', 
                '😟', '🙁', '☹️', '😮', '😯', '😲', '😳', '🥺', '😦', '😧', 
                '😨', '😰', '😥', '😢', '😭', '😱', '😖', '😣', '😞', '😓', 
                '😩', '😫', '🥱', '😤', '😡', '😠', '🤬', '😈', '👿', '💀', 
                '☠️', '💩', '🤡', '👹', '👺', '👻', '👽', '👾', '🤖'
            ],
            'gestures' => [
                '👋', '🤚', '🖐️', '✋', '🖖', '👌', '🤌', '🤏', '✌️', '🤞', 
                '🤟', '🤘', '🤙', '👈', '👉', '👆', '🖕', '👇', '☝️', '👍', 
                '👎', '✊', '👊', '🤛', '🤜', '👏', '🙌', '👐', '🤲', '🤝', 
                '🙏', '✍️', '💪', '🦾', '🦿', '🦵', '🦶', '👂', '🦻', '👃', 
                '🧠', '🫀', '🫁', '🦷', '🦴', '👀', '👁️', '👅', '👄'
            ],
            'hearts' => [
                '❤', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍', '🤎', '💔', 
                '❣️', '💕', '💞', '💓', '💗', '💖', '💘', '💝', '💟', '♥️'
            ],
            'objects' => [
                '⚽', '🏀', '🏈', '⚾', '🥎', '🎾', '🏐', '🏉', '🥏', '🎱', 
                '🪀', '🏓', '🏸', '🏒', '🏑', '🥍', '🏏', '🪃', '🥅', '⛳', 
                '🪁', '🏹', '🎣', '🤿', '🥊', '🥋', '🎽', '🛹', '🛼', '🛷', 
                '⛸️', '🥌', '🎿', '⛷️', '🏂', '🪂', '🏋️', '🤼', '🤸', '🤺', 
                '⛹️', '🤾', '🏌️', '🏇', '🧘', '🏄', '🏊', '🤽', '🚣', '🧗', 
                '🚵', '🚴', '🏆', '🥇', '🥈', '🥉', '🏅', '🎖️', '🏵️', '🎗️', 
                '🎫', '🎟️', '🎪', '🎭', '🎨', '🎬', '🎤', '🎧', '🎼', '🎹', 
                '🥁', '🪘', '🎷', '🎺', '🪗', '🎸', '🪕', '🎻', '🎲', '♟️', 
                '🎯', '🎳', '🎮', '🎰', '🧩'
            ]
        ];
        
        $result = $emojis[$category] ?? $emojis['frequent'];
        
        // Ensure proper UTF-8 encoding
        $result = array_map(function($emoji) {
            return mb_encode_numericentity(
                $emoji, 
                [0x80, 0x10FFFF, 0, 0x1FFFFF], 
                'UTF-8'
            );
        }, $result);
        
        return $this->json([
            'success' => true,
            'category' => $category,
            'emojis' => $result,
        ]);
    }

    // ========================
    // API Routes for Floating Chat
    // ========================

    #[Route('/api/chat/conversations', name: 'api_chat_conversations')]
    public function apiConversations(
        GroupeRepository $groupeRepository,
        MessageRepository $messageRepository
    ): Response {
        $user = $this->getUser();
        
        if (!$user instanceof UserInterface) {
            return $this->json(['success' => false, 'error' => 'Non connecté'], 401);
        }
        
        // Get user's groups
        $groupes = $groupeRepository->findJoinedGroupsByUser($user);
        $ownedGroups = $groupeRepository->findBy(['createur' => $user]);
        
        // Merge owned groups
        $seenIds = [];
        foreach ($groupes as $g) {
            $seenIds[] = $g->getId();
        }
        foreach ($ownedGroups as $ownedGroup) {
            if (!in_array($ownedGroup->getId(), $seenIds)) {
                $groupes[] = $ownedGroup;
            }
        }
        
        $conversations = [];
        foreach ($groupes as $groupe) {
            $lastMessage = $messageRepository->findOneBy(
                ['groupe' => $groupe],
                ['createdAt' => 'DESC']
            );
            
            $conversations[] = [
                'id' => $groupe->getId(),
                'nom' => $groupe->getNom(),
                'description' => $groupe->getDescription(),
                'isPublic' => $groupe->isPublic(),
                'membreCount' => $groupe->getMembres()->count(),
                'dernierMessage' => $lastMessage ? $lastMessage->getContenu() : null,
                'lastMessageTime' => $lastMessage ? $lastMessage->getCreatedAt()->format('Y-m-d H:i:s') : null,
                'timeAgo' => $lastMessage ? $this->getTimeAgo($lastMessage->getCreatedAt()) : '',
                'unreadCount' => 0, // Can be implemented with a read status
            ];
        }
        
        // Sort by last message time
        usort($conversations, function($a, $b) {
            if (!$a['lastMessageTime']) return 1;
            if (!$b['lastMessageTime']) return -1;
            return strtotime($b['lastMessageTime']) - strtotime($a['lastMessageTime']);
        });
        
        return $this->json(['success' => true, 'conversations' => $conversations]);
    }

    #[Route('/api/chat/messages/{groupId}', name: 'api_chat_messages')]
    public function apiMessages(
        int $groupId,
        MessageRepository $messageRepository,
        GroupeRepository $groupeRepository,
        MembreGroupeRepository $membreGroupeRepository
    ): Response {
        $user = $this->getUser();
        
        if (!$user instanceof UserInterface) {
            return $this->json(['success' => false, 'error' => 'Non connecté'], 401);
        }
        
        $groupe = $groupeRepository->find($groupId);
        
        if (!$groupe) {
            return $this->json(['success' => false, 'error' => 'Groupe non trouvé'], 404);
        }
        
        // Check if user is member
        $isMember = false;
        if ($groupe->getCreateur() === $user) {
            $isMember = true;
        } else {
            $membership = $membreGroupeRepository->findOneBy([
                'utilisateur' => $user,
                'groupe' => $groupe,
            ]);
            if ($membership && $membership->isAccepted()) {
                $isMember = true;
            }
        }
        
        if (!$isMember) {
            return $this->json(['success' => false, 'error' => 'Vous n\'êtes pas membre de ce groupe'], 403);
        }
        
        $messages = $messageRepository->findMessagesForGroupe($groupe, 'recent', 50);
        
        $messagesData = [];
        foreach ($messages as $msg) {
            $messagesData[] = [
                'id' => $msg->getId(),
                'contenu' => $msg->getContenu(),
                'typeMessage' => $msg->getTypeMessage(),
                'expediteurId' => $msg->getExpediteur()->getId(),
                'expediteurNom' => $msg->getExpediteur()->getProfil() ? 
                    $msg->getExpediteur()->getProfil()->getNom() : 
                    substr($msg->getExpediteur()->getEmail(), 0, strpos($msg->getExpediteur()->getEmail(), '@')),
                'createdAt' => $msg->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }
        
        return $this->json(['success' => true, 'messages' => $messagesData]);
    }

    #[Route('/api/chat/send/{groupId}', name: 'api_chat_send')]
    public function apiSendMessage(
        Request $request,
        int $groupId,
        MessageRepository $messageRepository,
        GroupeRepository $groupeRepository,
        MembreGroupeRepository $membreGroupeRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        
        if (!$user instanceof Utilisateur) {
            return $this->json(['success' => false, 'error' => 'Non connecté'], 401);
        }
        
        $groupe = $groupeRepository->find($groupId);
        
        if (!$groupe) {
            return $this->json(['success' => false, 'error' => 'Groupe non trouvé'], 404);
        }
        
        // Check if user is member
        $isMember = false;
        if ($groupe->getCreateur() === $user) {
            $isMember = true;
        } else {
            $membership = $membreGroupeRepository->findOneBy([
                'utilisateur' => $user,
                'groupe' => $groupe,
            ]);
            if ($membership && $membership->isAccepted()) {
                $isMember = true;
            }
        }
        
        if (!$isMember) {
            return $this->json(['success' => false, 'error' => 'Vous n\'êtes pas membre de ce groupe'], 403);
        }
        
        $contenu = $request->request->get('contenu', '');
        
        if (empty(trim($contenu))) {
            return $this->json(['success' => false, 'error' => 'Le message ne peut pas être vide']);
        }
        
        $message = new Message();
        $message->setContenu($contenu);
        $message->setTypeMessage('TEXTE');
        $message->setExpediteur($user);
        $message->setGroupe($groupe);
        
        $entityManager->persist($message);
        $entityManager->flush();
        
        $userName = $user->getProfil() ? 
            $user->getProfil()->getNom() : 
            substr($user->getEmail(), 0, strpos($user->getEmail(), '@'));
        
        return $this->json([
            'success' => true,
            'message' => [
                'id' => $message->getId(),
                'contenu' => $message->getContenu(),
                'typeMessage' => $message->getTypeMessage(),
                'expediteurId' => $user->getId(),
                'expediteurNom' => $userName,
                'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    // Helper function to get time ago string
    private function getTimeAgo(\DateTimeImmutable $dateTime): string {
        $now = new \DateTimeImmutable();
        $diff = $now->getTimestamp() - $dateTime->getTimestamp();
        
        if ($diff < 60) return 'À l\'instant';
        if ($diff < 3600) return floor($diff / 60) . ' min';
        if ($diff < 86400) return floor($diff / 3600) . 'h';
        if ($diff < 604800) return floor($diff / 86400) . 'j';
        
        return $dateTime->format('d/m');
    }
}
