<?php

namespace App\Controller;

use App\Entity\Filiere;
use App\Entity\Matiere;
use App\Form\MatiereType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/matiere')]
class MatiereController extends AbstractController
{
    // LISTE
    #[Route('/', name: 'matiere_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $repo = $em->getRepository(Matiere::class);
        $filiereRepo = $em->getRepository(Filiere::class);

        $selectedFiliere = $request->query->get('filiere');
        $selectedNiveau = $request->query->get('niveau');

        $qb = $em->createQueryBuilder()
            ->select('m', 'f')
            ->from(Matiere::class, 'm')
            ->leftJoin('m.filiere', 'f')
            ->andWhere('m.actif = :actif')
            ->setParameter('actif', true);

        if ($selectedFiliere !== null && $selectedFiliere !== '') {
            $qb->andWhere('f.id = :filiereId')
                ->setParameter('filiereId', (int) $selectedFiliere);
        }

        if ($selectedNiveau !== null && $selectedNiveau !== '') {
            $qb->andWhere('f.niveau = :niveau')
                ->setParameter('niveau', $selectedNiveau);
        }

        $matieres = $qb->getQuery()->getResult();

        $filieres = $filiereRepo->findActives();
        $niveaux = array_values(array_unique(array_map(
            static fn(Filiere $f): ?string => $f->getNiveau(),
            $filieres
        )));
        sort($niveaux);

        return $this->render('matiere/index.html.twig', [
            'matieres' => $matieres,
            'filieres' => $filieres,
            'niveaux' => $niveaux,
            'selectedFiliere' => $selectedFiliere,
            'selectedNiveau' => $selectedNiveau,
        ]);
    }

    // AJOUT
    #[Route('/new', name: 'matiere_new', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $matiere = new Matiere();
        $form = $this->createForm(MatiereType::class, $matiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $matiere->setCreatedAt(new \DateTime());
            $matiere->setActif(true);

            $em->persist($matiere);
            $em->flush();

            return $this->redirectToRoute('matiere_index');
        }

        return $this->render('matiere/form.html.twig', [
            'form' => $form->createView(),
            'edit' => false,
        ]);
    }

    // AFFICHER
    #[Route('/{id}', name: 'matiere_show', methods: ['GET'])]
    public function show(Matiere $matiere): Response
    {
        return $this->render('matiere/show.html.twig', [
            'matiere' => $matiere,
        ]);
    }

    // MODIFIER
    #[Route('/{id}/edit', name: 'matiere_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Matiere $matiere, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(MatiereType::class, $matiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $matiere->setUpdatedAt(new \DateTime());
            $em->flush();

            return $this->redirectToRoute('matiere_index');
        }

        return $this->render('matiere/form.html.twig', [
            'form' => $form->createView(),
            'edit' => true,
        ]);
    }

    // SUPPRIMER
    #[Route('/{id}/delete', name: 'matiere_delete', methods: ['POST'])]
    public function delete(Request $request, Matiere $matiere, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$matiere->getId(), $request->request->get('_token'))) {
            $matiere->setActif(false);
            $em->flush();
        }

        return $this->redirectToRoute('matiere_index');
    }
}
