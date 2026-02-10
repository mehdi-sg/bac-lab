<?php

namespace App\Repository;

use App\Entity\Chapitre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ChapitreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chapitre::class);
    }

    /**
     * @return Chapitre[]
     */
    public function search(string $query): array
    {
        $query = trim($query);
        if ($query === '') {
            return $this->findAll();
        }

        return $this->createQueryBuilder('c')
            ->leftJoin('c.matiere', 'm')
            ->addSelect('m')
            ->andWhere('c.titre LIKE :q OR c.contenu LIKE :q OR m.nom LIKE :q')
            ->setParameter('q', '%' . $query . '%')
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
