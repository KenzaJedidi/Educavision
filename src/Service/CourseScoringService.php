<?php

namespace App\Service;

use App\Entity\Course;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class CourseScoringService
{
    private CourseRepository $courseRepository;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(
        CourseRepository $courseRepository,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->courseRepository = $courseRepository;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * Calcule et met à jour le score de popularité d'un cours
     * Formule: Score = (Vues × 0.5) + (Likes × 2) + (Commentaires × 1.5)
     */
    public function updateCourseScore(Course $course): string
    {
        $score = ($course->getViews() * 0.5) + ($course->getLikes() * 2) + ($course->getCommentsCount() * 1.5);
        $course->setPopularityScore((string)$score);
        
        $this->entityManager->persist($course);
        $this->entityManager->flush();
        
        return $course->getPopularityScore();
    }

    /**
     * Met à jour les scores de tous les cours
     */
    public function updateAllCoursesScores(): int
    {
        $courses = $this->courseRepository->findAll();
        $updatedCount = 0;

        foreach ($courses as $course) {
            $this->updateCourseScore($course);
            $updatedCount++;
        }

        $this->logger->info("Scores de popularité mis à jour pour {$updatedCount} cours");
        return $updatedCount;
    }

    /**
     * Récupère le top 5 des cours les plus populaires
     */
    public function getTopCourses(int $limit = 5): array
    {
        return $this->courseRepository->findBy(
            ['status' => 1],
            ['popularity_score' => 'DESC'],
            $limit
        );
    }

    /**
     * Récupère les cours "trending" (créés récemment avec un bon score)
     */
    public function getTrendingCourses(int $days = 7, int $limit = 5): array
    {
        $date = new \DateTime();
        $date->modify("-{$days} days");

        return $this->courseRepository->createQueryBuilder('c')
            ->where('c.status = :status')
            ->andWhere('c.created_at >= :date')
            ->andWhere('c.popularity_score > :minScore')
            ->setParameter('status', 1)
            ->setParameter('date', $date)
            ->setParameter('minScore', 50)
            ->orderBy('c.popularity_score', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les cours les plus vus
     */
    public function getMostViewedCourses(int $limit = 5): array
    {
        return $this->courseRepository->findBy(
            ['status' => 1],
            ['views' => 'DESC'],
            $limit
        );
    }

    /**
     * Récupère les cours les plus aimés
     */
    public function getMostLikedCourses(int $limit = 5): array
    {
        return $this->courseRepository->findBy(
            ['status' => 1],
            ['likes' => 'DESC'],
            $limit
        );
    }

    /**
     * Calcule les statistiques globales des cours
     */
    public function getGlobalStatistics(): array
    {
        $totalCourses = $this->courseRepository->count(['status' => 1]);
        $totalViews = $this->courseRepository->createQueryBuilder('c')
            ->select('SUM(c.views)')
            ->where('c.status = :status')
            ->setParameter('status', 1)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        $totalLikes = $this->courseRepository->createQueryBuilder('c')
            ->select('SUM(c.likes)')
            ->where('c.status = :status')
            ->setParameter('status', 1)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        $totalComments = $this->courseRepository->createQueryBuilder('c')
            ->select('SUM(c.comments_count)')
            ->where('c.status = :status')
            ->setParameter('status', 1)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        $avgScore = $this->courseRepository->createQueryBuilder('c')
            ->select('AVG(c.popularity_score)')
            ->where('c.status = :status')
            ->setParameter('status', 1)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        return [
            'total_courses' => $totalCourses,
            'total_views' => (int) $totalViews,
            'total_likes' => (int) $totalLikes,
            'total_comments' => (int) $totalComments,
            'average_score' => round((float) $avgScore, 2),
        ];
    }

    /**
     * Enregistre une vue pour un cours
     */
    public function recordView(Course $course): void
    {
        $course->incrementViews();
        $this->updateCourseScore($course);
    }

    /**
     * Enregistre un like pour un cours
     */
    public function recordLike(Course $course): void
    {
        $course->incrementLikes();
        $this->updateCourseScore($course);
    }

    /**
     * Enregistre un commentaire pour un cours
     */
    public function recordComment(Course $course): void
    {
        $course->incrementCommentsCount();
        $this->updateCourseScore($course);
    }

    /**
     * Récupère les cours par catégorie avec leurs scores
     */
    public function getCoursesByCategoryWithScores(): array
    {
        $courses = $this->courseRepository->createQueryBuilder('c')
            ->where('c.status = :status')
            ->andWhere('c.category IS NOT NULL')
            ->setParameter('status', 1)
            ->orderBy('c.category', 'ASC')
            ->addOrderBy('c.popularity_score', 'DESC')
            ->getQuery()
            ->getResult();

        $categorizedCourses = [];
        foreach ($courses as $course) {
            $category = $course->getCategory() ?: 'Autre';
            $categorizedCourses[$category][] = $course;
        }

        return $categorizedCourses;
    }

    /**
     * Récupère les recommandations personnalisées basées sur la popularité
     */
    public function getPersonalizedRecommendations(?Course $currentCourse = null, int $limit = 3): array
    {
        $qb = $this->courseRepository->createQueryBuilder('c')
            ->where('c.status = :status')
            ->setParameter('status', 1)
            ->orderBy('c.popularity_score', 'DESC')
            ->setMaxResults($limit * 2); // On récupère plus pour filtrer si nécessaire

        if ($currentCourse) {
            $qb->andWhere('c.id != :currentId')
               ->setParameter('currentId', $currentCourse->getId());
        }

        $courses = $qb->getQuery()->getResult();

        // Mélanger les résultats pour plus de diversité
        shuffle($courses);
        
        return array_slice($courses, 0, $limit);
    }

    /**
     * Met à jour le classement des cours (pour affichage)
     */
    public function updateCourseRankings(): void
    {
        $courses = $this->courseRepository->findBy(
            ['status' => 1],
            ['popularity_score' => 'DESC']
        );

        $rank = 1;
        foreach ($courses as $course) {
            // Vous pourriez ajouter un champ 'ranking' dans l'entité si nécessaire
            $rank++;
        }
    }

    /**
     * Récupère les cours avec le plus grand progrès (augmentation du score)
     */
    public function getFastestGrowingCourses(int $days = 7, int $limit = 5): array
    {
        // Cette méthode nécessiterait un historique des scores pour être précise
        // Pour l'instant, on se base sur les cours récents avec beaucoup de vues
        $date = new \DateTime();
        $date->modify("-{$days} days");

        return $this->courseRepository->createQueryBuilder('c')
            ->where('c.status = :status')
            ->andWhere('c.created_at >= :date')
            ->setParameter('status', 1)
            ->setParameter('date', $date)
            ->orderBy('c.views', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
