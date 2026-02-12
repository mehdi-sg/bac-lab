<?php

namespace App\Controller;

use App\Entity\Chapitre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/chapitres')]
class ChapitreApiController extends AbstractController
{
    #[Route('/matiere/{id}', name: 'api_chapitres_by_matiere', methods: ['GET'])]
    public function getByMatiere(int $id, EntityManagerInterface $em): JsonResponse
    {
        $chapitres = $em->getRepository(Chapitre::class)->findBy(
            ['matiere' => $id, 'actif' => true],
            ['ordre' => 'ASC']
        );

        $data = [];
        foreach ($chapitres as $chapitre) {
            $data[] = [
                'id' => $chapitre->getId(),
                'titre' => $chapitre->getTitre(),
            ];
        }

        return $this->json($data);
    }
}
