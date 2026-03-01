<?php

namespace App\DTO;

/**
 * DTO pour la réponse d'une ressource/article Wikipedia
 */
class ComplementaryResourcesDTO
{
    /**
     * @param bool $success Succès de la requête
     * @param array $articles Articles Wikipedia trouvés
     * @param int $count Nombre d'articles trouvés
     * @param string|null $error Message d'erreur si applicable
     */
    public function __construct(
        public bool $success,
        public array $articles = [],
        public int $count = 0,
        public ?string $error = null
    ) {}

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'count' => $this->count,
            'articles' => $this->articles,
            'error' => $this->error,
        ];
    }
}
