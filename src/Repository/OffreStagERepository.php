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
