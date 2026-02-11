<?php

namespace App\Controller\Admin;

use App\Entity\Ressource;
use App\Form\RessourceType;
use App\Repository\RessourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/ressource')]
#[IsGranted('ROLE_ADMIN')]
final class AdminRessourceController extends AbstractController
{
    #[Route('', name: 'app_admin_ressource_index', methods: ['GET'])]
    public function index(RessourceRepository $ressourceRepository): Response
    {
        // Admin sees ALL resources
        $ressources = $ressourceRepository->findBy([], ['dateAjout' => 'DESC']);
        
        // Calculate statistics
        $stats = [
            'total' => count($ressources),
            'en_attente' => count(array_filter($ressources, fn($r) => $r->getStatut() === 'EN_ATTENTE')),
            'validees' => count(array_filter($ressources, fn($r) => $r->getStatut() === 'VALIDEE')),
            'rejetees' => count(array_filter($ressources, fn($r) => $r->getStatut() === 'REJETEE')),
        ];

        return $this->render('admin/ressource/index.html.twig', [
            'ressources' => $ressources,
            'stats' => $stats,
        ]);
    }

    #[Route('/new', name: 'app_admin_ressource_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ressource = new Ressource();
        $form = $this->createForm(RessourceType::class, $ressource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Admin resources are auto-validated
            $ressource->setStatut('VALIDEE');
            
            $entityManager->persist($ressource);
            $entityManager->flush();

            $this->addFlash('success', 'Ressource créée avec succès.');
            return $this->redirectToRoute('app_admin_ressource_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/ressource/new.html.twig', [
            'ressource' => $ressource,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_ressource_show', methods: ['GET'])]
    public function show(Ressource $ressource): Response
    {
        return $this->render('admin/ressource/show.html.twig', [
            'ressource' => $ressource,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_ressource_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ressource $ressource, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RessourceType::class, $ressource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Ressource modifiée avec succès.');
            return $this->redirectToRoute('app_admin_ressource_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/ressource/edit.html.twig', [
            'ressource' => $ressource,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_ressource_delete', methods: ['POST'])]
    public function delete(Request $request, Ressource $ressource, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ressource->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ressource);
            $entityManager->flush();
            
            $this->addFlash('success', 'Ressource supprimée avec succès.');
        }

        return $this->redirectToRoute('app_admin_ressource_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/validate', name: 'app_admin_ressource_validate', methods: ['POST'])]
    public function validate(Request $request, Ressource $ressource, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('validate'.$ressource->getId(), $request->getPayload()->getString('_token'))) {
            $ressource->setStatut('VALIDEE');
            $entityManager->flush();
            
            $this->addFlash('success', 'Ressource validée avec succès.');
        }

        return $this->redirectToRoute('app_admin_ressource_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/reject', name: 'app_admin_ressource_reject', methods: ['POST'])]
    public function reject(Request $request, Ressource $ressource, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('reject'.$ressource->getId(), $request->getPayload()->getString('_token'))) {
            $ressource->setStatut('REJETEE');
            $entityManager->flush();
            
            $this->addFlash('warning', 'Ressource rejetée.');
        }

        return $this->redirectToRoute('app_admin_ressource_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/toggle-status', name: 'app_admin_ressource_toggle_status', methods: ['POST'])]
    public function toggleStatus(Request $request, Ressource $ressource, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('toggle'.$ressource->getId(), $request->getPayload()->getString('_token'))) {
            $ressource->setEstActive(!$ressource->isEstActive());
            $entityManager->flush();
            
            $status = $ressource->isEstActive() ? 'activée' : 'désactivée';
            $this->addFlash('info', "Ressource {$status}.");
        }

        return $this->redirectToRoute('app_admin_ressource_index', [], Response::HTTP_SEE_OTHER);
    }
}
