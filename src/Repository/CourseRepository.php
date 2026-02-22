<?php

namespace App\Repository;

use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Course>
 */
class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    /**
     * Retourne les cours publiés (status = published)
     */
    public function findPublishedCourses(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.status = :status')
            ->setParameter('status', 'published')
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche avancée de cours
     */
    public function searchCourses(string $term = null, string $category = null, ?string $status = null): array
    {
        $qb = $this->createQueryBuilder('c');

        if ($status) {
            $qb->andWhere('c.status = :status')
               ->setParameter('status', $status);
        }
        // Ne pas filtrer par statut par défaut pour l'admin (montrer tous les cours)

        if ($term) {
            $qb->andWhere('c.title LIKE :term OR c.description LIKE :term')
               ->setParameter('term', '%' . $term . '%');
        }

        if ($category) {
            $qb->andWhere('c.category = :category')
               ->setParameter('category', $category);
        }

        return $qb->orderBy('c.createdAt', 'DESC')
                 ->getQuery()
                 ->getResult();
    }

    /**
     * Retourne les catégories disponibles
     */
    public function getAvailableCategories(): array
    {
        return $this->createQueryBuilder('c')
            ->select('DISTINCT c.category')
            ->where('c.category IS NOT NULL')
            ->andWhere('c.status = :status')
            ->setParameter('status', 'published')
            ->getQuery()
            ->getSingleColumnResult();
    }

    /**
     * Compte le nombre de cours publiés
     */
    public function countPublishedCourses(): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.status = :status')
            ->setParameter('status', 'published')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @deprecated Use findPublishedCourses() instead
     */
    public function findActiveCourses(): array
    {
        return $this->findPublishedCourses();
    }

    /**
     * @deprecated Use countPublishedCourses() instead
     */
    public function countActiveCourses(): int
    {
        return $this->countPublishedCourses();
    }

    //    /**
    //     * @return Course[] Returns an array of Course objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Course
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
