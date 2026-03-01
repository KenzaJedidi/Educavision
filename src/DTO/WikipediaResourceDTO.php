<?php

namespace App\DTO;

/**
 * DTO pour une ressource Wikipedia complémentaire
 */
class WikipediaResourceDTO
{
    public function __construct(
        public string $title,
        public string $summary,
        public string $url,
        public string $source = 'Wikipedia',
        public float $relevanceScore = 1.0
    ) {}

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'summary' => $this->summary,
            'url' => $this->url,
            'source' => $this->source,
            'relevance_score' => $this->relevanceScore,
        ];
    }
}
