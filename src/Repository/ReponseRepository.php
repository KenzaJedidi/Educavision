<?php

namespace App\Repository;

use App\Entity\Reponse;
use App\Entity\Reclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reponse::class);
    }

    /**
     * Retourne toutes les réponses ordonnées par date
     */
    public function findAllOrderedByDate(): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.dateReponse', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les réponses avec leur réclamation (évite N+1)
     */
    public function findAllWithReclamation(): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.reclamation', 'rec')
            ->addSelect('rec')
            ->orderBy('r.dateReponse', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les réponses par réclamation
     */
    public function findByReclamation(Reclamation $reclamation): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.reclamation = :reclamation')
            ->setParameter('reclamation', $reclamation)
            ->orderBy('r.dateReponse', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les réponses par réclamation ID
     */
    public function findByReclamationId(int $reclamationId): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.reclamation = :reclamationId')
            ->setParameter('reclamationId', $reclamationId)
            ->orderBy('r.dateReponse', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des réponses par contenu
     */
    public function searchByContenu(string $term): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.contenu LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('r.dateReponse', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les réponses par note
     */
    public function findByRating(int $rating): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.rating = :rating')
            ->setParameter('rating', $rating)
            ->orderBy('r.dateReponse', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les réponses avec une note
     */
    public function findWithRating(): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.rating IS NOT NULL')
            ->orderBy('r.rating', 'DESC')
            ->addOrderBy('r.dateReponse', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les réponses sans note
     */
    public function findWithoutRating(): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.rating IS NULL')
            ->orderBy('r.dateReponse', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte le nombre de réponses par réclamation
     */
    public function countByReclamation(Reclamation $reclamation): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.reclamation = :reclamation')
            ->setParameter('reclamation', $reclamation)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Retourne les réponses récentes (derniers X jours)
     */
    public function findRecent(int $days = 7): array
    {
        $date = new \DateTime("-{$days} days");
        
        return $this->createQueryBuilder('r')
            ->where('r.dateReponse >= :date')
            ->setParameter('date', $date)
            ->orderBy('r.dateReponse', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Calcule la note moyenne des réponses
     */
    public function getAverageRating(): ?float
    {
        $result = $this->createQueryBuilder('r')
            ->select('AVG(r.rating)')
            ->where('r.rating IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();

        return $result !== null ? (float) $result : null;
    }

    /**
     * Supprime toutes les réponses d'une réclamation
     */
    public function deleteByReclamation(Reclamation $reclamation): int
    {
        return $this->createQueryBuilder('r')
            ->delete()
            ->where('r.reclamation = :reclamation')
            ->setParameter('reclamation', $reclamation)
            ->getQuery()
            ->execute();
    }
}
