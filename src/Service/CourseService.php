<?php

namespace App\Service;

use App\Entity\Course;
use App\Repository\ChapterRepository;
use App\Repository\CourseRepository;
use App\Service\AI\DeepSeekApiService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Service métier pour la gestion des cours
 * 
 * Orchestre :
 * - La génération de résumés IA (DeepSeek en priorité, fallback OpenAI)
 * - La génération de mots-clés automatiques
 * - La récupération de ressources complémentaires (Wikipedia)
 * - Les opérations CRUD sur les cours
 */
class CourseService
{
    public function __construct(
        private CourseRepository $courseRepository,
        private ChapterRepository $chapterRepository,
        private EntityManagerInterface $entityManager,
        private OpenAICourseService $openAICourseService,
        private WikipediaResourceService $wikipediaResourceService,
        private DeepSeekApiService $deepSeekApiService,
        private FileTextExtractorService $fileTextExtractorService,
        private string $projectDir,
        private LoggerInterface $logger
    ) {}

    /**
     * Récupère un cours avec ses ressources complémentaires
     * 
     * @return array{course: Course, resources: array}
     */
    public function getCourseWithResources(int $courseId): array
    {
        $course = $this->courseRepository->find($courseId);

        if (!$course) {
            throw new \Exception("Cours avec l'ID {$courseId} non trouvé");
        }

        $resources = [];

        // Si le cours a des mots-clés, récupère les ressources Wikipedia
        if ($course->getKeywords()) {
            $resources = $this->wikipediaResourceService->getComplementaryResources(
                $course->getTitre(),
                $course->getKeywords()
            );
        }

        return [
            'course' => $course,
            'resources' => $resources,
        ];
    }

    /**
     * Crée un nouveau cours avec génération automatique du résumé et des mots-clés
     * 
     * @return array{course: Course, summary: string|null, keywords: string|null, errors: array}
     */
    public function createCourseWithAI(
        string $title,
        string $description,
        string $category,
        ?string $imageUrl = null,
        ?string $pdfFile = null,
        ?string $price = null,
        int $status = 1
    ): array {
        $errors = [];
        $course = new Course();
        $course->setTitre($title);
        $course->setDescription($description);
        $course->setCategory($category);
        $course->setImageUrl($imageUrl);
        $course->setPdfFile($pdfFile);
        $course->setPrice($price);
        $course->setStatus($status);
        $course->setCreatedAt(new \DateTime());

        try {
            // Génère le résumé IA
            $summary = $this->openAICourseService->generateCourseSummary($title, $description, $category);
            if ($summary) {
                $course->setWikipediaSummary($summary);
            } else {
                $errors['summary'] = $this->openAICourseService->getLastError();
                $this->logger->warning("Résumé IA non généré: " . $errors['summary']);
            }

            // Génère les mots-clés automatiques
            $keywords = $this->openAICourseService->generateKeywords($title, $description, $category);
            if ($keywords) {
                $course->setKeywords($keywords);
            } else {
                $errors['keywords'] = $this->openAICourseService->getLastError();
                $this->logger->warning("Mots-clés non générés: " . $errors['keywords']);
            }

        } catch (\Exception $e) {
            $errors['ai_error'] = $e->getMessage();
            $this->logger->error("Erreur lors de la génération IA: " . $e->getMessage());
        }

        // Sauvegarde le cours
        $this->entityManager->persist($course);
        $this->entityManager->flush();

        $this->logger->info("Cours créé avec ID: " . $course->getId());

        return [
            'course' => $course,
            'summary' => $course->getWikipediaSummary(),
            'keywords' => $course->getKeywords(),
            'errors' => $errors,
        ];
    }

