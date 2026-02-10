<?php

namespace App\Repository;

use App\Entity\Groupe;
use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Récupère les messages d'un groupe avec tri (feature avancée).
     *
     * @param string $sort  "recent" | "old" | "pdf"
     * @param int    $limit nombre max de messages
     *
     * @return Message[]
     */
    public function findMessagesForGroupe(Groupe $groupe, string $sort = 'recent', int $limit = 50): array
    {
        $qb = $this->createQueryBuilder('m')
            ->andWhere('m.groupe = :g')
            ->andWhere('m.deletedAt IS NULL')
            ->setParameter('g', $groupe)
            ->setMaxResults($limit);

        if ($sort === 'old') {
            $qb->orderBy('m.createdAt', 'ASC');
        } elseif ($sort === 'pdf') {
            $qb->andWhere('m.typeMessage = :t')
               ->setParameter('t', 'PDF')
               ->orderBy('m.createdAt', 'ASC');
        } else {
            // recent - show oldest first for proper chat display
            $qb->orderBy('m.createdAt', 'ASC');
        }

        return $qb->getQuery()->getResult();
    }
}
