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
     * Retourne les réclamations triées de la plus importante et fréquente vers la moins.
     * Importance: statut 'en cours de traitement' en premier.
     * Fréquence: nombre d'occurrences du même titre (plus fréquent d'abord).
     * Départage: plus récent d'abord.
     */
    /* Tri avancé retiré à la demande; conserver le dépôt minimal. */

    public function searchByFields(string $term): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.nom LIKE :term OR r.prenom LIKE :term OR r.email LIKE :term OR r.titre LIKE :term OR r.role LIKE :term')
            ->setParameter('term', '%'.$term.'%')
            ->getQuery()
            ->getResult();
    }

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
}