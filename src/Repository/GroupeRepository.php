<?php

namespace App\Repository;

use App\Entity\Groupe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Groupe>
 */
class GroupeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Groupe::class);
    }
public function findMesGroupes($user, string $q = ''): array
{
    $qb = $this->createQueryBuilder('g')
        ->join('g.membres', 'm')
        ->andWhere('m.utilisateur = :u')
        ->setParameter('u', $user)
        ->orderBy('g.id', 'DESC');

    if ($q !== '') {
        $qb->andWhere('LOWER(g.nom) LIKE :q OR LOWER(g.description) LIKE :q')
           ->setParameter('q', '%'.mb_strtolower($q).'%');
    }

    return $qb->getQuery()->getResult();
}

public function findPublicNonRejoints($user, string $q = ''): array
{
    $qb = $this->createQueryBuilder('g')
        ->andWhere('g.isPublic = true')
        ->orderBy('g.id', 'DESC');

    // exclude mes groupes
    $qb->andWhere('g.id NOT IN (
        SELECT g2.id FROM App\Entity\Groupe g2
        JOIN g2.membres m2
        WHERE m2.utilisateur = :u
    )')->setParameter('u', $user);

    if ($q !== '') {
        $qb->andWhere('LOWER(g.nom) LIKE :q OR LOWER(g.description) LIKE :q')
           ->setParameter('q', '%'.mb_strtolower($q).'%');
    }

    return $qb->getQuery()->getResult();
}

    //    /**
    //     * @return Groupe[] Returns an array of Groupe objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('g.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Groupe
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
