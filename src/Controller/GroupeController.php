<?php

namespace App\Controller;

use App\Entity\Groupe;
use App\Entity\Message;
use App\Entity\MembreGroupe;
use App\Entity\Notification;
use App\Entity\Utilisateur;
use App\Form\GroupeType;
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
    public function index(GroupeRepository $groupeRepository, MembreGroupeRepository $membreGroupeRepository): Response
    {
        $groupes = $groupeRepository->findAll();
        $user = $this->getUser();
        
        // Get user's memberships for each group
        $userMemberships = [];
        if ($user instanceof UserInterface) {
            $membres = $membreGroupeRepository->findBy(['utilisateur' => $user]);
            foreach ($membres as $membre) {
                $userMemberships[$membre->getGroupe()->getId()] = $membre;
            }
        }
        
        return $this->render('groupe/index.html.twig', [
            'groupes' => $groupes,
            'userMemberships' => $userMemberships,
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

        if ($form->isSubmitted() && $form->isValid()) {
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

    #[Route('/groupes/{id}/accepter/{membreId}', name: 'groupe_accept_member', methods: ['POST'])]
    public function acceptMember(
        Groupe $groupe,
        int $membreId,
        EntityManagerInterface $entityManager,
        MembreGroupeRepository $membreGroupeRepository
    ): Response {
        $user = $this->getUser();
        
        // Only owner can accept members
        if ($groupe->getCreateur() !== $user) {
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
        
        // Only owner can reject members
        if ($groupe->getCreateur() !== $user) {
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
        
        // Only owner can remove members
        if ($groupe->getCreateur() !== $user) {
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
            return $this->redirectToRoute('app_home');
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
        $membership = null;
        
        if ($user instanceof UserInterface) {
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
        if ($isMember || $isOwner) {
            $messages = $messageRepository->findMessagesForGroupe($groupe, 'recent', 50);
        } else {
            $messages = [];
        }

        // Get user's joined groups for sidebar
        $userGroupes = [];
        if ($user instanceof UserInterface) {
            $userGroupes = $groupeRepository->findJoinedGroupsByUser($user);
            
            // Also include groups owned by the user
            $ownedGroups = $groupeRepository->findBy(['createur' => $user]);
            $seenIds = [];
            foreach ($userGroupes as $g) {
                $seenIds[] = $g->getId();
            }
            foreach ($ownedGroups as $ownedGroup) {
                if (!in_array($ownedGroup->getId(), $seenIds)) {
                    $userGroupes[] = $ownedGroup;
                }
            }
        }

        return $this->render('chat/groupe.html.twig', [
            'groupe' => $groupe,
            'messages' => $messages,
            'groupes' => $userGroupes,
            'isMember' => $isMember,
            'isOwner' => $isOwner,
            'membership' => $membership,
            'pendingRequestsCount' => $pendingRequestsCount,
        ]);
    }

    #[Route('/chat/groupe/{id}/envoyer', name: 'chat_send_message', methods: ['POST'])]
    public function sendMessage(
        Request $request,
        Groupe $groupe,
        EntityManagerInterface $entityManager,
        MembreGroupeRepository $membreGroupeRepository
    ): Response {
        $user = $this->getUser();
        
        if (!$user instanceof Utilisateur) {
            return $this->json(['error' => 'Vous devez être connecté'], 401);
        }

        // Check if user is owner or accepted member
        $isOwner = $groupe->getCreateur() === $user;
        
        if (!$isOwner) {
            $membre = $membreGroupeRepository->findOneBy([
                'utilisateur' => $user,
                'groupe' => $groupe,
            ]);
            
            if (!$membre || (!$membre->isAccepted() && $membre->getStatut() !== null)) {
                return $this->json(['error' => 'Vous devez être membre du groupe pour envoyer des messages'], 403);
            }
        }

        $contenu = $request->request->get('contenu', '');
        
        if (empty(trim($contenu))) {
            return $this->json(['error' => 'Le message ne peut pas être vide'], 400);
        }

        $message = new Message();
        $message->setContenu($contenu);
        $message->setTypeMessage('TEXTE');
        $message->setExpediteur($user);
        $message->setGroupe($groupe);
        
        $entityManager->persist($message);
        $entityManager->flush();

        // Get user display name
        $userName = 'Membre';
        if ($user instanceof Utilisateur) {
            $profil = $user->getProfil();
            if ($profil !== null && $profil->getNom()) {
                $userName = $profil->getNom();
            } else {
                $userName = explode('@', $user->getEmail())[0];
            }
        }

        return $this->json([
            'success' => true,
            'message' => [
                'id' => $message->getId(),
                'contenu' => $message->getContenu(),
                'createdAt' => $message->getCreatedAt()->format('d/m/Y H:i'),
                'expediteur' => [
                    'nom' => $userName,
                ],
            ],
        ]);
    }

    #[Route('/chat/groupe/{id}/repondre', name: 'chat_reply_message', methods: ['POST'])]
    public function replyToMessage(
        Request $request,
        Message $parentMessage,
        EntityManagerInterface $entityManager,
        MembreGroupeRepository $membreGroupeRepository
    ): Response {
        $user = $this->getUser();
        
        if (!$user instanceof Utilisateur) {
            return $this->json(['error' => 'Vous devez être connecté'], 401);
        }

        $groupe = $parentMessage->getGroupe();
        
        // Check if user is owner or accepted member
        $isOwner = $groupe->getCreateur() === $user;
        
        if (!$isOwner) {
            $membre = $membreGroupeRepository->findOneBy([
                'utilisateur' => $user,
                'groupe' => $groupe,
            ]);
            
            if (!$membre || (!$membre->isAccepted() && $membre->getStatut() !== null)) {
                return $this->json(['error' => 'Vous devez être membre du groupe'], 403);
            }
        }

        $contenu = $request->request->get('contenu', '');
        
        if (empty(trim($contenu))) {
            return $this->json(['error' => 'Le message ne peut pas être vide'], 400);
        }

        $message = new Message();
        $message->setContenu($contenu);
        $message->setTypeMessage('TEXTE');
        $message->setExpediteur($user);
        $message->setGroupe($groupe);
        $message->setParentMessage($parentMessage);
        
        $entityManager->persist($message);
        $entityManager->flush();

        // Get user display name
        $userName = 'Membre';
        $profil = $user->getProfil();
        if ($profil !== null && $profil->getNom()) {
            $userName = $profil->getNom();
        } else {
            $userName = explode('@', $user->getEmail())[0];
        }

        return $this->json([
            'success' => true,
            'message' => [
                'id' => $message->getId(),
                'contenu' => $message->getContenu(),
                'createdAt' => $message->getCreatedAt()->format('d/m/Y H:i'),
                'parentMessage' => [
                    'id' => $parentMessage->getId(),
                    'contenu' => substr($parentMessage->getContenu(), 0, 50) . '...',
                ],
                'expediteur' => [
                    'nom' => $userName,
                ],
            ],
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

        // Check if user is the sender
        if ($message->getExpediteur() !== $user) {
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
}
