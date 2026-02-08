<?php

namespace App\Repository;

use App\Entity\Inscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Inscription>
 */
class InscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inscription::class);
    }

    /**
     * @return Inscription[]
     */
    public function findByUtilisateur(int $utilisateurId): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.utilisateur = :userId')
            ->setParameter('userId', $utilisateurId)
            ->orderBy('i.dateInscription', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Inscription[]
     */
    public function findByCours(int $coursId): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.cours = :coursId')
            ->setParameter('coursId', $coursId)
            ->orderBy('i.dateInscription', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
