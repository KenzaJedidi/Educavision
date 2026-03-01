<?php

namespace App\DTO;

/**
 * DTO pour l'affichage d'un chapitre dans une liste
 */
class ChapterListItemDTO
{
    public function __construct(
        public int $id,
        public string $titre,
        public ?string $description,
        public string $status,
        public ?int $position,
        public ?string $difficultyLevel,
        public ?string $imageUrl,
        public ?\DateTime $createdAt,
        public ?\DateTime $updatedAt,
        public bool $hasEnrichedContent,
        public bool $hasOutline,
        public array $availableTranslations = [],
        public ?string $teacherName = null
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'description' => $this->description,
            'status' => $this->status,
            'position' => $this->position,
            'difficultyLevel' => $this->difficultyLevel,
            'imageUrl' => $this->imageUrl,
            'createdAt' => $this->createdAt?->format('Y-m-d\TH:i:sP'),
            'updatedAt' => $this->updatedAt?->format('Y-m-d\TH:i:sP'),
            'hasEnrichedContent' => $this->hasEnrichedContent,
            'hasOutline' => $this->hasOutline,
            'availableTranslations' => $this->availableTranslations,
            'teacherName' => $this->teacherName,
        ];
    }
}
