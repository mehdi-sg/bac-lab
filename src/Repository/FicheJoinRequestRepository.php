<?php

namespace App\Repository;

use App\Entity\FicheJoinRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FicheJoinRequest>
 */
class FicheJoinRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FicheJoinRequest::class);
    }

    /**
     * Find all pending requests for a fiche
     */
    public function findPendingByFiche(int $ficheId): array
    {
        return $this->createQueryBuilder('fjr')
            ->andWhere('fjr.fiche = :ficheId')
            ->andWhere('fjr.status = :status')
            ->setParameter('ficheId', $ficheId)
            ->setParameter('status', FicheJoinRequest::STATUS_PENDING)
            ->orderBy('fjr.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all requests for a fiche
     */
    public function findByFiche(int $ficheId): array
    {
        return $this->createQueryBuilder('fjr')
            ->andWhere('fjr.fiche = :ficheId')
            ->setParameter('ficheId', $ficheId)
            ->orderBy('fjr.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find request by fiche and utilisateur
     */
    public function findByFicheAndUtilisateur(int $ficheId, int $utilisateurId): ?FicheJoinRequest
    {
        return $this->createQueryBuilder('fjr')
            ->andWhere('fjr.fiche = :ficheId')
            ->andWhere('fjr.utilisateur = :utilisateurId')
            ->setParameter('ficheId', $ficheId)
            ->setParameter('utilisateurId', $utilisateurId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Check if user has pending request for fiche
     */
    public function hasPendingRequest(int $ficheId, int $utilisateurId): bool
    {
        return $this->createQueryBuilder('fjr')
            ->select('COUNT(fjr.id)')
            ->andWhere('fjr.fiche = :ficheId')
            ->andWhere('fjr.utilisateur = :utilisateurId')
            ->andWhere('fjr.status = :status')
            ->setParameter('ficheId', $ficheId)
            ->setParameter('utilisateurId', $utilisateurId)
            ->setParameter('status', FicheJoinRequest::STATUS_PENDING)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    /**
     * Find all pending requests for user
     */
    public function findPendingByUtilisateur(int $utilisateurId): array
    {
        return $this->createQueryBuilder('fjr')
            ->andWhere('fjr.utilisateur = :utilisateurId')
            ->andWhere('fjr.status = :status')
            ->setParameter('utilisateurId', $utilisateurId)
            ->setParameter('status', FicheJoinRequest::STATUS_PENDING)
            ->orderBy('fjr.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
