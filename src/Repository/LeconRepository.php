<?php

namespace App\Repository;

use App\Entity\Lecon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lecon>
 */
class LeconRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lecon::class);
    }

    /**
     * @return Lecon[]
     */
    public function findByCours(int $coursId): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.cours = :coursId')
            ->setParameter('coursId', $coursId)
            ->orderBy('l.ordre', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
