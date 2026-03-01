<?php

namespace App\DTO;

class QuizGenerationDTO
{
    public function __construct(
        public int $quizId,
        public bool $success,
        public int $questionsGenerated,
        public ?string $difficulty = null,
        public ?string $error = null
    ) {}

    public function toArray(): array
    {
        return [
            'quizId' => $this->quizId,
            'success' => $this->success,
            'questionsGenerated' => $this->questionsGenerated,
            'difficulty' => $this->difficulty,
            'error' => $this->error,
        ];
    }
}
