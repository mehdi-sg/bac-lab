<?php

namespace App\Controller;

use App\Entity\Groupe;
use App\Entity\Message;
use App\Entity\MembreGroupe;
use App\Repository\GroupeRepository;
use App\Repository\MessageRepository;
use App\Repository\MembreGroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ChatController extends AbstractController
{
    
    #[Route('/chat', name: 'chat_index', methods: ['GET'])]
    public function index(
        Request $request,
        GroupeRepository $groupeRepo,
        MembreGroupeRepository $membreRepo,
        MessageRepository $messageRepo
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $q = trim((string)$request->query->get('q', ''));
        $sort = (string)$request->query->get('sort', 'recent');
        $groupeId = $request->query->getInt('groupeId', 0);

        // Mes groupes (via MembreGroupe)
        $mesGroupes = $groupeRepo->findMesGroupes($user, $q);

        // Groupes publics non rejoints (explorer)
        $groupesPublics = $groupeRepo->findPublicNonRejoints($user, $q);

        $activeGroupe = null;
        $messages = [];

        if ($groupeId > 0) {
            $activeGroupe = $groupeRepo->find($groupeId);
            if ($activeGroupe) {
                // sécurité: membre obligatoire
                $membership = $membreRepo->findOneBy(['groupe' => $activeGroupe, 'utilisateur' => $user]);
                if (!$membership) {
                    $this->addFlash('error', "Tu n'es pas membre de ce groupe.");
                    return $this->redirectToRoute('chat_index');
                }

                $messages = $messageRepo->findMessagesForGroupe($activeGroupe, $sort);
            }
        } elseif (count($mesGroupes) > 0) {
            // ouvrir automatiquement le premier groupe
            $activeGroupe = $mesGroupes[0];
            $messages = $messageRepo->findMessagesForGroupe($activeGroupe, $sort);
        }

        return $this->render('chat/index.html.twig', [
            'q' => $q,
            'sort' => $sort,
            'mesGroupes' => $mesGroupes,
            'groupesPublics' => $groupesPublics,
            'activeGroupe' => $activeGroupe,
            'messages' => $messages,
        ]);
    }

    #[Route('/groupes/create', name: 'groupe_create', methods: ['POST'])]
    public function createGroupe(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$this->isCsrfTokenValid('create_groupe', (string)$request->request->get('_token'))) {
            throw $this->createAccessDeniedException('CSRF invalid');
        }

        $nom = trim((string)$request->request->get('nom'));
        $description = trim((string)$request->request->get('description'));

        $g = new Groupe();
        $g->setNom($nom);
        $g->setDescription($description);
        $g->setCreateur($this->getUser());
        $g->setIsPublic(true);

        // validation server: Assert dans l’entity + validator auto avec Forms normalement.
        // Ici minimal: on flush, et si invalid => exception. Le mieux est d’utiliser FormType (je peux te le donner).
        $em->persist($g);

        // ajouter le createur comme membre (important)
        $m = new MembreGroupe();
        $m->setUtilisateur($this->getUser());
        $m->setGroupe($g);
        $m->setRoleMembre('ADMIN'); // ou CREATEUR si tu ajoutes la valeur
        $em->persist($m);

        $em->flush();

        return $this->redirectToRoute('chat_index', ['groupeId' => $g->getId()]);
    }

    #[Route('/groupes/{id}/join', name: 'groupe_join', methods: ['POST'])]
    public function join(Groupe $groupe, Request $request, MembreGroupeRepository $membreRepo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$this->isCsrfTokenValid('join'.$groupe->getId(), (string)$request->request->get('_token'))) {
            throw $this->createAccessDeniedException('CSRF invalid');
        }

        $user = $this->getUser();
        $existing = $membreRepo->findOneBy(['groupe' => $groupe, 'utilisateur' => $user]);
        if ($existing) {
            return $this->redirectToRoute('chat_index', ['groupeId' => $groupe->getId()]);
        }

        if (!$groupe->isPublic()) {
            $this->addFlash('error', "Groupe privé : accès refusé.");
            return $this->redirectToRoute('chat_index');
        }

        $m = new MembreGroupe();
        $m->setUtilisateur($user);
        $m->setGroupe($groupe);
        $m->setRoleMembre('MEMBRE');

        $em->persist($m);
        $em->flush();

        return $this->redirectToRoute('chat_index', ['groupeId' => $groupe->getId()]);
    }

    #[Route('/groupes/{id}/leave', name: 'groupe_leave', methods: ['POST'])]
    public function leave(Groupe $groupe, Request $request, MembreGroupeRepository $membreRepo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$this->isCsrfTokenValid('leave'.$groupe->getId(), (string)$request->request->get('_token'))) {
            throw $this->createAccessDeniedException('CSRF invalid');
        }

        $user = $this->getUser();
        $membership = $membreRepo->findOneBy(['groupe' => $groupe, 'utilisateur' => $user]);
        if (!$membership) {
            return $this->redirectToRoute('chat_index');
        }

        // empêche le créateur de quitter (simple)
        if ($groupe->getCreateur() && $groupe->getCreateur()->getId() === $user->getId()) {
            $this->addFlash('error', "Le créateur ne peut pas quitter son propre groupe.");
            return $this->redirectToRoute('chat_index', ['groupeId' => $groupe->getId()]);
        }

        $em->remove($membership);
        $em->flush();

        return $this->redirectToRoute('chat_index');
    }

    #[Route('/groupes/{id}/messages/send', name: 'message_send', methods: ['POST'])]
    public function sendMessage(
        Groupe $groupe,
        Request $request,
        MembreGroupeRepository $membreRepo,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$this->isCsrfTokenValid('send'.$groupe->getId(), (string)$request->request->get('_token'))) {
            throw $this->createAccessDeniedException('CSRF invalid');
        }

        $user = $this->getUser();
        $membership = $membreRepo->findOneBy(['groupe' => $groupe, 'utilisateur' => $user]);
        if (!$membership) {
            throw $this->createAccessDeniedException("Not a member");
        }

        $contenu = trim((string)$request->request->get('contenu'));
        if ($contenu === '') {
            $this->addFlash('error', "Message vide.");
            return $this->redirectToRoute('chat_index', ['groupeId' => $groupe->getId()]);
        }

        $msg = new Message();
        $msg->setGroupe($groupe);
        $msg->setExpediteur($user);
        $msg->setContenu($contenu);
        $msg->setTypeMessage('TEXTE');

        $parentId = $request->request->getInt('parent_id', 0);
        if ($parentId > 0) {
            $parent = $em->getRepository(Message::class)->find($parentId);
            if ($parent && $parent->getGroupe()->getId() === $groupe->getId()) {
                $msg->setParentMessage($parent);
            }
        }

        $em->persist($msg);
        $em->flush();

        return $this->redirectToRoute('chat_index', ['groupeId' => $groupe->getId()]);
    }
}
