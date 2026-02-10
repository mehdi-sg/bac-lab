<?php

namespace App\Controller;

use App\Entity\Groupe;
use App\Entity\Message;
use App\Entity\MembreGroupe;
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
    public function index(GroupeRepository $groupeRepository): Response
    {
        $groupes = $groupeRepository->findAll();
        
        return $this->render('groupe/index.html.twig', [
            'groupes' => $groupes,
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
        $membres = $membreGroupeRepository->findBy(['groupe' => $groupe]);
        
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

        // Check if already a member
        $existingMembre = $membreGroupeRepository->findOneBy([
            'utilisateur' => $user,
            'groupe' => $groupe,
        ]);

        if ($existingMembre) {
            return $this->json([
                'success' => false,
                'message' => 'Vous êtes déjà membre de ce groupe',
            ]);
        }

        // Check if group is public or user is invited (for private groups)
        if (!$groupe->isPublic()) {
            return $this->json([
                'success' => false,
                'message' => 'Ce groupe est privé. Vous devez être invité pour rejoindre.',
            ]);
        }

        // Add user as member
        $membre = new MembreGroupe();
        $membre->setUtilisateur($user);
        $membre->setGroupe($groupe);
        $membre->setRoleMembre('MEMBRE');
        $membre->setDateJoint(new \DateTimeImmutable());

        $entityManager->persist($membre);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Vous avez rejoint le groupe avec succès !',
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

    #[Route('/chat', name: 'chat_index')]
    public function chatIndex(
        GroupeRepository $groupeRepository
    ): Response {
        // Redirect to the first public group, or show empty state
        $groupes = $groupeRepository->findBy(['isPublic' => true]);
        
        if (!empty($groupes)) {
            return $this->redirectToRoute('chat_groupe', ['id' => $groupes[0]->getId()]);
        }
        
        // If no groups exist, redirect to home
        return $this->redirectToRoute('app_home');
    }

    #[Route('/chat/groupe/{id}', name: 'chat_groupe')]
    public function chatGroupe(
        Groupe $groupe,
        MessageRepository $messageRepository,
        MembreGroupeRepository $membreGroupeRepository,
        GroupeRepository $groupeRepository
    ): Response {
        $messages = $messageRepository->findMessagesForGroupe($groupe, 'recent', 50);
        
        // Check if user is member (for private groups)
        $user = $this->getUser();
        $isMember = false;
        
        if ($user instanceof UserInterface) {
            $membre = $membreGroupeRepository->findOneBy([
                'utilisateur' => $user,
                'groupe' => $groupe,
            ]);
            $isMember = $membre !== null;
        }

        // For private groups, redirect if not member
        if (!$groupe->isPublic() && !$isMember && $user instanceof UserInterface) {
            $this->addFlash('error', 'Vous devez rejoindre ce groupe pour voir les messages.');
            return $this->redirectToRoute('groupe_index');
        }
        
        // For public groups, allow viewing but not posting

        return $this->render('chat/groupe.html.twig', [
            'groupe' => $groupe,
            'messages' => $messages,
            'groupes' => $groupeRepository->findAll(),
            'isMember' => $isMember,
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

        // Check if user is member of the group (for both public and private)
        $membre = $membreGroupeRepository->findOneBy([
            'utilisateur' => $user,
            'groupe' => $groupe,
        ]);

        if (!$membre && $groupe->getCreateur() !== $user) {
            return $this->json(['error' => 'Vous devez rejoindre ce groupe pour envoyer des messages'], 403);
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
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        
        if (!$user instanceof Utilisateur) {
            return $this->json(['error' => 'Vous devez être connecté'], 401);
        }

        // Check if user is member of the group
        $groupe = $parentMessage->getGroupe();
        $membre = $entityManager->getRepository(MembreGroupe::class)->findOneBy([
            'utilisateur' => $user,
            'groupe' => $groupe,
        ]);

        if (!$membre && $groupe->getCreateur() !== $user) {
            return $this->json(['error' => 'Vous devez rejoindre ce groupe'], 403);
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
