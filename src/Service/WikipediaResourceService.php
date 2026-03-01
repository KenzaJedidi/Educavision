<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

/**
 * Service d'intégration Wikipedia API pour la récupération de ressources complémentaires
 * 
 * Permet de récupérer des articles et informations complémentaires basées sur
 * le titre du cours et les mots-clés
 */
class WikipediaResourceService
{
    private string $apiUrl = 'https://fr.wikipedia.org/w/api.php';
    private string $lastError = '';

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger
    ) {}

    /**
     * Retourne le dernier message d'erreur
     */
    public function getLastError(): string
    {
        return $this->lastError;
    }

    /**
     * Récupère les ressources Wikipedia complémentaires pour un cours
     * 
     * @return array{articles: array, summary: string|null} Articles et résumé trouvés
     */
    public function getComplementaryResources(string $courseTitle, ?string $keywords = null): array
    {
        $this->lastError = '';
        $articles = [];

        try {
            // Cherche d'abord par le titre principal
            $articles = $this->searchWikipediaArticles($courseTitle);

            // Si mots-clés fournis, enrichit la recherche
            if (!empty($keywords) && count($articles) < 3) {
                $keywordArray = array_slice(
                    array_map('trim', explode(',', $keywords)),
                    0,
                    3
                );
                
                foreach ($keywordArray as $keyword) {
                    if (count($articles) < 5) {
                        $moreArticles = $this->searchWikipediaArticles($keyword, 2);
                        $articles = array_merge($articles, $moreArticles);
                    }
                }
            }

            // Déduplique et limite à 5 articles
            $articles = $this->deduplicateArticles($articles);
            $articles = array_slice($articles, 0, 5);

            return [
                'articles' => $articles,
                'count' => count($articles),
            ];

        } catch (\Exception $e) {
            $this->lastError = "Erreur lors de la récupération Wikipedia: " . $e->getMessage();
            $this->logger->error($this->lastError);
            
            return [
                'articles' => [],
                'count' => 0,
            ];
        }
    }

    /**
     * Recherche des articles Wikipedia par mot-clé
     * 
     * @param string $query Terme à rechercher
     * @param int $limit Nombre maximum de résultats
     * @return array Liste des articles trouvés
     */
    private function searchWikipediaArticles(string $query, int $limit = 3): array
    {
        try {
            $response = $this->httpClient->request('GET', $this->apiUrl, [
                'query' => [
                    'action' => 'query',
                    'list' => 'search',
                    'srsearch' => $query,
                    'srnamespace' => 0,
                    'srlimit' => $limit,
                    'srwhat' => 'text',
                    'format' => 'json',
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                return [];
            }

            $data = $response->toArray();
            $results = [];

            if (isset($data['query']['search'])) {
                foreach ($data['query']['search'] as $item) {
                    $pageTitle = $item['title'];
                    $summary = $this->extractSummary($item['snippet']);
                    
                    // Récupère le URL complet de l'article
                    $articleUrl = "https://fr.wikipedia.org/wiki/" . urlencode($pageTitle);
                    
                    $results[] = [
                        'title' => $pageTitle,
                        'summary' => $summary,
                        'url' => $articleUrl,
                        'source' => 'Wikipedia',
                        'relevance_score' => $this->calculateRelevance($item),
                    ];
                }
            }

            return $results;

        } catch (\Exception $e) {
            $this->logger->warning("Erreur recherche Wikipedia pour '{$query}': " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère un résumé d'un article Wikipedia
     * 
     * @return string|null Paragraphe d'ouverture ou null
     */
    public function getArticleExtract(string $pageTitle): ?string
    {
        try {
            $response = $this->httpClient->request('GET', $this->apiUrl, [
                'query' => [
                    'action' => 'query',
                    'titles' => $pageTitle,
                    'prop' => 'extracts',
                    'explaintext' => true,
                    'exintro' => true,
                    'exlimit' => 1,
                    'format' => 'json',
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                return null;
            }

            $data = $response->toArray();
            
            if (isset($data['query']['pages'])) {
                $pages = array_values($data['query']['pages']);
                if (isset($pages[0]['extract'])) {
                    return $this->truncateText($pages[0]['extract'], 500);
                }
            }

            return null;

        } catch (\Exception $e) {
            $this->logger->warning("Erreur extraction article Wikipedia: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupère la langue suggérée au vu du contenu
     * Utile pour adapter la recherche Wikipedia
     * 
     * @return string Code ISO langue (ex: 'fr', 'en')
     */
    public function getContentLanguage(string $title, string $description): string
    {
        // Analyse basique : détecte si contenu en français
        $textContent = $title . ' ' . $description;
        
        // Mots français courants
        $frenchIndicators = ['le', 'la', 'de', 'que', 'est', 'cours', 'pour', 'les', 'dans', 'sur'];
        
        $frenchCount = 0;
        foreach ($frenchIndicators as $word) {
            if (preg_match('/\b' . $word . '\b/i', $textContent)) {
                $frenchCount++;
            }
        }

        return $frenchCount > 3 ? 'fr' : 'en';
    }

    /**
     * Extrait le résumé en supprimant les balises HTML
     */
    private function extractSummary(string $snippet): string
    {
        // Supprime les balises HTML et les entités
        $summary = strip_tags($snippet);
        $summary = html_entity_decode($summary, ENT_QUOTES, 'UTF-8');
        
        // Limite à 200 caractères
        return $this->truncateText($summary, 200);
    }

    /**
     * Tronque du texte intelligemment selon le nombre de caractères
     */
    private function truncateText(string $text, int $maxLength): string
    {
        if (strlen($text) <= $maxLength) {
            return $text;
        }

        $truncated = substr($text, 0, $maxLength);
        $lastSpace = strrpos($truncated, ' ');
        
        if ($lastSpace !== false) {
            $truncated = substr($truncated, 0, $lastSpace);
        }

        return trim($truncated) . '...';
    }

    /**
     * Déduplique les articles basé sur le titre
     */
    private function deduplicateArticles(array $articles): array
    {
        $seen = [];
        $result = [];

        foreach ($articles as $article) {
            $titleLower = strtolower($article['title']);
            
            if (!isset($seen[$titleLower])) {
                $seen[$titleLower] = true;
                $result[] = $article;
            }
        }

        return $result;
    }

    /**
     * Calcule un score de pertinence pour un résultat
     */
    private function calculateRelevance(array $item): float
    {
        $relevance = 1.0;

        // Bonus si le terme est dans le titre
        if (isset($item['title'])) {
            $relevance += 0.5;
        }

        // Bonus si snippet contient beaucoup de texte
        if (isset($item['snippet']) && strlen($item['snippet']) > 150) {
            $relevance += 0.3;
        }

        return min($relevance, 2.0);
    }
}