    /**
     * Génère le résumé IA pour un cours existant.
     * Utilise DeepSeek en priorité (pour les étudiants), fallback sur OpenAI si DeepSeek indisponible.
     *
     * @return array{success: bool, summary: string|null, error: string|null}
     */
    public function generateCourseSummary(int $courseId): array
    {
        $course = $this->courseRepository->find($courseId);

        if (!$course) {
            return [
                'success' => false,
                'summary' => null,
                'error' => "Cours non trouvé",
            ];
        }

        $title = $course->getTitre() ?? '';
        $description = $course->getDescription() ?? '';
        $category = $course->getCategory() ?? '';

        // Extraire le texte du PDF du cours (priorité) pour le résumer
        $pdfContent = $this->extractPdfContentForSummary($course);
        $chaptersContent = $pdfContent ?? null;

        try {
            $summary = null;

            // 1. Essayer DeepSeek en priorité (API dédiée pour les étudiants)
            if ($this->deepSeekApiService->isConfigured()) {
                $summary = $this->deepSeekApiService->generateCourseSummary(
                    $title,
                    $description,
                    $category,
                    $chaptersContent
                );
                if ($summary) {
                    $this->logger->info("Résumé généré via DeepSeek pour le cours {$courseId}");
                }
            }

            // 2. Fallback sur OpenAI si DeepSeek échoue ou non configuré
            if (!$summary) {
                $summary = $this->openAICourseService->generateCourseSummary($title, $description, $category);
                if ($summary) {
                    $this->logger->info("Résumé généré via OpenAI pour le cours {$courseId}");
                }
            }

            if (!$summary) {
                $error = $this->openAICourseService->getLastError() ?? 'Aucune API IA configurée (DeepSeek ou OpenAI). Vérifiez DEEPSEEK_API_KEY dans .env';
                return [
                    'success' => false,
                    'summary' => null,
                    'error' => $error,
                ];
            }

            // Sauvegarde le résumé
            $course->setWikipediaSummary($summary);
            $this->entityManager->flush();

            return [
                'success' => true,
                'summary' => $summary,
                'error' => null,
            ];

        } catch (\Exception $e) {
            $error = "Erreur lors de la génération du résumé: " . $e->getMessage();
            $this->logger->error($error);

            return [
                'success' => false,
                'summary' => null,
                'error' => $error,
            ];
        }
    }

    /**
     * Extrait le texte du PDF du cours pour le résumer via IA.
     * Limité à ~25 000 caractères pour une réponse en temps raisonnable.
     * Retourne null si aucun PDF ou extraction impossible.
     */
    private function extractPdfContentForSummary(Course $course): ?string
    {
        $pdfFilename = $course->getPdfFile();
        if (empty($pdfFilename)) {
            return null;
        }

        $path = $this->projectDir . '/public/uploads/courses/pdf/' . $pdfFilename;
        $text = $this->fileTextExtractorService->extractTextFromPdfPath($path);

        if (empty($text) || mb_strlen($text) < 50) {
            return null;
        }

        // Limiter pour éviter timeout DeepSeek (12k → réponse ~20-40 sec)
        $maxChars = 12_000;
        if (mb_strlen($text) > $maxChars) {
            $text = mb_substr($text, 0, $maxChars) . "\n\n[... document tronqué ...]";
        }

        return "## Contenu du document PDF du cours\n\n" . $text;
    }

    /**
     * Construit une chaîne de contenu à partir des chapitres du cours pour enrichir le résumé IA.
     * Limité à ~12k caractères pour une réponse rapide de l'API DeepSeek.
     */
    private function buildChaptersContentForSummary(Course $course): ?string
    {
        $chapters = $this->chapterRepository->findByCourseOrdered($course);
        if (empty($chapters)) {
            return null;
        }

        $maxChars = 6_000;
        $currentLength = 0;
        $parts = [];

        foreach ($chapters as $chapter) {
            if ($currentLength >= $maxChars) {
                break;
            }
            $titre = $chapter->getTitre() ?? '';
            $desc = $chapter->getDescription() ?? '';
            $enriched = $chapter->getEnrichedContent() ?? '';
            $content = !empty($enriched) ? strip_tags($enriched) : ($desc ?? '');
            $remaining = $maxChars - $currentLength;
            if (mb_strlen($content) > $remaining) {
                $content = mb_substr($content, 0, $remaining) . '...';
            }
            if (!empty($titre) || !empty($content)) {
                $chunk = "### " . $titre . "\n" . $content;
                $parts[] = $chunk;
                $currentLength += mb_strlen($chunk);
            }
        }

        return empty($parts) ? null : implode("\n\n", $parts);
    }

    /**
     * Génère les mots-clés IA pour un cours existant
     * 
     * @return array{success: bool, keywords: string|null, error: string|null}
     */
    public function generateCourseKeywords(int $courseId): array
    {
        $course = $this->courseRepository->find($courseId);

        if (!$course) {
            return [
                'success' => false,
                'keywords' => null,
                'error' => "Cours non trouvé",
            ];
        }

        $title = $course->getTitre() ?? '';
        $description = $course->getDescription() ?? '';
        $category = $course->getCategory() ?? '';

        try {
            $keywords = null;

            // OpenAI en priorité pour les mots-clés (réponse souvent 5-15 sec)
            if ($this->openAICourseService->isConfigured()) {
                $keywords = $this->openAICourseService->generateKeywords($title, $description, $category);
            }

            // DeepSeek en fallback (plus lent pour ce type de requête)
            if (!$keywords && $this->deepSeekApiService->isConfigured()) {
                $keywords = $this->deepSeekApiService->generateCourseKeywords($title, $description, $category);
            }

            if (!$keywords) {
                // Fallback: mots-clés basiques à partir du titre et de la catégorie
                $keywords = $this->buildFallbackKeywords($title, $description, $category);
                $this->logger->info("Mots-clés fallback utilisés pour le cours {$courseId}");
            }

            if (!$keywords) {
                $error = $this->openAICourseService->getLastError() ?? 'Aucune API IA configurée (DeepSeek ou OpenAI).';
                return [
                    'success' => false,
                    'keywords' => null,
                    'error' => $error,
                ];
            }

            // Sauvegarde les mots-clés
            $course->setKeywords($keywords);
            $this->entityManager->flush();

            $this->logger->info("Mots-clés IA générés pour le cours {$courseId}");

            return [
                'success' => true,
                'keywords' => $keywords,
                'error' => null,
            ];

        } catch (\Exception $e) {
            $error = "Erreur lors de la génération des mots-clés: " . $e->getMessage();
            $this->logger->error($error);
            
            return [
                'success' => false,
                'keywords' => null,
                'error' => $error,
            ];
        }
    }

