<?php

namespace App\Repository;

use App\Entity\Filiere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Filiere>
 */
class FiliereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Filiere::class);
    }

    /**
     * Retourne toutes les filières ordonnées par nom
     */
    public function findAllOrderedByName(): array
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les filières avec leurs métiers (évite N+1)
     */
    public function findAllWithMetiers(): array
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.metiers', 'm')
            ->addSelect('m')
            ->orderBy('f.nom', 'ASC')
            ->addOrderBy('m.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des filières par nom
     */
    public function searchByName(string $term): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.nom LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('f.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte le nombre de métiers par filière
     */
    public function countMetiersByFiliere(Filiere $filiere): int
    {
        return (int) $this->createQueryBuilder('f')
            ->select('COUNT(m.id)')
            ->leftJoin('f.metiers', 'm')
            ->where('f.id = :id')
            ->setParameter('id', $filiere->getId())
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Retourne les filières avec le nombre de métiers
     */
    public function findAllWithMetiersCount(): array
    {
        return $this->createQueryBuilder('f')
            ->select('f', 'COUNT(m.id) as metiersCount')
            ->leftJoin('f.metiers', 'm')
            ->groupBy('f.id')
            ->orderBy('f.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Supprime une filière et ses métiers associés
     */
    public function deleteWithMetiers(Filiere $filiere): void
    {
        // D'abord supprimer les métiers associés
        $this->getEntityManager()->createQuery('DELETE FROM App\Entity\Metier m WHERE m.filiere = :filiere')
            ->setParameter('filiere', $filiere)
            ->execute();

        // Puis supprimer la filière
        $this->getEntityManager()->remove($filiere);
        $this->getEntityManager()->flush();
    }
}