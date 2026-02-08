<?php

namespace App\Repository;

use App\Entity\LogAuthentification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogAuthentification>
 */
class LogAuthentificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogAuthentification::class);
    }

    /**
     * @return LogAuthentification[]
     */
    public function findRecentByUser(int $utilisateurId, int $limit = 10): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.utilisateur = :userId')
            ->setParameter('userId', $utilisateurId)
            ->orderBy('l.dateConnexion', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
