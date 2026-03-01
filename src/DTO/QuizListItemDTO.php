<?php

namespace App\DTO;

class QuizListItemDTO
{
    public function __construct(
        public int $id,
        public string $titre,
        public ?string $description = null,
        public string $status = 'draft',
        public ?string $difficultyLevel = null,
        public int $numberOfQuestions = 0,
        public int $timeLimit = 0,
        public bool $visible = false
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'description' => $this->description,
            'status' => $this->status,
            'difficultyLevel' => $this->difficultyLevel,
            'numberOfQuestions' => $this->numberOfQuestions,
            'timeLimit' => $this->timeLimit,
            'visible' => $this->visible,
        ];
    }
}
