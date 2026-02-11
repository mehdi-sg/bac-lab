<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * Find unread notifications for a user.
     *
     * @return Notification[]
     */
    public function findUnreadByUser(Utilisateur $user): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.utilisateur = :user')
            ->andWhere('n.isRead = false')
            ->setParameter('user', $user)
            ->orderBy('n.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Count unread notifications for a user.
     */
    public function countUnreadByUser(Utilisateur $user): int
    {
        return $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->andWhere('n.utilisateur = :user')
            ->andWhere('n.isRead = false')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Find recent notifications for a user.
     *
     * @return Notification[]
     */
    public function findRecentByUser(Utilisateur $user, int $limit = 10): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.utilisateur = :user')
            ->setParameter('user', $user)
            ->orderBy('n.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsReadByUser(Utilisateur $user): int
    {
        return $this->createQueryBuilder('n')
            ->update(Notification::class, 'n')
            ->set('n.isRead', 'true')
            ->andWhere('n.utilisateur = :user')
            ->andWhere('n.isRead = false')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
