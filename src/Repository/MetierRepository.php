<?php

namespace App\Repository;

use App\Entity\Metier;
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
     * Recherche avancée combinée
     * @return Metier[] Returns an array of Metier objects
     */
    public function searchAdvanced(?string $search = null, ?string $secteur = null, ?string $niveauEtude = null): array
    {
        $query = $this->createQueryBuilder('m');

        // Recherche par nom ou description
        if ($search) {
            $query->andWhere('m.nom LIKE :search OR m.description LIKE :search')
                  ->setParameter('search', '%' . $search . '%');
        }

        // Filtre par secteur
        if ($secteur && $secteur !== '') {
            $query->andWhere('m.secteur = :secteur')
                  ->setParameter('secteur', $secteur);
        }

        // Filtre par niveau d'étude
        if ($niveauEtude && $niveauEtude !== '') {
            $query->andWhere('m.niveauEtude = :niveauEtude')
                  ->setParameter('niveauEtude', $niveauEtude);
        }

        return $query->orderBy('m.nom', 'ASC')
                     ->getQuery()
                     ->getResult();
    }

    /**
     * Récupère tous les secteurs distincts
     */
    public function findDistinctSecteurs(): array
    {
        return $this->createQueryBuilder('m')
            ->select('DISTINCT m.secteur')
            ->where('m.secteur IS NOT NULL')
            ->orderBy('m.secteur', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère tous les niveaux d'études distincts
     */
    public function findDistinctNiveauxEtude(): array
    {
        return $this->createQueryBuilder('m')
            ->select('DISTINCT m.niveauEtude')
            ->where('m.niveauEtude IS NOT NULL')
            ->orderBy('m.niveauEtude', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Tri par salaire moyen (ascendant)
     */
    public function findBySalaireMoyenAsc(): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.salaireeMoyen IS NOT NULL')
            ->orderBy('m.salaireeMoyen', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Tri par salaire moyen (descendant)
     */
    public function findBySalaireMoyenDesc(): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.salaireeMoyen IS NOT NULL')
            ->orderBy('m.salaireeMoyen', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Tri par nom (A à Z)
     */
    public function findByNomAsc(): array
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Tri par perspectives d'emploi
     */
    public function findByPerspectives(string $perspective): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.perspectivesEmploi = :perspective')
            ->setParameter('perspective', $perspective)
            ->orderBy('m.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
