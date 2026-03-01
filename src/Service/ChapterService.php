<?php

namespace App\Service;

use App\Entity\Chapter;
use App\Entity\Course;
use App\Repository\ChapterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ChapterService
{
    public function __construct(
        private ChapterRepository $chapterRepository,
        private ChapterAIService $aiService,
        private ChapterTranslationService $translationService,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {}

    /**
     * Crée un nouveau chapitre pour un cours
     */
    public function createChapter(
        Course $course,
        string $title,
        string $description,
        ?string $imageUrl = null,
        ?string $teacherName = null,
        ?string $teacherEmail = null
    ): Chapter {
        $chapter = new Chapter();
        $chapter->setCourse($course);
        $chapter->setTitre($title);
        $chapter->setDescription($description);
        $chapter->setImageUrl($imageUrl);
        $chapter->setTeacherName($teacherName);
        $chapter->setTeacherEmail($teacherEmail);
        $chapter->setStatus('draft');
        
        // Déterminer la position
        $maxPosition = $this->chapterRepository->findMaxPositionByCourse($course);
        $chapter->setPosition(($maxPosition ?? 0) + 1);

        $this->entityManager->persist($chapter);
        $this->entityManager->flush();

        $this->logger->info('Chapitre créé', [
            'chapter_id' => $chapter->getId(),
            'course_id' => $course->getId(),
        ]);

        return $chapter;
    }

    /**
     * Enrichit un chapitre avec IA
     */
    public function enrichChapter(Chapter $chapter): Chapter
    {
        $enrichedContent = $this->aiService->enrichChapterContent(
            $chapter->getTitre(),
            $chapter->getDescription(),
            $chapter->getCourse()?->getCategory() ?? ''
        );

        if ($enrichedContent) {
            $chapter->setEnrichedContent($enrichedContent);
            $chapter->setUpdatedAt(new \DateTime());
        }

        $this->entityManager->flush();
        return $chapter;
    }

    /**
     * Détecte le niveau de difficulté d'un chapitre
     */
    public function detectDifficultyLevel(Chapter $chapter): Chapter
    {
        $level = $this->aiService->detectDifficultyLevel(
            $chapter->getTitre(),
            $chapter->getDescription()
        );

        if ($level) {
            $chapter->setDifficultyLevel($level);
            $chapter->setUpdatedAt(new \DateTime());
            $this->entityManager->flush();
        }

        return $chapter;
    }

    /**
     * Génère un plan structuré pour le chapitre
     */
    public function generateOutline(Chapter $chapter): Chapter
    {
        $outline = $this->aiService->generateStructuredOutline(
            $chapter->getTitre(),
            $chapter->getDescription()
        );

        if ($outline) {
            $chapter->setStructuredOutline($outline);
            $chapter->setUpdatedAt(new \DateTime());
            $this->entityManager->flush();
        }

        return $chapter;
    }

    /**
     * Traduction complète du chapitre (titre + description + contenu enrichi)
     */
    public function translateChapter(Chapter $chapter, string $targetLanguage = 'en'): Chapter
    {
        $translations = $chapter->getTranslations() ?? [];

        // Traduire le titre
        $translatedTitle = $this->translationService->translateTitle(
            $chapter->getTitre(),
            $targetLanguage
        );

        // Traduire la description
        $translatedDescription = $this->translationService->translateContent(
            $chapter->getDescription() ?? '',
            $targetLanguage
        );

        // Traduire le contenu enrichi si disponible
        $translatedEnrichedContent = null;
        if ($chapter->getEnrichedContent()) {
            $translatedEnrichedContent = $this->translationService->translateContent(
                $chapter->getEnrichedContent(),
                $targetLanguage
            );
        }

        // Stocker les traductions
        $translations[$targetLanguage] = [
            'titre' => $translatedTitle,
            'description' => $translatedDescription,
            'enriched_content' => $translatedEnrichedContent,
        ];

        $chapter->setTranslations($translations);
        $chapter->setUpdatedAt(new \DateTime());
        $this->entityManager->flush();

        $this->logger->info('Chapitre traduit', [
            'chapter_id' => $chapter->getId(),
            'language' => $targetLanguage,
        ]);

        return $chapter;
    }

    /**
     * Réorganise les chapitres (drag & drop)
     */
    public function reorderChapters(Course $course, array $chapterPositions): void
    {
        // $chapterPositions = ['chapterId1' => 1, 'chapterId2' => 2, ...]
        
        foreach ($chapterPositions as $chapterId => $position) {
            $chapter = $this->chapterRepository->find($chapterId);
            
            if ($chapter && $chapter->getCourse()?->getId() === $course->getId()) {
                $chapter->setPosition((int)$position);
                $chapter->setUpdatedAt(new \DateTime());
            }
        }

        $this->entityManager->flush();

        $this->logger->info('Chapitres réorganisés', [
            'course_id' => $course->getId(),
        ]);
    }

    /**
     * Publie un chapitre
     */
    public function publishChapter(Chapter $chapter): Chapter
    {
        $chapter->setStatus('published');
        $chapter->setUpdatedAt(new \DateTime());
        $this->entityManager->flush();

        $this->logger->info('Chapitre publié', [
            'chapter_id' => $chapter->getId(),
        ]);

        return $chapter;
    }

    /**
     * Sauvegarde un brouillon
     */
    public function saveDraft(Chapter $chapter): Chapter
    {
        $chapter->setStatus('draft');
        $chapter->setUpdatedAt(new \DateTime());
        $this->entityManager->flush();

        return $chapter;
    }

    /**
     * Récupère tous les chapitres d'un cours (triés par position)
     */
    public function getChaptersByCourse(Course $course): array
    {
        return $this->chapterRepository->findByCourseOrdered($course);
    }

    /**
     * Supprime un chapitre
     */
    public function deleteChapter(Chapter $chapter): void
    {
        $this->entityManager->remove($chapter);
        $this->entityManager->flush();

        $this->logger->info('Chapitre supprimé', [
            'chapter_id' => $chapter->getId(),
        ]);
    }

    /**
     * Met à jour un chapitre
     */
    public function updateChapter(
        Chapter $chapter,
        ?string $title = null,
        ?string $description = null,
        ?string $imageUrl = null
    ): Chapter {
        if ($title) $chapter->setTitre($title);
        if ($description) $chapter->setDescription($description);
        if ($imageUrl !== null) $chapter->setImageUrl($imageUrl);
        
        $chapter->setUpdatedAt(new \DateTime());
        $this->entityManager->flush();

        return $chapter;
    }
}
