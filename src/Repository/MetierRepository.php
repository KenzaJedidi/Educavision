<?php

namespace App\Repository;

use App\Entity\Metier;
use App\Entity\Filiere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Metier>
 */
class MetierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Metier::class);
    }

    /**
     * Retourne tous les métiers ordonnés par nom
     */
    public function findAllOrderedByName(): array
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les métiers avec leur filière (évite N+1)
     */
    public function findAllWithFiliere(): array
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.filiere', 'f')
            ->addSelect('f')
            ->orderBy('f.nom', 'ASC')
            ->addOrderBy('m.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des métiers par nom ou description
     */
    public function searchByTerm(string $term): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.nom LIKE :term OR m.description LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('m.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les métiers par filière
     */
    public function findByFiliere(Filiere $filiere): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.filiere = :filiere')
            ->setParameter('filiere', $filiere)
            ->orderBy('m.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les métiers par filière ID
     */
    public function findByFiliereId(int $filiereId): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.filiere = :filiereId')
            ->setParameter('filiereId', $filiereId)
            ->orderBy('m.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte le nombre de métiers par filière
     */
    public function countByFiliere(Filiere $filiere): int
    {
        return (int) $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.filiere = :filiere')
            ->setParameter('filiere', $filiere)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Retourne les métiers sans filière
     */
    public function findWithoutFiliere(): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.filiere IS NULL')
            ->orderBy('m.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Supprime tous les métiers d'une filière
     */
    public function deleteByFiliere(Filiere $filiere): int
    {
        return $this->createQueryBuilder('m')
            ->delete()
            ->where('m.filiere = :filiere')
            ->setParameter('filiere', $filiere)
            ->getQuery()
            ->execute();
    }
}
