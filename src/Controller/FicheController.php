<?php

namespace App\Controller;

use App\Entity\Fiche;
use App\Entity\FicheVersion;
use App\Form\FicheType;
use App\Repository\FicheRepository;
use App\Repository\FicheVersionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/fiche')]
class FicheController extends AbstractController
{
    // =============================
    // INDEX — Dashboard
    // =============================
    #[Route('/', name: 'fiche_index', methods: ['GET'])]
    public function index(FicheRepository $ficheRepository): Response
    {
        return $this->render('fiche/index.html.twig', [
            'fiches' => $ficheRepository->findAll(),
        ]);
    }

    // =============================
    // CREATE — New fiche
    // =============================
    #[Route('/new', name: 'fiche_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $fiche = new Fiche();
        $fiche->setCreatedAt(new \DateTimeImmutable());

        $form = $this->createForm(FicheType::class, $fiche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($fiche);
            $em->flush();

            return $this->redirectToRoute('fiche_index');
        }

        return $this->render('fiche/new.html.twig', [
            'form' => $form,
        ]);
    }

    // =============================
    // SHOW — Reading mode
    // =============================
    #[Route('/{id}', name: 'fiche_show', methods: ['GET'])]
    public function show(Fiche $fiche): Response
    {
        return $this->render('fiche/show.html.twig', [
            'fiche' => $fiche,
        ]);
    }

    // =============================
    // EDIT — Co-edition + versioning
    // =============================
    #[Route('/{id}/edit', name: 'fiche_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Fiche $fiche,
        EntityManagerInterface $em
    ): Response {
        // Save old content BEFORE edit
        $oldContent = $fiche->getContent();

        $form = $this->createForm(FicheType::class, $fiche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Create fiche version (history)
            $version = new FicheVersion();
            $version->setContent($oldContent);
            $version->setEditedAt(new \DateTimeImmutable());
            $version->setEditorName('Utilisateur'); // later: real user
            $version->setFiche($fiche);

            $em->persist($version);
            $em->flush();

            return $this->redirectToRoute('fiche_show', [
                'id' => $fiche->getId()
            ]);
        }

        return $this->render('fiche/edit.html.twig', [
            'form' => $form,
            'fiche' => $fiche,
        ]);
    }

    // =============================
    // HISTORY — Version timeline
    // =============================
    #[Route('/{id}/history', name: 'fiche_history', methods: ['GET'])]
    public function history(
        Fiche $fiche,
        FicheVersionRepository $ficheVersionRepository
    ): Response {
        return $this->render('fiche/history.html.twig', [
            'fiche' => $fiche,
            'versions' => $ficheVersionRepository->findBy(
                ['fiche' => $fiche],
                ['editedAt' => 'DESC']
            ),
        ]);
    }
    #[Route('/fiche/{id}/delete', name: 'fiche_delete', methods: ['POST'])]
public function delete(
    Request $request,
    Fiche $fiche,
    EntityManagerInterface $em
): Response {
    if ($this->isCsrfTokenValid('delete_fiche_'.$fiche->getId(), $request->request->get('_token'))) {
        $em->remove($fiche);
        $em->flush();
    }

    return $this->redirectToRoute('fiche_index');
}

}
