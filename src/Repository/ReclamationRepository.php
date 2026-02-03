<?php

namespace App\Repository;

use App\Entity\Reclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReclamationRepository extends ServiceEntityRepository
{
        public function searchByFields(string $term): array
        {
            return $this->createQueryBuilder('r')
                ->where('r.nom LIKE :term OR r.prenom LIKE :term OR r.email LIKE :term OR r.titre LIKE :term  OR r.role LIKE :term')
                ->setParameter('term', '%'.$term.'%')
                ->getQuery()
                ->getResult();
        }
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }
    public function countBy(): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
