<?php

namespace App\Repository;

use App\Entity\MembreGroupe;
use App\Entity\Utilisateur;
use App\Entity\Groupe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MembreGroupe>
 */
class MembreGroupeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MembreGroupe::class);
    }

    /**
     * Find a member by user and group
     */
    public function findByUtilisateurAndGroupe(Utilisateur $utilisateur, Groupe $groupe): ?MembreGroupe
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.utilisateur = :utilisateur')
            ->andWhere('m.groupe = :groupe')
            ->setParameter('utilisateur', $utilisateur)
            ->setParameter('groupe', $groupe)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find all members of a group
     */
    public function findByGroupe(Groupe $groupe): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.groupe = :groupe')
            ->setParameter('groupe', $groupe)
            ->orderBy('m.dateJoint', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all groups a user is member of
     */
    public function findByUtilisateur(Utilisateur $utilisateur): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.utilisateur = :utilisateur')
            ->setParameter('utilisateur', $utilisateur)
            ->orderBy('m.dateJoint', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Check if user is member of a group
     */
    public function isMember(Utilisateur $utilisateur, Groupe $groupe): bool
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->andWhere('m.utilisateur = :utilisateur')
            ->andWhere('m.groupe = :groupe')
            ->setParameter('utilisateur', $utilisateur)
            ->setParameter('groupe', $groupe)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    /**
     * Count members in a group
     */
    public function countByGroupe(Groupe $groupe): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->andWhere('m.groupe = :groupe')
            ->setParameter('groupe', $groupe)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Find admins of a group
     */
    public function findAdminsByGroupe(Groupe $groupe): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.groupe = :groupe')
            ->andWhere('m.roleMembre IN (:roles)')
            ->setParameter('groupe', $groupe)
            ->setParameter('roles', ['ADMIN', 'ANIMATEUR'])
            ->orderBy('m.dateJoint', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
