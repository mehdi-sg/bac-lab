<?php

namespace App\Repository;

use App\Entity\Filiere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Filiere>
 *
 * @method Filiere|null find($id, $lockMode = null, $lockVersion = null)
 * @method Filiere|null findOneBy(array $criteria, array $orderBy = null)
 * @method Filiere[]    findAll()
 * @method Filiere[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FiliereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Filiere::class);
    }

    // Méthode personnalisée pour récupérer les filières actives
    public function findActives(): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.actif = :val')
            ->setParameter('val', true)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Filiere[]
     */
    public function searchActives(string $query): array
    {
        $query = trim($query);
        if ($query === '') {
            return $this->findActives();
        }

        return $this->createQueryBuilder('f')
            ->andWhere('f.actif = :val')
            ->andWhere('f.nom LIKE :q OR f.niveau LIKE :q')
            ->setParameter('val', true)
            ->setParameter('q', '%' . $query . '%')
            ->orderBy('f.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
