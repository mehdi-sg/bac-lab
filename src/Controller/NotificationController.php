<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\MembreGroupe;
use App\Entity\Groupe;
use App\Repository\NotificationRepository;
use App\Repository\MembreGroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Csrf\CsrfTokenManagerInterface;

class NotificationController extends AbstractController
{
    #[Route('/notifications', name: 'notifications_index')]
    public function index(NotificationRepository $notificationRepository): Response
    {
        $user = $this->getUser();
        
        if (!$user instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        $notifications = $notificationRepository->findRecentByUser($user, 20);

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    #[Route('/notifications/mark-read/{id}', name: 'notification_mark_read', methods: ['POST'])]
    public function markRead(
        Notification $notification,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        
        if ($notification->getUtilisateur() !== $user) {
            return $this->json(['error' => 'Non autorisé'], 403);
        }

        $notification->setRead(true);
        $entityManager->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/notifications/mark-all-read', name: 'notifications_mark_all_read', methods: ['POST'])]
    public function markAllRead(
        NotificationRepository $notificationRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        
        if (!$user instanceof UserInterface) {
            return $this->json(['error' => 'Non autorisé'], 401);
        }

        $notificationRepository->markAllAsReadByUser($user);

        return $this->json(['success' => true]);
    }

    #[Route('/notifications/count', name: 'notifications_count')]
    public function count(NotificationRepository $notificationRepository): Response
    {
        $user = $this->getUser();
        
        if (!$user instanceof UserInterface) {
            return $this->json(['count' => 0]);
        }

        $count = $notificationRepository->countUnreadByUser($user);

        return $this->json(['count' => $count]);
    }

    #[Route('/notifications/{id}/accept', name: 'notification_accept', methods: ['POST'])]
    public function acceptJoinRequest(
        Notification $notification,
        EntityManagerInterface $entityManager,
        MembreGroupeRepository $membreGroupeRepository,
        Request $request
    ): Response {
        $user = $this->getUser();
        
        if (!$user instanceof UserInterface) {
            return $this->json(['error' => 'Non autorisé'], 401);
        }

        if ($notification->getUtilisateur() !== $user) {
            return $this->json(['error' => 'Non autorisé'], 403);
        }

        $membre = $notification->getMembre();
        $groupe = null;
        
        // If no membre associated, try to find it from the link
        if (!$membre instanceof MembreGroupe) {
            $link = $notification->getLink();
            if ($link && preg_match('/\/chat\/groupe\/(\d+)/', $link, $matches)) {
                $groupeId = (int) $matches[1];
                // Find the pending membership request for this group
                $membre = $membreGroupeRepository->findOneBy([
                    'groupe' => $groupeId,
                    'statut' => 'PENDING'
                ]);
                if ($membre) {
                    $groupe = $membre->getGroupe();
                }
            }
        } else {
            $groupe = $membre->getGroupe();
        }
        
        if (!$membre instanceof MembreGroupe || !$groupe instanceof Groupe) {
            return $this->json(['error' => 'Demande non trouvée'], 404);
        }

        $membre->setStatut('ACCEPTED');
        $notification->setRead(true);
        
        // Create notification for the accepted user
        $requester = $membre->getUtilisateur();
        $acceptNotification = new Notification();
        $acceptNotification->setUtilisateur($requester);
        $acceptNotification->setType('join_accepted');
        $acceptNotification->setTitle('Demande acceptée');
        $acceptNotification->setMessage('Votre demande de rejoindre le groupe "' . $groupe->getNom() . '" a été acceptée');
        $acceptNotification->setLink('/chat/groupe/' . $groupe->getId());
        $entityManager->persist($acceptNotification);
        
        $entityManager->flush();

        return $this->json(['success' => true, 'redirect' => '/chat/groupe/' . $groupe->getId()]);
    }

    #[Route('/notifications/{id}/reject', name: 'notification_reject', methods: ['POST'])]
    public function rejectJoinRequest(
        Notification $notification,
        EntityManagerInterface $entityManager,
        MembreGroupeRepository $membreGroupeRepository,
        Request $request
    ): Response {
        $user = $this->getUser();
        
        if (!$user instanceof UserInterface) {
            return $this->json(['error' => 'Non autorisé'], 401);
        }

        if ($notification->getUtilisateur() !== $user) {
            return $this->json(['error' => 'Non autorisé'], 403);
        }

        $membre = $notification->getMembre();
        $groupe = null;
        
        // If no membre associated, try to find it from the link
        if (!$membre instanceof MembreGroupe) {
            $link = $notification->getLink();
            if ($link && preg_match('/\/chat\/groupe\/(\d+)/', $link, $matches)) {
                $groupeId = (int) $matches[1];
                // Find the pending membership request for this group
                $membre = $membreGroupeRepository->findOneBy([
                    'groupe' => $groupeId,
                    'statut' => 'PENDING'
                ]);
                if ($membre) {
                    $groupe = $membre->getGroupe();
                }
            }
        } else {
            $groupe = $membre->getGroupe();
        }
        
        if (!$membre instanceof MembreGroupe) {
            // For old notifications without membre, just mark as read
            $notification->setRead(true);
            $entityManager->flush();
            return $this->json(['success' => true]);
        }

        $membre->setStatut('REJECTED');
        $notification->setRead(true);
        
        // Create notification for the rejected user
        $requester = $membre->getUtilisateur();
        $rejectNotification = new Notification();
        $rejectNotification->setUtilisateur($requester);
        $rejectNotification->setType('join_rejected');
        $rejectNotification->setTitle('Demande refusée');
        if ($groupe instanceof Groupe) {
            $rejectNotification->setMessage('Votre demande de rejoindre le groupe "' . $groupe->getNom() . '" a été refusée');
        } else {
            $rejectNotification->setMessage('Votre demande de rejoindre un groupe a été refusée');
        }
        $entityManager->persist($rejectNotification);
        
        $entityManager->flush();

        return $this->json(['success' => true]);
    }
}
