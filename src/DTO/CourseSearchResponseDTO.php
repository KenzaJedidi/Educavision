<?php

namespace App\DTO;

/**
 * DTO pour la réponse de recherche paginée de cours
 */
class CourseSearchResponseDTO
{
    /**
     * @param array $items Courses
     * @param int $total Nombre total de résultats
     * @param int $page Page actuelle
     * @param int $limit Éléments par page
     * @param int $pages Nombre de pages
     */
    public function __construct(
        public array $items,
        public int $total,
        public int $page,
        public int $limit,
        public int $pages
    ) {}

    public function toArray(): array
    {
        return [
            'data' => $this->items,
            'pagination' => [
                'total' => $this->total,
                'page' => $this->page,
                'limit' => $this->limit,
                'pages' => $this->pages,
            ],
        ];
    }
}
