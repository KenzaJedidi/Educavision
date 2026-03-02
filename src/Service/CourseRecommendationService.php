<?php

namespace App\Service;

use App\Entity\Course;
use App\Repository\CourseRepository;
use Psr\Log\LoggerInterface;

class CourseRecommendationService
{
    private CourseRepository $courseRepository;
    private LoggerInterface $logger;

    public function __construct(
        CourseRepository $courseRepository,
        LoggerInterface $logger
    ) {
        $this->courseRepository = $courseRepository;
        $this->logger = $logger;
    }

    /**
     * Recommande des cours similaires basés sur les mots-clés
     */
    public function getSimilarCourses(Course $course, int $limit = 5): array
    {
        $keywords = $this->extractKeywords($course);
        
        if (empty($keywords)) {
            return $this->getFallbackRecommendations($course, $limit);
        }

        $similarCourses = [];
        
        // Recherche par titre
        $titleMatches = $this->searchByTitle($course, $keywords, $limit);
        $similarCourses = array_merge($similarCourses, $titleMatches);

        // Recherche par description
        if (count($similarCourses) < $limit) {
            $descriptionMatches = $this->searchByDescription($course, $keywords, $limit - count($similarCourses));
            $similarCourses = array_merge($similarCourses, $descriptionMatches);
        }

        // Recherche par catégorie
        if (count($similarCourses) < $limit && $course->getCategory()) {
            $categoryMatches = $this->searchByCategory($course, $limit - count($similarCourses));
            $similarCourses = array_merge($similarCourses, $categoryMatches);
        }

        // Compléter avec des recommandations par popularité si nécessaire
        if (count($similarCourses) < $limit) {
            $popularCourses = $this->getPopularRecommendations($course, $limit - count($similarCourses));
            $similarCourses = array_merge($similarCourses, $popularCourses);
        }

        // Éliminer les doublons et limiter le nombre
        $uniqueCourses = [];
        $seenIds = [];
        
        foreach ($similarCourses as $similarCourse) {
            if (!in_array($similarCourse->getId(), $seenIds)) {
                $uniqueCourses[] = $similarCourse;
                $seenIds[] = $similarCourse->getId();
            }
        }

        return array_slice($uniqueCourses, 0, $limit);
    }

