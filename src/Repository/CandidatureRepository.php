<?php

namespace App\Repository;

use App\Entity\Candidature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Candidature>
 */
class CandidatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Candidature::class);
    }

    /** @return Candidature[] */
    public function findLatest(int $limit = 20): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.dateCandidature', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
