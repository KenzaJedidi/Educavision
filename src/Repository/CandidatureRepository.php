<?php

namespace App\Repository;

use App\Entity\Candidature;
use App\Entity\OffreStage;
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

    /**
     * Retourne les candidatures les plus récentes
     */
    public function findLatest(int $limit = 20): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.dateCandidature', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne toutes les candidatures avec leur offre de stage (évite N+1)
     */
    public function findAllWithOffreStage(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.offreStage', 'o')
            ->addSelect('o')
            ->orderBy('c.dateCandidature', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les candidatures par offre de stage
     */
    public function findByOffreStage(OffreStage $offreStage): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.offreStage = :offreStage')
            ->setParameter('offreStage', $offreStage)
            ->orderBy('c.dateCandidature', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les candidatures par offre de stage ID
     */
    public function findByOffreStageId(int $offreStageId): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.offreStage = :offreStageId')
            ->setParameter('offreStageId', $offreStageId)
            ->orderBy('c.dateCandidature', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les candidatures par statut
     */
    public function findByStatut(string $statut): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.statut = :statut')
            ->setParameter('statut', $statut)
            ->orderBy('c.dateCandidature', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les candidatures par email
     */
    public function findByEmail(string $email): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.email = :email')
            ->setParameter('email', $email)
            ->orderBy('c.dateCandidature', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des candidatures par nom ou prénom
     */
    public function searchByNom(string $term): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.nom LIKE :term OR c.prenom LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('c.dateCandidature', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les candidatures favorites
     */
    public function findFavoris(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.favori = :favori')
            ->setParameter('favori', true)
            ->orderBy('c.dateCandidature', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les candidatures avec score IA
     */
    public function findWithScoreIa(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.scoreIa IS NOT NULL')
            ->orderBy('c.scoreIa', 'DESC')
            ->addOrderBy('c.dateCandidature', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les candidatures par score IA minimum
     */
    public function findByMinScoreIa(int $minScore): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.scoreIa >= :minScore')
            ->setParameter('minScore', $minScore)
            ->orderBy('c.scoreIa', 'DESC')
            ->addOrderBy('c.dateCandidature', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les candidatures avec note admin
     */
    public function findWithNoteAdmin(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.noteAdmin IS NOT NULL')
            ->orderBy('c.noteAdmin', 'DESC')
            ->addOrderBy('c.dateCandidature', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les candidatures récentes (derniers X jours)
     */
    public function findRecent(int $days = 7): array
    {
        $date = new \DateTime("-{$days} days");
        
        return $this->createQueryBuilder('c')
            ->where('c.dateCandidature >= :date')
            ->setParameter('date', $date)
            ->orderBy('c.dateCandidature', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte le nombre de candidatures par offre de stage
     */
    public function countByOffreStage(OffreStage $offreStage): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.offreStage = :offreStage')
            ->setParameter('offreStage', $offreStage)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Compte le nombre de candidatures par statut
     */
    public function countByStatut(string $statut): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.statut = :statut')
            ->setParameter('statut', $statut)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Calcule le score IA moyen
     */
    public function getAverageScoreIa(): ?float
    {
        $result = $this->createQueryBuilder('c')
            ->select('AVG(c.scoreIa)')
            ->where('c.scoreIa IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();

        return $result !== null ? (float) $result : null;
    }

    /**
     * Calcule la note admin moyenne
     */
    public function getAverageNoteAdmin(): ?float
    {
        $result = $this->createQueryBuilder('c')
            ->select('AVG(c.noteAdmin)')
            ->where('c.noteAdmin IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();

        return $result !== null ? (float) $result : null;
    }

    /**
     * Supprime toutes les candidatures d'une offre de stage
     */
    public function deleteByOffreStage(OffreStage $offreStage): int
    {
        return $this->createQueryBuilder('c')
            ->delete()
            ->where('c.offreStage = :offreStage')
            ->setParameter('offreStage', $offreStage)
            ->getQuery()
            ->execute();
    }
}
