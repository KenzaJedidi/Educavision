<?php

namespace App\Repository;

use App\Entity\Chapter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chapter>
 */
class ChapterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chapter::class);
    }

    /**
     * Retourne les chapitres d'un cours ordonnés par ordre
     */
    public function findByCourseOrdered(int $courseId): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.course = :courseId')
            ->setParameter('courseId', $courseId)
            ->orderBy('c.ordre', 'ASC')
            ->addOrderBy('c.created_at', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne le prochain ordre pour un cours
     */
    public function getNextOrder(int $courseId): int
    {
        $maxOrder = $this->createQueryBuilder('c')
            ->select('MAX(c.ordre)')
            ->where('c.course = :courseId')
            ->setParameter('courseId', $courseId)
            ->getQuery()
            ->getSingleScalarResult();

        return ($maxOrder ?? 0) + 1;
    }

    /**
     * Recherche de chapitres par titre
     */
    public function searchByTitle(string $term, int $courseId = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.titre LIKE :term')
            ->setParameter('term', '%' . $term . '%');

        if ($courseId) {
            $qb->andWhere('c.course = :courseId')
               ->setParameter('courseId', $courseId);
        }

        return $qb->orderBy('c.ordre', 'ASC')
                 ->getQuery()
                 ->getResult();
    }

    /**
     * Compte le nombre de chapitres par cours
     */
    public function countByCourse(int $courseId): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.course = :courseId')
            ->setParameter('courseId', $courseId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    //    /**
    //     * @return Chapter[] Returns an array of Chapter objects
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

    //    public function findOneBySomeField($value): ?Chapter
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
