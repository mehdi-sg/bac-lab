<?php

namespace App\Controller;

use App\Entity\Chapitre;
use App\Form\ChapitreType;
use App\Repository\ChapitreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/chapitre')]
class ChapitreController extends AbstractController
{
    #[Route('/', name: 'chapitre_index', methods: ['GET'])]
    public function index(Request $request, ChapitreRepository $chapitreRepository): Response
    {
        $query = trim((string) $request->query->get('q', ''));
        $chapitres = $query === ''
            ? $chapitreRepository->findAll()
            : $chapitreRepository->search($query);

        return $this->render('chapitre/index.html.twig', [
            'chapitres' => $chapitres,
            'query' => $query,
        ]);
    }

    #[Route('/new', name: 'chapitre_new', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $chapitre = new Chapitre();
        $form = $this->createForm(ChapitreType::class, $chapitre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($chapitre);
            $em->flush();

            return $this->redirectToRoute('chapitre_index');
        }

        return $this->render('chapitre/new.html.twig', [
            'chapitre' => $chapitre,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'chapitre_show', methods: ['GET'])]
    public function show(Chapitre $chapitre): Response
    {
        return $this->render('chapitre/show.html.twig', [
            'chapitre' => $chapitre,
        ]);
    }

    #[Route('/{id}/edit', name: 'chapitre_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Chapitre $chapitre, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ChapitreType::class, $chapitre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('chapitre_index');
        }

        return $this->render('chapitre/edit.html.twig', [
            'chapitre' => $chapitre,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'chapitre_delete', methods: ['POST'])]
    public function delete(Request $request, Chapitre $chapitre, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$chapitre->getId(), $request->request->get('_token'))) {
            $em->remove($chapitre);
            $em->flush();
        }

        return $this->redirectToRoute('chapitre_index');
    }
}
