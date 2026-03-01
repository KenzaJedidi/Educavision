<?php

namespace App\DTO;

/**
 * DTO pour la réponse d'enrichissement IA d'un chapitre
 */
class ChapterEnrichmentDTO
{
    public function __construct(
        public int $chapterId,
        public bool $success,
        public ?string $enrichedContent = null,
        ?string $difficultyLevel = null,
        ?string $structuredOutline = null,
        ?string $error = null
    ) {
        $this->difficultyLevel = $difficultyLevel;
        $this->structuredOutline = $structuredOutline;
        $this->error = $error;
    }

    public ?string $difficultyLevel;
    public ?string $structuredOutline;
    public ?string $error;

    public function toArray(): array
    {
        return [
            'chapterId' => $this->chapterId,
            'success' => $this->success,
            'enrichedContent' => $this->enrichedContent,
            'difficultyLevel' => $this->difficultyLevel,
            'structuredOutline' => $this->structuredOutline,
            'error' => $this->error,
        ];
    }
}
