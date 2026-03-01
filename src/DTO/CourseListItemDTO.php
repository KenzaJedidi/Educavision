<?php

namespace App\DTO;

/**
 * DTO pour la liste des cours (sans relations circulaires)
 */
class CourseListItemDTO
{
    public function __construct(
        public int $id,
        public string $titre,
        public ?string $description,
        public ?float $price,
        public ?string $category,
        public ?string $imageUrl,
        public ?\DateTime $createdAt,
        public ?string $summary = null,
        public ?string $keywords = null,
        public int $views = 0,
        public int $likes = 0
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'description' => $this->description,
            'price' => $this->price,
            'category' => $this->category,
            'imageUrl' => $this->imageUrl,
            'createdAt' => $this->createdAt ? $this->createdAt->format('Y-m-d\TH:i:sP') : null,
            'summary' => $this->summary,
            'keywords' => $this->keywords,
            'views' => $this->views,
            'likes' => $this->likes,
        ];
    }
}
