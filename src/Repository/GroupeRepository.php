<?php

namespace App\Repository;

use App\Entity\Groupe;
use App\Entity\Utilisateur;
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

    /**
     * Mes groupes (groupes où l'utilisateur est membre), avec recherche optionnelle.
     *
     * @return Groupe[]
     */
    public function findMesGroupes(Utilisateur $user, string $q = ''): array
    {
        $qb = $this->createQueryBuilder('g')
            ->innerJoin('g.membres', 'm')
            ->andWhere('m.utilisateur = :u')
            ->setParameter('u', $user)
            ->orderBy('g.id', 'DESC');

        $q = trim($q);
        if ($q !== '') {
            $qb->andWhere('LOWER(g.nom) LIKE :q OR LOWER(g.description) LIKE :q')
               ->setParameter('q', '%' . mb_strtolower($q) . '%');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Groupes publics que l'utilisateur n'a pas encore rejoints, avec recherche optionnelle.
     *
     * @return Groupe[]
     */
    public function findPublicNonRejoints(Utilisateur $user, string $q = ''): array
    {
        $qb = $this->createQueryBuilder('g')
            // left join uniquement sur l'utilisateur courant
            ->leftJoin('g.membres', 'm', 'WITH', 'm.utilisateur = :u')
            ->andWhere('g.isPublic = true')
            // si m est NULL => l'utilisateur n'est pas membre de ce groupe
            ->andWhere('m.id IS NULL')
            ->setParameter('u', $user)
            ->orderBy('g.id', 'DESC');

        $q = trim($q);
        if ($q !== '') {
            $qb->andWhere('LOWER(g.nom) LIKE :q OR LOWER(g.description) LIKE :q')
               ->setParameter('q', '%' . mb_strtolower($q) . '%');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Groupes rejoints par l'utilisateur (tout statut).
     *
     * @return Groupe[]
     */
    public function findJoinedGroupsByUser(Utilisateur $user): array
    {
        return $this->createQueryBuilder('g')
            ->innerJoin('g.membres', 'm')
            ->andWhere('m.utilisateur = :u')
            ->setParameter('u', $user)
            ->orderBy('g.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Tous les groupes (pour le sidebar - inclut groupes publics + groupes rejoints).
     *
     * @return Groupe[]
     */
    public function findAllForSidebar(Utilisateur $user): array
    {
        // Groupes publics
        $publicGroups = $this->createQueryBuilder('g')
            ->andWhere('g.isPublic = true')
            ->orderBy('g.nom', 'ASC')
            ->getQuery()
            ->getResult();

        // Groupes privés rejoints par l'utilisateur
        $joinedGroups = $this->createQueryBuilder('g')
            ->innerJoin('g.membres', 'm')
            ->andWhere('m.utilisateur = :u')
            ->andWhere('g.isPublic = false')
            ->setParameter('u', $user)
            ->orderBy('g.nom', 'ASC')
            ->getQuery()
            ->getResult();

        // Fusionner en évitant les doublons
        $allGroups = [];
        $seenIds = [];

        foreach ($publicGroups as $group) {
            if (!in_array($group->getId(), $seenIds)) {
                $allGroups[] = $group;
                $seenIds[] = $group->getId();
            }
        }

        foreach ($joinedGroups as $group) {
            if (!in_array($group->getId(), $seenIds)) {
                $allGroups[] = $group;
                $seenIds[] = $group->getId();
            }
        }

        return $allGroups;
    }
}
