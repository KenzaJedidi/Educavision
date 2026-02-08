<?php

namespace App\Repository;

use App\Entity\Cours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cours>
 */
class CoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cours::class);
    }

    /**
     * @return Cours[]
     */
    public function findPublished(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.statut = :statut')
            ->setParameter('statut', 'publie')
            ->orderBy('c.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Cours[]
     */
    public function findByProfesseur(int $professeurId): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.professeur = :profId')
            ->setParameter('profId', $professeurId)
            ->orderBy('c.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Cours[]
     */
    public function searchByFields(string $term): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.titre LIKE :term OR c.description LIKE :term OR c.categorie LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('c.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
