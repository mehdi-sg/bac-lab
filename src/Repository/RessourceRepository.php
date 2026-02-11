<?php

namespace App\Repository;

use App\Entity\Ressource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ressource>
 */
class RessourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ressource::class);
    }
    

    public function findPublicRessources(?string $q = null, ?int $typeId = null, ?int $limit = null): array
  {
    $qb = $this->createQueryBuilder('r')
        ->andWhere('r.estActive = :active')
        ->andWhere('r.statut = :statut')
        ->setParameter('active', true)
        ->setParameter('statut', 'VALIDEE')
        ->orderBy('r.dateAjout', 'DESC');

    if ($q) {
        $qb->andWhere('LOWER(r.titre) LIKE :q OR LOWER(r.tags) LIKE :q OR LOWER(r.auteur) LIKE :q')
           ->setParameter('q', '%'.mb_strtolower($q).'%');
    }

    // Note: typeId parameter is deprecated since TypeRessource entity was removed
    // Keeping parameter for backward compatibility but not using it
    if ($typeId) {
        // No longer filtering by typeRessource since it was removed
        // Use categorie field instead if needed in the future
    }

    if ($limit) {
        $qb->setMaxResults($limit);
    }

    return $qb->getQuery()->getResult();
 }





    //    /**
    //     * @return Ressource[] Returns an array of Ressource objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Ressource
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
