<?php

namespace App\Repository;

use App\Entity\OffreStage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OffreStage>
 */
class OffreStagERepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OffreStage::class);
    }

    public function findAllOrderedByDate()
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.dateDebut', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function searchByTitre(?string $search = null)
    {
        $query = $this->createQueryBuilder('o');

        if ($search) {
            $query->andWhere('o.titre LIKE :search')
                  ->setParameter('search', '%' . $search . '%');
        }

        return $query->orderBy('o.dateDebut', 'DESC')
                     ->getQuery()
                     ->getResult();
    }

    /**
     * Search with optional filters: title, salary range, duration range
     */
    public function searchWithFilters(?string $search = null, ?float $minSalary = null, ?float $maxSalary = null, ?int $minDays = null, ?int $maxDays = null)
    {
        $qb = $this->createQueryBuilder('o');

        if ($search) {
            $qb->andWhere('(o.titre LIKE :search OR o.entreprise LIKE :search)')
               ->setParameter('search', '%' . $search . '%');
        }

        if ($minSalary !== null) {
            $qb->andWhere('o.salaire IS NOT NULL AND o.salaire >= :minSalary')
               ->setParameter('minSalary', $minSalary);
        }

        if ($maxSalary !== null) {
            $qb->andWhere('o.salaire IS NOT NULL AND o.salaire <= :maxSalary')
               ->setParameter('maxSalary', $maxSalary);
        }

        if ($minDays !== null) {
            $qb->andWhere('o.dureeJours >= :minDays')
               ->setParameter('minDays', $minDays);
        }

        if ($maxDays !== null) {
            $qb->andWhere('o.dureeJours <= :maxDays')
               ->setParameter('maxDays', $maxDays);
        }

        return $qb->orderBy('o.dateDebut', 'DESC')
                  ->getQuery()
                  ->getResult();
    }

    public function findByStatut(string $statut)
    {
        return $this->createQueryBuilder('o')
            ->where('o.statut = :statut')
            ->setParameter('statut', $statut)
            ->orderBy('o.dateDebut', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
