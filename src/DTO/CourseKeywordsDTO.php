<?php

namespace App\DTO;

/**
 * DTO pour la réponse de génération de mots-clés IA
 */
class CourseKeywordsDTO
{
    public function __construct(
        public int $courseId,
        public bool $success,
        public ?string $keywords = null,
        public ?array $keywordsList = null,
        public ?string $error = null
    ) {}

    public function toArray(): array
    {
        return [
            'course_id' => $this->courseId,
            'success' => $this->success,
            'keywords' => $this->keywords,
            'keywords_list' => $this->keywordsList ?? ($this->keywords ? explode(',', $this->keywords) : []),
            'error' => $this->error,
        ];
    }
}
