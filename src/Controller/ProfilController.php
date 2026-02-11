<?php

namespace App\Controller;

use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profile')]
#[IsGranted('ROLE_USER')]
class ProfilController extends AbstractController
{
    #[Route('/', name: 'app_profile')]
    public function index(): Response
    {
        $user = $this->getUser();
        $profil = $user->getProfil();

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'profil' => $profil,
        ]);
    }

    #[Route('/edit', name: 'app_profile_edit')]
    public function edit(
        Request $request,
        EntityManagerInterface $em,
        TokenGeneratorInterface $tokenGen
    ): Response {
        $user = $this->getUser();
        $profil = $user->getProfil();

        if (!$profil) {
            throw $this->createNotFoundException('Profil non trouvé');
        }

        $form = $this->createForm(ProfilType::class, $profil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Votre profil a été mis à jour avec succès !');
            return $this->redirectToRoute('app_profile');
        }

        // ✅ Tokens "signés" (aléatoires) stockés en session pour l’étape de confirmation
        $disableToken = $tokenGen->generateToken();
        $deleteToken  = $tokenGen->generateToken();

        $request->getSession()->set('profile_disable_token', $disableToken);
        $request->getSession()->set('profile_delete_token', $deleteToken);

        return $this->render('profile/edit.html.twig', [
            'form' => $form,
            'profil' => $profil,
            'disableToken' => $disableToken,
            'deleteToken' => $deleteToken,
        ]);
    }

    // ===========================
    //  Désactivation (2 étapes)
    // ===========================

    #[Route('/disable/confirm/{token}', name: 'app_profile_disable_confirm', methods: ['GET', 'POST'])]
    public function disableConfirm(
        string $token,
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        LoggerInterface $logger,
        TokenStorageInterface $tokenStorage
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        // ✅ Vérif token session
        $expected = $request->getSession()->get('profile_disable_token');
        if (!$expected || !hash_equals($expected, $token)) {
            throw $this->createAccessDeniedException('Lien invalide ou expiré.');
        }

        if ($request->isMethod('POST')) {
            // CSRF
            if (!$this->isCsrfTokenValid('disable_account', $request->request->get('_token'))) {
                $this->addFlash('danger', 'Token invalide.');
                return $this->redirectToRoute('app_profile_disable_confirm', ['token' => $token]);
            }

            // Mot de passe
            $plainPassword = (string) $request->request->get('password');
            if ($plainPassword === '' || !$hasher->isPasswordValid($user, $plainPassword)) {
                $this->addFlash('danger', 'Mot de passe incorrect.');
                return $this->redirectToRoute('app_profile_disable_confirm', ['token' => $token]);
            }

            // Action : désactiver
            if (method_exists($user, 'setIsActive')) {
                $user->setIsActive(false);
                $em->flush();
            }

            // Log
            $logger->warning('User self-disabled account', [
                'user_id' => method_exists($user, 'getId') ? $user->getId() : null,
                'email' => method_exists($user, 'getEmail') ? $user->getEmail() : null,
                'ip' => $request->getClientIp(),
            ]);

            // ✅ Très important : retirer l'utilisateur du contexte de sécurité
            $tokenStorage->setToken(null);

            // nettoyer token + session (logout)
            $request->getSession()->remove('profile_disable_token');
            $request->getSession()->invalidate();

            $this->addFlash('success', 'Votre compte a été désactivé.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('profile/confirm_disable.html.twig', [
            'token' => $token,
        ]);
    }


    #[Route('/delete/confirm/{token}', name: 'app_profile_delete_confirm', methods: ['GET', 'POST'])]
    public function deleteConfirm(
        string $token,
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        LoggerInterface $logger,
        TokenStorageInterface $tokenStorage
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        // ✅ Vérif token session
        $expected = $request->getSession()->get('profile_delete_token');
        if (!$expected || !hash_equals($expected, $token)) {
            throw $this->createAccessDeniedException('Lien invalide ou expiré.');
        }

        if ($request->isMethod('POST')) {
            // CSRF
            if (!$this->isCsrfTokenValid('delete_account', $request->request->get('_token'))) {
                $this->addFlash('danger', 'Token invalide.');
                return $this->redirectToRoute('app_profile_delete_confirm', ['token' => $token]);
            }

            // Mot de passe
            $plainPassword = (string) $request->request->get('password');
            if ($plainPassword === '' || !$hasher->isPasswordValid($user, $plainPassword)) {
                $this->addFlash('danger', 'Mot de passe incorrect.');
                return $this->redirectToRoute('app_profile_delete_confirm', ['token' => $token]);
            }

            // Log AVANT suppression
            $logger->warning('User self-deleted account', [
                'user_id' => method_exists($user, 'getId') ? $user->getId() : null,
                'email' => method_exists($user, 'getEmail') ? $user->getEmail() : null,
                'ip' => $request->getClientIp(),
            ]);

            // ✅ Très important : retirer l'utilisateur du contexte de sécurité
            // sinon Twig base.html.twig essaie encore de refresh l'user supprimé => 500
            $tokenStorage->setToken(null);

            // nettoyer token + session (logout)
            $request->getSession()->remove('profile_delete_token');
            $request->getSession()->invalidate();

            // Suppression
            $em->remove($user);
            $em->flush();

            $this->addFlash('success', 'Votre compte a été supprimé.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('profile/confirm_delete.html.twig', [
            'token' => $token,
        ]);
    }
}
