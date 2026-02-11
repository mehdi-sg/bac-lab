<?php

namespace App\Controller;

use App\Entity\Profil;
use App\Entity\Utilisateur;
use App\Form\AdminUtilisateurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/users')]
#[IsGranted('ROLE_ADMIN')]
class AdminUserController extends AbstractController
{
    #[Route('/', name: 'admin_user_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $q = trim((string) $request->query->get('q', ''));
        $status = (string) $request->query->get('status', ''); // active|inactive|''
        $role = (string) $request->query->get('role', '');     // ROLE_ADMIN|ROLE_MODERATOR|ROLE_USER|''

        $qb = $em->getRepository(\App\Entity\Utilisateur::class)->createQueryBuilder('u');

        if ($q !== '') {
            $qb->andWhere('LOWER(u.email) LIKE :q')
               ->setParameter('q', '%'.mb_strtolower($q).'%');
        }

        if ($status === 'active') {
            $qb->andWhere('u.isActive = 1');
        } elseif ($status === 'inactive') {
            $qb->andWhere('u.isActive = 0');
        }

        if ($role !== '') {
            $qb->andWhere('u.roles LIKE :r')
               ->setParameter('r', '%"'.$role.'"%');
        }

        $users = $qb->orderBy('u.email', 'ASC')->getQuery()->getResult();

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'q' => $q,
            'status' => $status,
            'role' => $role,
        ]);
    }

    #[Route('/new', name: 'admin_user_new')]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): Response {
        $user = new Utilisateur();
        $user->setIsActive(true);
        $user->setRoles(['ROLE_USER']);

        // Create a new Profil and set it on the user
        $profil = new Profil();
        $profil->setUtilisateur($user);
        $user->setProfil($profil);

        $form = $this->createForm(AdminUtilisateurType::class, $user, [
            'is_edit' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plain = $form->get('plainPassword')->getData();
            $user->setPassword($hasher->hashPassword($user, $plain));

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Utilisateur ajouté avec succès.');
            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_user_edit')]
    public function edit(
        Utilisateur $user,
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): Response {
        $form = $this->createForm(AdminUtilisateurType::class, $user, [
            'is_edit' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plain = $form->get('plainPassword')->getData();
            if ($plain) {
                $user->setPassword($hasher->hashPassword($user, $plain));
            }

            $em->flush();
            $this->addFlash('success', 'Utilisateur modifié.');
            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/{id}/toggle', name: 'admin_user_toggle')]
    public function toggle(Utilisateur $user, EntityManagerInterface $em): Response
    {
        $user->setIsActive(!$user->isActive());
        $em->flush();

        return $this->redirectToRoute('admin_user_index');
    }

    #[Route('/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
    public function delete(
        Utilisateur $user,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if (!$this->isCsrfTokenValid('delete_user_'.$user->getId(), $request->request->get('_token'))) {
            return $this->redirectToRoute('admin_user_index');
        }

        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('admin_user_index');
    }
}
