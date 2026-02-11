<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminSearchController extends AbstractController
{
    #[Route('/search', name: 'admin_search', methods: ['GET'])]
    public function search(Request $request, EntityManagerInterface $em): Response
    {
        $q = trim((string) $request->query->get('q', ''));

        $users = [];
        // tu peux ajouter d'autres résultats après (matières/ressources/quiz...)
        if ($q !== '') {
            $users = $em->getRepository(Utilisateur::class)
                ->createQueryBuilder('u')
                ->andWhere('LOWER(u.email) LIKE :q')
                ->setParameter('q', '%'.mb_strtolower($q).'%')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();
        }

        return $this->render('admin/admin_search/index.html.twig', [
            'q' => $q,
            'users' => $users,

            // placeholders pour modules futurs
            'matieres' => [],
            'ressources' => [],
            'quiz' => [],
        ]);
    }
}
