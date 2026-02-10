<?php

namespace App\Controller;

use App\Entity\Filiere;
use App\Form\FiliereType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/filiere')]
class FiliereController extends AbstractController
{
    // LISTE
    #[Route('/', name: 'filiere_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $repo = $em->getRepository(Filiere::class);
        $query = trim((string) $request->query->get('q', ''));
        $filieres = $query === ''
            ? $repo->findActives()
            : $repo->searchActives($query);

        return $this->render('filiere/index.html.twig', [
            'filieres' => $filieres,
            'query' => $query,
        ]);
    }

    // AJOUT
    #[Route('/new', name: 'filiere_new', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $filiere = new Filiere();
        $form = $this->createForm(FiliereType::class, $filiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filiere->setCreatedAt(new \DateTime());
            $filiere->setActif(true);

            $em->persist($filiere);
            $em->flush();

            return $this->redirectToRoute('filiere_index');
        }

        return $this->render('filiere/form.html.twig', [
            'form' => $form->createView(),
            'edit' => false,
        ]);
    }

    // AFFICHER
    #[Route('/{id}', name: 'filiere_show', methods: ['GET'])]
    public function show(Filiere $filiere): Response
    {
        return $this->render('filiere/show.html.twig', [
            'filiere' => $filiere,
        ]);
    }

    // MODIFIER
    #[Route('/{id}/edit', name: 'filiere_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Filiere $filiere, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(FiliereType::class, $filiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filiere->setUpdatedAt(new \DateTime());
            $em->flush();

            return $this->redirectToRoute('filiere_index');
        }

        return $this->render('filiere/form.html.twig', [
            'form' => $form->createView(),
            'edit' => true,
        ]);
    }

    // SUPPRIMER
    #[Route('/{id}/delete', name: 'filiere_delete', methods: ['POST'])]
    public function delete(Request $request, Filiere $filiere, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$filiere->getId(), $request->request->get('_token'))) {
            $filiere->setActif(false);
            $em->flush();
        }

        return $this->redirectToRoute('filiere_index');
    }
}