    /**
     * Extrait les mots-clés d'un cours
     */
    private function extractKeywords(Course $course): array
    {
        $text = strtolower($course->getTitre() . ' ' . ($course->getDescription() ?? ''));
        
        // Nettoyage du texte
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        
        $words = explode(' ', trim($text));
        
        // Mots à exclure
        $stopWords = [
            'le', 'la', 'les', 'de', 'des', 'du', 'et', 'à', 'a', 'pour', 'dans', 'avec', 'sur', 'par',
            'que', 'qui', 'quoi', 'où', 'quand', 'comment', 'pourquoi', 'ce', 'cette', 'ces', 'cet',
            'une', 'un', 'vos', 'votre', 'nos', 'notre', 'leur', 'leurs', 'tout', 'tous', 'toute', 'toutes',
            'est', 'sont', 'été', 'être', 'avoir', 'plus', 'moins', 'très', 'bien', 'aussi', 'comme',
            'cours', 'formation', 'apprentissage', 'leçon', 'tutoriel', 'guide', 'initiation', 'découverte'
        ];
        
        $keywords = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 2 && !in_array($word, $stopWords);
        });
        
        // Compter les occurrences et garder les plus fréquents
        $keywordCounts = array_count_values($keywords);
        arsort($keywordCounts);
        
        return array_keys(array_slice($keywordCounts, 0, 10));
    }

    /**
     * Recherche des cours par similarité de titre
     */
    private function searchByTitle(Course $course, array $keywords, int $limit): array
    {
        $qb = $this->courseRepository->createQueryBuilder('c')
            ->where('c.status = :status')
            ->andWhere('c.id != :currentId')
            ->setParameter('status', 1)
            ->setParameter('currentId', $course->getId());

        $orConditions = [];
        $params = ['status' => 1, 'currentId' => $course->getId()];

        foreach ($keywords as $i => $keyword) {
            $orConditions[] = 'LOWER(c.titre) LIKE :keyword' . $i;
            $params['keyword' . $i] = '%' . $keyword . '%';
        }

        if (!empty($orConditions)) {
            $qb->andWhere('(' . implode(' OR ', $orConditions) . ')');
            foreach ($params as $key => $value) {
                if ($key !== 'status' && $key !== 'currentId') {
                    $qb->setParameter($key, $value);
                }
            }
        }

        return $qb->orderBy('c.popularity_score', 'DESC')
                  ->setMaxResults($limit)
                  ->getQuery()
                  ->getResult();
    }

    /**
     * Recherche des cours par similarité de description
     */
    private function searchByDescription(Course $course, array $keywords, int $limit): array
    {
        $qb = $this->courseRepository->createQueryBuilder('c')
            ->where('c.status = :status')
            ->andWhere('c.id != :currentId')
            ->andWhere('c.description IS NOT NULL')
            ->setParameter('status', 1)
            ->setParameter('currentId', $course->getId());

        $orConditions = [];
        $params = ['status' => 1, 'currentId' => $course->getId()];

        foreach ($keywords as $i => $keyword) {
            $orConditions[] = 'LOWER(c.description) LIKE :keyword' . $i;
            $params['keyword' . $i] = '%' . $keyword . '%';
        }

        if (!empty($orConditions)) {
            $qb->andWhere('(' . implode(' OR ', $orConditions) . ')');
            foreach ($params as $key => $value) {
                if ($key !== 'status' && $key !== 'currentId') {
                    $qb->setParameter($key, $value);
                }
            }
        }

        return $qb->orderBy('c.popularity_score', 'DESC')
                  ->setMaxResults($limit)
                  ->getQuery()
                  ->getResult();
    }

    /**
     * Recherche des cours dans la même catégorie
     */
    private function searchByCategory(Course $course, int $limit): array
    {
        return $this->courseRepository->findBy(
            [
                'category' => $course->getCategory(),
                'status' => 1
            ],
            ['popularity_score' => 'DESC'],
            $limit
        );
    }

    /**
     * Recommandations basées sur la popularité
     */
    private function getPopularRecommendations(Course $course, int $limit): array
    {
        return $this->courseRepository->createQueryBuilder('c')
            ->where('c.status = :status')
            ->andWhere('c.id != :currentId')
            ->setParameter('status', 1)
            ->setParameter('currentId', $course->getId())
            ->orderBy('c.popularity_score', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recommandations de secours si aucune similarité trouvée
     */
    public function getFallbackRecommendations(?Course $course = null, int $limit): array
    {
        return $this->courseRepository->findBy(
            ['status' => 1],
            ['popularity_score' => 'DESC'],
            $limit
        );
    }

    /**
     * Calcule un score de similarité entre deux cours
     */
    public function calculateSimilarityScore(Course $course1, Course $course2): float
    {
        if ($course1->getId() === $course2->getId()) {
            return 0;
        }

        $score = 0;
        $maxScore = 100;

        // Similarité de catégorie (30 points)
        if ($course1->getCategory() && $course1->getCategory() === $course2->getCategory()) {
            $score += 30;
        }

        // Similarité de mots-clés dans le titre (40 points)
        $keywords1 = $this->extractKeywords($course1);
        $keywords2 = $this->extractKeywords($course2);
        $commonKeywords = array_intersect($keywords1, $keywords2);
        $score += min(40, count($commonKeywords) * 10);

        // Similarité de description (30 points)
        if ($course1->getDescription() && $course2->getDescription()) {
            $desc1 = strtolower($course1->getDescription());
            $desc2 = strtolower($course2->getDescription());
            $commonDescWords = array_intersect(
                $this->extractKeywords($course1),
                $this->extractKeywords($course2)
            );
            $score += min(30, count($commonDescWords) * 5);
        }

        return $score / $maxScore; // Retourne un score entre 0 et 1
    }

    /**
     * Recommande des cours basés sur les préférences de l'utilisateur
     * (à implémenter avec un système utilisateur)
     */
    public function getPersonalizedRecommendations(?array $preferences = null, int $limit = 5): array
    {
        if (!$preferences) {
            return $this->getFallbackRecommendations(null, $limit);
        }

        $qb = $this->courseRepository->createQueryBuilder('c')
            ->where('c.status = :status')
            ->setParameter('status', 1);

        // Filtrer par catégories préférées
        if (!empty($preferences['categories'])) {
            $qb->andWhere('c.category IN (:categories)')
               ->setParameter('categories', $preferences['categories']);
        }

        // Ordonner par popularité et pertinence
        return $qb->orderBy('c.popularity_score', 'DESC')
                  ->addOrderBy('c.created_at', 'DESC')
                  ->setMaxResults($limit)
                  ->getQuery()
                  ->getResult();
    }

    /**
     * Récupère les cours recommandés pour la page d'accueil
     */
    public function getHomepageRecommendations(): array
    {
        return [
            'trending' => $this->getTrendingCourses(),
            'popular' => $this->getPopularCourses(),
            'recent' => $this->getRecentCourses(),
            'by_category' => $this->getCoursesByMainCategories(),
        ];
    }

    private function getTrendingCourses(int $limit = 3): array
    {
        $date = new \DateTime();
        $date->modify('-7 days');

        return $this->courseRepository->createQueryBuilder('c')
            ->where('c.status = :status')
            ->andWhere('c.created_at >= :date')
            ->setParameter('status', 1)
            ->setParameter('date', $date)
            ->orderBy('c.popularity_score', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    private function getPopularCourses(int $limit = 3): array
    {
        return $this->courseRepository->findBy(
            ['status' => 1],
            ['popularity_score' => 'DESC'],
            $limit
        );
    }

    private function getRecentCourses(int $limit = 3): array
    {
        return $this->courseRepository->findBy(
            ['status' => 1],
            ['created_at' => 'DESC'],
            $limit
        );
    }

    private function getCoursesByMainCategories(): array
    {
        $mainCategories = ['Développement', 'Design', 'Marketing', 'Business'];
        $coursesByCategory = [];

        foreach ($mainCategories as $category) {
            $courses = $this->courseRepository->findBy(
                ['category' => $category, 'status' => 1],
                ['popularity_score' => 'DESC'],
                2
            );
            $coursesByCategory[$category] = $courses;
        }

        return $coursesByCategory;
    }
}
