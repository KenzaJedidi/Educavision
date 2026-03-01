<?php

namespace App\Controller\Api;

use App\Entity\Chapter;
use App\Entity\Course;
use App\Repository\ChapterRepository;
use App\Repository\CourseRepository;
use App\Service\ChapterService;
use App\DTO\ChapterEnrichmentDTO;
use App\DTO\ChapterTranslationDTO;
use App\DTO\ChapterListItemDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/chapters')]
class ChapterController extends AbstractController
{
    public function __construct(
        private ChapterService $chapterService,
        private ChapterRepository $chapterRepository,
        private CourseRepository $courseRepository,
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Liste les chapitres d'un cours
     * GET /api/chapters/course/{courseId}
     */
    #[Route('/course/{courseId}', name: 'api_chapters_by_course', methods: ['GET'])]
    public function getChaptersByCourse(int $courseId): JsonResponse
    {
        try {
            $course = $this->courseRepository->find($courseId);
            if (!$course) {
                return $this->json([
                    'success' => false,
                    'error' => 'Cours non trouvé',
                ], Response::HTTP_NOT_FOUND);
            }

            $chapters = $this->chapterService->getChaptersByCourse($course);
            
            $items = array_map(function(Chapter $chapter) {
                return new ChapterListItemDTO(
                    $chapter->getId(),
                    $chapter->getTitre(),
                    $chapter->getDescription(),
                    $chapter->getStatus(),
                    $chapter->getPosition(),
                    $chapter->getDifficultyLevel(),
                    $chapter->getImageUrl(),
                    $chapter->getCreatedAt(),
                    $chapter->getUpdatedAt(),
                    !empty($chapter->getEnrichedContent()),
                    !empty($chapter->getStructuredOutline()),
                    array_keys($chapter->getTranslations() ?? []),
                    $chapter->getTeacherName()
                );
            }, $chapters);

            return $this->json([
                'success' => true,
                'data' => array_map(fn($item) => $item->toArray(), $items),
                'count' => count($items),
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Récupère un chapitre spécifique
     * GET /api/chapters/{id}
     */
    #[Route('/{id}', name: 'api_chapter_get', methods: ['GET'])]
    public function getChapter(int $id): JsonResponse
    {
        try {
            $chapter = $this->chapterRepository->find($id);
            if (!$chapter) {
                return $this->json([
                    'success' => false,
                    'error' => 'Chapitre non trouvé',
                ], Response::HTTP_NOT_FOUND);
            }

            return $this->json([
                'success' => true,
                'data' => [
                    'id' => $chapter->getId(),
                    'titre' => $chapter->getTitre(),
                    'description' => $chapter->getDescription(),
                    'enrichedContent' => $chapter->getEnrichedContent(),
                    'difficultyLevel' => $chapter->getDifficultyLevel(),
                    'structuredOutline' => $chapter->getStructuredOutline(),
                    'status' => $chapter->getStatus(),
                    'position' => $chapter->getPosition(),
                    'translations' => $chapter->getTranslations(),
                    'imageUrl' => $chapter->getImageUrl(),
                    'teacherName' => $chapter->getTeacherName(),
                    'createdAt' => $chapter->getCreatedAt()?->format('Y-m-d\TH:i:sP'),
                    'updatedAt' => $chapter->getUpdatedAt()?->format('Y-m-d\TH:i:sP'),
                ],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Enrichit un chapitre avec IA (enrichir le contenu + détecter le niveau + générer le plan)
     * POST /api/chapters/{id}/enrich
     */
    #[Route('/{id}/enrich', name: 'api_chapter_enrich', methods: ['POST'])]
    public function enrichChapter(int $id): JsonResponse
    {
        try {
            $chapter = $this->chapterRepository->find($id);
            if (!$chapter) {
                return $this->json([
                    'success' => false,
                    'error' => 'Chapitre non trouvé',
                ], Response::HTTP_NOT_FOUND);
            }

            // Enrichir le contenu
            $chapter = $this->chapterService->enrichChapter($chapter);

            // Détecter le niveau
            $chapter = $this->chapterService->detectDifficultyLevel($chapter);

            // Générer le plan
            $chapter = $this->chapterService->generateOutline($chapter);

            $dto = new ChapterEnrichmentDTO(
                $chapter->getId(),
                true,
                $chapter->getEnrichedContent(),
                $chapter->getDifficultyLevel(),
                $chapter->getStructuredOutline()
            );

            return $this->json([
                'success' => true,
                'data' => $dto->toArray(),
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Traduit un chapitre
     * POST /api/chapters/{id}/translate
     * Body: { "targetLanguage": "en" }
     */
    #[Route('/{id}/translate', name: 'api_chapter_translate', methods: ['POST'])]
    public function translateChapter(int $id, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $targetLanguage = $data['targetLanguage'] ?? 'en';

            $chapter = $this->chapterRepository->find($id);
            if (!$chapter) {
                return $this->json([
                    'success' => false,
                    'error' => 'Chapitre non trouvé',
                ], Response::HTTP_NOT_FOUND);
            }

            $chapter = $this->chapterService->translateChapter($chapter, $targetLanguage);

            $dto = new ChapterTranslationDTO(
                $chapter->getId(),
                true,
                $targetLanguage,
                $chapter->getTranslation($targetLanguage) ? [
                    'titre' => $chapter->getTranslations()[$targetLanguage]['titre'],
                    'description' => $chapter->getTranslations()[$targetLanguage]['description'],
                ] : null
            );

            return $this->json([
                'success' => true,
                'data' => $dto->toArray(),
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Réorganise les chapitres (drag & drop)
     * POST /api/chapters/reorder
     * Body: { "courseId": 1, "chapterPositions": [{"chapterId": 1, "position": 1}] }
     */
    #[Route('/reorder', name: 'api_chapters_reorder', methods: ['POST'])]
    public function reorderChapters(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $courseId = $data['courseId'] ?? null;
            $chapterPositions = $data['chapterPositions'] ?? [];

            if (!$courseId || empty($chapterPositions)) {
                return $this->json([
                    'success' => false,
                    'error' => 'courseId et chapterPositions sont requis',
                ], Response::HTTP_BAD_REQUEST);
            }

            $course = $this->courseRepository->find($courseId);
            if (!$course) {
                return $this->json([
                    'success' => false,
                    'error' => 'Cours non trouvé',
                ], Response::HTTP_NOT_FOUND);
            }

            // Convertir le format: [{"chapterId": 1, "position": 1}] en [1 => 1, 2 => 2]
            $positions = [];
            foreach ($chapterPositions as $item) {
                if (isset($item['chapterId']) && isset($item['position'])) {
                    $positions[$item['chapterId']] = $item['position'];
                }
            }

            $this->chapterService->reorderChapters($course, $positions);

            return $this->json([
                'success' => true,
                'message' => 'Chapitres réorganisés avec succès',
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Publie un chapitre
     * POST /api/chapters/{id}/publish
     */
    #[Route('/{id}/publish', name: 'api_chapter_publish', methods: ['POST'])]
    public function publishChapter(int $id): JsonResponse
    {
        try {
            $chapter = $this->chapterRepository->find($id);
            if (!$chapter) {
                return $this->json([
                    'success' => false,
                    'error' => 'Chapitre non trouvé',
                ], Response::HTTP_NOT_FOUND);
            }

            $chapter = $this->chapterService->publishChapter($chapter);

            return $this->json([
                'success' => true,
                'data' => [
                    'id' => $chapter->getId(),
                    'status' => $chapter->getStatus(),
                    'message' => 'Chapitre publié avec succès',
                ],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Sauvegarde un brouillon
     * POST /api/chapters/{id}/draft
     */
    #[Route('/{id}/draft', name: 'api_chapter_draft', methods: ['POST'])]
    public function saveDraft(int $id, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $chapter = $this->chapterRepository->find($id);
            if (!$chapter) {
                return $this->json([
                    'success' => false,
                    'error' => 'Chapitre non trouvé',
                ], Response::HTTP_NOT_FOUND);
            }

            // Mettre à jour les champs si fournis
            if (isset($data['titre'])) $chapter->setTitre($data['titre']);
            if (isset($data['description'])) $chapter->setDescription($data['description']);
            if (isset($data['imageUrl'])) $chapter->setImageUrl($data['imageUrl']);

            $chapter = $this->chapterService->saveDraft($chapter);

            return $this->json([
                'success' => true,
                'data' => [
                    'id' => $chapter->getId(),
                    'status' => $chapter->getStatus(),
                    'message' => 'Brouillon sauvegardé avec succès',
                ],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Supprime un chapitre
     * DELETE /api/chapters/{id}
     */
    #[Route('/{id}', name: 'api_chapter_delete', methods: ['DELETE'])]
    public function deleteChapter(int $id): JsonResponse
    {
        try {
            $chapter = $this->chapterRepository->find($id);
            if (!$chapter) {
                return $this->json([
                    'success' => false,
                    'error' => 'Chapitre non trouvé',
                ], Response::HTTP_NOT_FOUND);
            }

            $this->chapterService->deleteChapter($chapter);

            return $this->json([
                'success' => true,
                'message' => 'Chapitre supprimé avec succès',
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Récupère les langues disponibles pour la traduction
     * GET /api/chapters/languages/available
     */
    #[Route('/languages/available', name: 'api_languages_available', methods: ['GET'])]
    public function getAvailableLanguages(): JsonResponse
    {
        $languages = [
            'en' => 'English',
            'es' => 'Español',
            'de' => 'Deutsch',
            'it' => 'Italiano',
            'pt' => 'Português',
            'ru' => 'Русский',
            'ja' => 'Japanese',
            'zh' => '中文',
            'ar' => 'العربية',
        ];

        return $this->json([
            'success' => true,
            'data' => $languages,
        ]);
    }
}
