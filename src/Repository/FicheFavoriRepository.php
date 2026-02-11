<?php

namespace App\Repository;

use App\Entity\FicheFavori;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FicheFavori>
 */
class FicheFavoriRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FicheFavori::class);
    }

    /**
     * Find all favorite fiches for a user
     * @return FicheFavori[]
     */
    public function findByUtilisateur(int $utilisateurId): array
    {
        return $this->createQueryBuilder('ff')
            ->andWhere('ff.utilisateur = :utilisateurId')
            ->setParameter('utilisateurId', $utilisateurId)
            ->orderBy('ff.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Check if a fiche is favorited by a user
     */
    public function isFavorited(int $ficheId, int $utilisateurId): bool
    {
        $count = $this->createQueryBuilder('ff')
            ->select('COUNT(ff.id)')
            ->andWhere('ff.fiche = :ficheId')
            ->andWhere('ff.utilisateur = :utilisateurId')
            ->setParameter('ficheId', $ficheId)
            ->setParameter('utilisateurId', $utilisateurId)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    /**
     * Find a specific favorite entry
     */
    public function findOneByFicheAndUtilisateur(int $ficheId, int $utilisateurId): ?FicheFavori
    {
        return $this->createQueryBuilder('ff')
            ->andWhere('ff.fiche = :ficheId')
            ->andWhere('ff.utilisateur = :utilisateurId')
            ->setParameter('ficheId', $ficheId)
            ->setParameter('utilisateurId', $utilisateurId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
