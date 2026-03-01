<?php

namespace App\DTO;

/**
 * DTO pour la traduction d'un chapitre
 */
class ChapterTranslationDTO
{
    public function __construct(
        public int $chapterId,
        public bool $success,
        public string $language,
        public ?array $translations = null,
        ?string $error = null
    ) {
        $this->error = $error;
    }

    public ?string $error;

    public function toArray(): array
    {
        return [
            'chapterId' => $this->chapterId,
            'success' => $this->success,
            'language' => $this->language,
            'translations' => $this->translations,
            'error' => $this->error,
        ];
    }
}
