<?php

namespace App\Repository;

use App\Entity\Chapter;
use App\Entity\Course;
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
     * Retourne les chapitres d'un cours ordonnés par position
     */
    public function findByCourseOrdered(Course $course): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.course = :courseId')
            ->setParameter('courseId', $course->getId())
            ->orderBy('c.position', 'ASC')
            ->addOrderBy('c.created_at', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les chapitres d'un cours avec le cours (évite N+1)
     */
    public function findByCourseWithCourse(int $courseId): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.course', 'course')
            ->addSelect('course')
            ->where('c.course = :courseId')
            ->setParameter('courseId', $courseId)
            ->orderBy('c.position', 'ASC')
            ->addOrderBy('c.created_at', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne la position maximale pour un cours
     */
    public function findMaxPositionByCourse(Course $course): ?int
    {
        return $this->createQueryBuilder('c')
            ->select('MAX(c.position)')
            ->where('c.course = :courseId')
            ->setParameter('courseId', $course->getId())
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Retourne les chapitres publiés d'un cours
     */
    public function findByCourseAndStatus(Course $course, string $status = 'published'): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.course = :courseId')
            ->andWhere('c.status = :status')
            ->setParameter('courseId', $course->getId())
            ->setParameter('status', $status)
            ->orderBy('c.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les chapitres avec contenu enrichi
     */
    public function findWithEnrichedContent(Course $course): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.course = :courseId')
            ->andWhere('c.enriched_content IS NOT NULL')
            ->setParameter('courseId', $course->getId())
            ->orderBy('c.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte les chapitres par statut
     */
    public function countByStatus(Course $course, string $status): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.course = :courseId')
            ->andWhere('c.status = :status')
            ->setParameter('courseId', $course->getId())
            ->setParameter('status', $status)
            ->getQuery()
            ->getSingleScalarResult();
    }

    //    /**
    //     * @return Chapter[] Returns an array of Chapter objects
    //     */
    //    public function findByExampleField($value): array
    //    {
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
