<?php

namespace App\Repository;

use App\Entity\Fiche;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Fiche>
 */
class FicheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fiche::class);
    }

    //    /**
    //     * @return Fiche[] Returns an array of Fiche objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

//    public function findOneBySomeField($value): ?Fiche
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * Find fiches by filiere
     * @return Fiche[]
     */
    public function findByFiliere(int $filiereId): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.filiere = :filiereId')
            ->setParameter('filiereId', $filiereId)
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find fiches where user is owner (via FicheModerateur)
     * @return Fiche[]
     */
    public function findByOwner(int $userId): array
    {
        return $this->createQueryBuilder('f')
            ->innerJoin('f.moderateurs', 'fm')
            ->andWhere('fm.utilisateur = :userId')
            ->andWhere('fm.isOwner = :isOwner')
            ->setParameter('userId', $userId)
            ->setParameter('isOwner', true)
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find fiches where user is moderator (owner or co-moderator)
     * @return Fiche[]
     */
    public function findByModerateur(int $userId): array
    {
        return $this->createQueryBuilder('f')
            ->innerJoin('f.moderateurs', 'fm')
            ->andWhere('fm.utilisateur = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find favorite fiches for a user
     * @return Fiche[]
     */
    public function findFavoritesByUser(int $userId): array
    {
        return $this->createQueryBuilder('f')
            ->innerJoin('f.favoris', 'ff')
            ->andWhere('ff.utilisateur = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('ff.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find fiches with combined filters and pagination
     * 
     * @param array $filters Available filters: 'type' => 'all'|'own'|'favorite', 'filiere' => int|null
     * @param int|null $userId Required for 'own' and 'favorite' filters
     * @param int $page Page number (1-based)
     * @param int $limit Number of items per page
     * @return array{0: array, 1: int} Array with fiches and total count
     */
    public function findByFiltersWithPagination(array $filters = [], ?int $userId = null, int $page = 1, int $limit = 12): array
    {
        $qb = $this->createQueryBuilder('f');
        
        // Apply type filter
        $type = $filters['type'] ?? 'all';
        
        if ($type === 'own' && $userId) {
            // User's own fiches (where they are owner)
            $qb->innerJoin('f.moderateurs', 'fm')
               ->andWhere('fm.utilisateur = :userId')
               ->andWhere('fm.isOwner = :isOwner')
               ->setParameter('userId', $userId)
               ->setParameter('isOwner', true);
        } elseif ($type === 'favorite' && $userId) {
            // User's favorite fiches
            $qb->innerJoin('f.favoris', 'ff')
               ->andWhere('ff.utilisateur = :userId')
               ->setParameter('userId', $userId);
        } elseif ($type === 'moderateur' && $userId) {
            // All fiches where user is moderator (owner or co-moderator)
            $qb->innerJoin('f.moderateurs', 'fm')
               ->andWhere('fm.utilisateur = :userId')
               ->setParameter('userId', $userId);
        }
        
        // Apply filiere filter
        if (!empty($filters['filiere'])) {
            $qb->andWhere('f.filiere = :filiereId')
               ->setParameter('filiereId', (int) $filters['filiere']);
        }
        
        // Get total count before pagination
        $countQuery = clone $qb;
        $totalCount = count($countQuery->getQuery()->getResult());
        
        // Apply pagination
        $qb->orderBy('f.createdAt', 'DESC')
           ->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit);
        
        return [
            $qb->getQuery()->getResult(),
            $totalCount
        ];
    }
}
