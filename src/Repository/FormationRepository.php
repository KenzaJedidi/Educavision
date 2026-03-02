<?php

namespace App\Repository;

use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Formation>
 */
class FormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formation::class);
    }

    /**
     * Retourne toutes les formations ordonnées par nom
     */
    public function findAllOrderedByName(): array
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les formations avec leurs prérequis (évite N+1)
     */
    public function findAllWithPrerequis(): array
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.prerequis', 'p')
            ->addSelect('p')
            ->orderBy('f.nom', 'ASC')
            ->addOrderBy('p.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des formations par nom ou description
     */
    public function searchByTerm(string $term): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.nom LIKE :term OR f.description LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('f.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les formations par niveau
     */
    public function findByNiveau(string $niveau): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.niveau = :niveau')
            ->setParameter('niveau', $niveau)
            ->orderBy('f.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les formations par durée
     */
    public function findByDuree(string $duree): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.duree = :duree')
            ->setParameter('duree', $duree)
            ->orderBy('f.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte le nombre de prérequis par formation
     */
    public function countPrerequisByFormation(Formation $formation): int
    {
        return (int) $this->createQueryBuilder('f')
            ->select('COUNT(p.id)')
            ->leftJoin('f.prerequis', 'p')
            ->where('f.id = :id')
            ->setParameter('id', $formation->getId())
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Retourne les formations avec le nombre de prérequis
     */
    public function findAllWithPrerequisCount(): array
    {
        return $this->createQueryBuilder('f')
            ->select('f', 'COUNT(p.id) as prerequisCount')
            ->leftJoin('f.prerequis', 'p')
            ->groupBy('f.id')
            ->orderBy('f.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Supprime une formation et ses prérequis associés
     */
    public function deleteWithPrerequis(Formation $formation): void
    {
        // D'abord supprimer les prérequis associés
        $this->getEntityManager()->createQuery('DELETE FROM App\Entity\Prerequis p WHERE p.formation = :formation')
            ->setParameter('formation', $formation)
            ->execute();

        // Puis supprimer la formation
        $this->getEntityManager()->remove($formation);
        $this->getEntityManager()->flush();
    }
}