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
    

    public function findPublicRessources(
<<<<<<< HEAD
        ?string $q = null,
        ?string $filiere = null,
        ?string $matiere = null,
        ?string $typeFichier = null,
=======
        ?string $q = null, 
        ?string $typeFichier = null, 
>>>>>>> 3f3303933bea9b9163379c7e207e7022d3dc6a2f
        ?string $categorie = null,
        ?int $limit = null
    ): array {
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.estActive = :active')
            ->andWhere('r.statut = :statut')
            ->setParameter('active', true)
            ->setParameter('statut', 'VALIDEE')
            ->orderBy('r.dateAjout', 'DESC');

<<<<<<< HEAD
        // Recherche par texte
=======
        // Search query (titre, tags, auteur, description)
>>>>>>> 3f3303933bea9b9163379c7e207e7022d3dc6a2f
        if ($q) {
            $qb->andWhere('LOWER(r.titre) LIKE :q OR LOWER(r.tags) LIKE :q OR LOWER(r.auteur) LIKE :q OR LOWER(r.description) LIKE :q')
               ->setParameter('q', '%'.mb_strtolower($q).'%');
        }

<<<<<<< HEAD
        // Filtre par filière
        if ($filiere) {
            $qb->andWhere('LOWER(r.filiere) = :filiere')
               ->setParameter('filiere', mb_strtolower($filiere));
        }

        // Filtre par matière
        if ($matiere) {
            $qb->andWhere('LOWER(r.matiere) = :matiere')
               ->setParameter('matiere', mb_strtolower($matiere));
        }

        // Filtre par type de fichier
=======
        // Filter by type (PDF, VIDEO, LIEN)
>>>>>>> 3f3303933bea9b9163379c7e207e7022d3dc6a2f
        if ($typeFichier) {
            $qb->andWhere('r.typeFichier = :typeFichier')
               ->setParameter('typeFichier', $typeFichier);
        }

<<<<<<< HEAD
        // Filtre par catégorie
=======
        // Filter by category (Cours, Exercices, etc.)
>>>>>>> 3f3303933bea9b9163379c7e207e7022d3dc6a2f
        if ($categorie) {
            $qb->andWhere('r.categorie = :categorie')
               ->setParameter('categorie', $categorie);
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
