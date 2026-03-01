<?php

namespace App\DTO;

/**
 * DTO pour la réponse de génération de résumé IA
 */
class CourseAISummaryDTO
{
    public function __construct(
        public int $courseId,
        public bool $success,
        public ?string $summary = null,
        public ?string $error = null
    ) {}

    public function toArray(): array
    {
        return [
            'course_id' => $this->courseId,
            'success' => $this->success,
            'summary' => $this->summary,
            'error' => $this->error,
        ];
    }
}
