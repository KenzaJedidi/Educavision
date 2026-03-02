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

    /**
     * Retourne toutes les offres ordonnées par date de début
     */
    public function findAllOrderedByDate(): array
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.dateDebut', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les offres avec leurs candidatures (évite N+1)
     */
    public function findAllWithCandidatures(): array
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.candidatures', 'c')
            ->addSelect('c')
            ->orderBy('o.dateDebut', 'DESC')
            ->addOrderBy('c.dateCandidature', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des offres par titre
     */
    public function searchByTitre(?string $search = null): array
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
     * Recherche avancée avec filtres multiples
     */
    public function searchWithFilters(?string $search = null, ?float $minSalary = null, ?float $maxSalary = null, ?int $minDays = null, ?int $maxDays = null): array
    {
        $qb = $this->createQueryBuilder('o');

        if ($search) {
            $qb->andWhere('(o.titre LIKE :search OR o.entreprise LIKE :search OR o.description LIKE :search)')
               ->setParameter('search', '%' . $search . '%');
        }

        if ($minSalary !== null) {
            $qb->andWhere('o.salaire IS NOT NULL AND CAST(o.salaire AS DECIMAL) >= :minSalary')
               ->setParameter('minSalary', $minSalary);
        }

        if ($maxSalary !== null) {
            $qb->andWhere('o.salaire IS NOT NULL AND CAST(o.salaire AS DECIMAL) <= :maxSalary')
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

    /**
     * Retourne les offres par statut
     */
    public function findByStatut(string $statut): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.statut = :statut')
            ->setParameter('statut', $statut)
            ->orderBy('o.dateDebut', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les offres par entreprise
     */
    public function findByEntreprise(string $entreprise): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.entreprise LIKE :entreprise')
            ->setParameter('entreprise', '%' . $entreprise . '%')
            ->orderBy('o.dateDebut', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les offres par lieu
     */
    public function findByLieu(string $lieu): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.lieu LIKE :lieu')
            ->setParameter('lieu', '%' . $lieu . '%')
            ->orderBy('o.dateDebut', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les offres récentes (derniers X jours)
     */
    public function findRecent(int $days = 30): array
    {
        $date = new \DateTime("-{$days} days");
        
        return $this->createQueryBuilder('o')
            ->where('o.dateCreation >= :date')
            ->setParameter('date', $date)
            ->orderBy('o.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les offres ouvertes
     */
    public function findOpen(): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.statut = :statut')
            ->setParameter('statut', 'Ouvert')
            ->orderBy('o.dateDebut', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les offres pourvues
     */
    public function findFilled(): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.statut = :statut')
            ->setParameter('statut', 'Pourvu')
            ->orderBy('o.dateDebut', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte le nombre d'offres par statut
     */
    public function countByStatut(string $statut): int
    {
        return (int) $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->where('o.statut = :statut')
            ->setParameter('statut', $statut)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Compte le nombre total d'offres
     */
    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Supprime une offre et ses candidatures associées
     */
    public function deleteWithCandidatures(OffreStage $offreStage): void
    {
        // D'abord supprimer les candidatures associées
        $this->getEntityManager()->createQuery('DELETE FROM App\Entity\Candidature c WHERE c.offreStage = :offreStage')
            ->setParameter('offreStage', $offreStage)
            ->execute();

        // Puis supprimer l'offre
        $this->getEntityManager()->remove($offreStage);
        $this->getEntityManager()->flush();
    }
}