    /**
     * Génère des mots-clés basiques en fallback (titre + catégorie + mots courants).
     */
    private function buildFallbackKeywords(string $title, string $description, string $category): string
    {
        $keywords = [];
        if (!empty($category)) {
            $keywords[] = mb_strtolower(trim($category));
        }
        $stopWords = ['pour', 'avec', 'dans', 'vers', 'les', 'des', 'une', 'du', 'de', 'le', 'la', 'et', 'ou'];
        foreach (preg_split('/[\s,;]+/', $title . ' ' . $description, -1, PREG_SPLIT_NO_EMPTY) as $word) {
            $word = mb_strtolower(trim($word));
            if (mb_strlen($word) > 3 && !in_array($word, $stopWords) && !in_array($word, $keywords) && count($keywords) < 12) {
                $keywords[] = $word;
            }
        }
        $generic = ['apprentissage', 'cours', 'formation', 'éducation'];
        foreach ($generic as $kw) {
            if (!in_array($kw, $keywords) && count($keywords) < 12) {
                $keywords[] = $kw;
            }
        }
        return implode(', ', array_slice($keywords, 0, 12));
    }

    /**
     * Récupère les ressources Wikipedia complémentaires pour un cours
     * 
     * @return array{success: bool, articles: array, error: string|null}
     */
    public function getComplementaryResources(int $courseId): array
    {
        $course = $this->courseRepository->find($courseId);

        if (!$course) {
            return [
                'success' => false,
                'articles' => [],
                'error' => "Cours non trouvé",
            ];
        }

        try {
            $resources = $this->wikipediaResourceService->getComplementaryResources(
                $course->getTitre(),
                $course->getKeywords()
            );

            return [
                'success' => true,
                'articles' => $resources['articles'],
                'error' => null,
            ];

        } catch (\Exception $e) {
            $error = "Erreur lors de la récupération des ressources: " . $e->getMessage();
            $this->logger->error($error);
            
            return [
                'success' => false,
                'articles' => [],
                'error' => $error,
            ];
        }
    }

    /**
     * Recherche avancée de cours avec pagination
     * 
     * @return array Résultats paginés
     */
    public function searchCourses(
        ?string $title = null,
        ?string $category = null,
        ?string $keywords = null,
        ?string $sortBy = 'date',
        int $page = 1,
        int $limit = 12
    ): array {
        return $this->courseRepository->searchCoursesAdvanced(
            $title,
            $category,
            $keywords,
            $sortBy,
            $page,
            $limit
        );
    }

    /**
     * Incrémente le nombre de vues d'un cours
     */
    public function incrementViews(int $courseId): void
    {
        $course = $this->courseRepository->find($courseId);
        if ($course) {
            $course->setViews($course->getViews() + 1);
            $this->entityManager->flush();
        }
    }

    /**
     * Incrémente les "likes" d'un cours
     */
    public function incrementLikes(int $courseId): void
    {
        $course = $this->courseRepository->find($courseId);
        if ($course) {
            $course->setLikes($course->getLikes() + 1);
            $this->entityManager->flush();
        }
    }

    /**
     * Met à jour la date d'accès d'un cours
     */
    public function updateLastAccessed(int $courseId): void
    {
        $course = $this->courseRepository->find($courseId);
        if ($course) {
            $course->setLastAccessed(new \DateTime());
            $this->entityManager->flush();
        }
    }

    /**
     * Récupère les cours les plus populaires
     */
    public function getMostPopularCourses(int $limit = 6): array
    {
        return $this->courseRepository->findMostPopular($limit);
    }

    /**
     * Récupère les cours les plus récents
     */
    public function getLatestCourses(int $limit = 6): array
    {
        return $this->courseRepository->findLatest($limit);
    }
}
