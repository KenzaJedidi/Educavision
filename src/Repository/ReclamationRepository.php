<?php

namespace App\Repository;

use App\Entity\Reclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }

    /**
     * Retourne les réclamations triées par date (plus récent d'abord)
     */
    public function findAllOrderedByDate(): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.dateReclamation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les réclamations avec leurs réponses (évite N+1)
     */
    public function findAllWithReponses(): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.reponses', 'rep')
            ->addSelect('rep')
            ->orderBy('r.dateReclamation', 'DESC')
            ->addOrderBy('rep.dateReponse', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les réclamations par statut
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.status = :status')
            ->setParameter('status', $status)
            ->orderBy('r.dateReclamation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les réclamations par rôle
     */
    public function findByRole(string $role): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.role = :role')
            ->setParameter('role', $role)
            ->orderBy('r.dateReclamation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des réclamations par terme
     */
    public function searchByFields(string $term): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.nom LIKE :term OR r.prenom LIKE :term OR r.email LIKE :term OR r.titre LIKE :term OR r.role LIKE :term')
            ->setParameter('term', '%'.$term.'%')
            ->orderBy('r.dateReclamation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte le nombre total de réclamations
     */
    public function countBy(): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Compte le nombre de réclamations par statut
     */
    public function countByStatus(string $status): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Compte le nombre de réclamations par rôle
     */
    public function countByRole(string $role): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.role = :role')
            ->setParameter('role', $role)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Retourne les réclamations récentes (derniers 7 jours)
     */
    public function findRecent(int $days = 7): array
    {
        $date = new \DateTime("-{$days} days");
        
        return $this->createQueryBuilder('r')
            ->where('r.dateReclamation >= :date')
            ->setParameter('date', $date)
            ->orderBy('r.dateReclamation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les réclamations sans réponse
     */
    public function findWithoutReponse(): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.status = :status')
            ->setParameter('status', 'en cours de traitement')
            ->orderBy('r.dateReclamation', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les réclamations traitées
     */
    public function findTreated(): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.status = :status')
            ->setParameter('status', 'traiter')
            ->orderBy('r.dateReclamation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Supprime une réclamation et ses réponses
     */
    public function deleteWithReponses(Reclamation $reclamation): void
    {
        // D'abord supprimer les réponses associées
        $this->getEntityManager()->createQuery('DELETE FROM App\Entity\Reponse r WHERE r.reclamation = :reclamation')
            ->setParameter('reclamation', $reclamation)
            ->execute();

        // Puis supprimer la réclamation
        $this->getEntityManager()->remove($reclamation);
        $this->getEntityManager()->flush();
    }
}