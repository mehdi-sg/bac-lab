<?php

namespace App\Repository;

use App\Entity\FicheModerateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FicheModerateur>
 */
class FicheModerateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FicheModerateur::class);
    }

    /**
     * Find all moderateurs for a fiche
     */
    public function findByFiche(int $ficheId): array
    {
        return $this->createQueryBuilder('fm')
            ->andWhere('fm.fiche = :ficheId')
            ->setParameter('ficheId', $ficheId)
            ->orderBy('fm.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find moderateur by fiche and utilisateur
     */
    public function findByFicheAndUtilisateur(int $ficheId, int $utilisateurId): ?FicheModerateur
    {
        return $this->createQueryBuilder('fm')
            ->andWhere('fm.fiche = :ficheId')
            ->andWhere('fm.utilisateur = :utilisateurId')
            ->setParameter('ficheId', $ficheId)
            ->setParameter('utilisateurId', $utilisateurId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Check if user is moderateur of fiche
     */
    public function isModerateur(int $ficheId, int $utilisateurId): bool
    {
        return $this->createQueryBuilder('fm')
            ->select('COUNT(fm.id)')
            ->andWhere('fm.fiche = :ficheId')
            ->andWhere('fm.utilisateur = :utilisateurId')
            ->setParameter('ficheId', $ficheId)
            ->setParameter('utilisateurId', $utilisateurId)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    /**
     * Find all fiches where user is moderateur
     */
    public function findByUtilisateur(int $utilisateurId): array
    {
        return $this->createQueryBuilder('fm')
            ->andWhere('fm.utilisateur = :utilisateurId')
            ->setParameter('utilisateurId', $utilisateurId)
            ->orderBy('fm.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all owners of a fiche
     */
    public function findOwnersByFiche(int $ficheId): array
    {
        return $this->createQueryBuilder('fm')
            ->andWhere('fm.fiche = :ficheId')
            ->andWhere('fm.isOwner = :isOwner')
            ->setParameter('ficheId', $ficheId)
            ->setParameter('isOwner', true)
            ->getQuery()
            ->getResult();
    }
}
