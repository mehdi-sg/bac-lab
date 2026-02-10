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
}
