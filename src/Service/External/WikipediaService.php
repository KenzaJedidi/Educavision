<?php

namespace App\Service\External;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class WikipediaService
{
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;

    public function __construct(
        HttpClientInterface $httpClient,
        LoggerInterface $logger
    ) {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    /**
     * Récupère un résumé Wikipedia basé sur un terme de recherche
     */
    public function getSummary(string $query, string $language = 'fr'): ?array
    {
        try {
            // Recherche d'articles
            $searchResponse = $this->httpClient->request('GET', 'https://' . $language . '.wikipedia.org/w/api.php', [
                'query' => [
                    'action' => 'query',
                    'list' => 'search',
                    'srsearch' => $query,
                    'format' => 'json',
                    'srlimit' => 1,
                    'srprop' => 'snippet|titlesnippet',
                ],
            ]);

            if ($searchResponse->getStatusCode() === 200) {
                $searchData = $searchResponse->toArray();
                
                if (!empty($searchData['query']['search'])) {
                    $firstResult = $searchData['query']['search'][0];
                    $title = $firstResult['title'];
                    
                    // Récupération du contenu complet de l'article
                    $contentResponse = $this->httpClient->request('GET', 'https://' . $language . '.wikipedia.org/w/api.php', [
                        'query' => [
                            'action' => 'query',
                            'prop' => 'extracts|info|pageimages',
                            'exintro' => true,
                            'explaintext' => true,
                            'inprop' => 'url',
                            'pithumbsize' => 300,
                            'titles' => $title,
                            'format' => 'json',
                            'redirects' => true,
                        ],
                    ]);

                    if ($contentResponse->getStatusCode() === 200) {
                        $contentData = $contentResponse->toArray();
                        $pages = $contentData['query']['pages'];
                        
                        if (!empty($pages)) {
                            $page = reset($pages);
                            
                            return [
                                'title' => $page['title'] ?? null,
                                'summary' => $page['extract'] ?? null,
                                'url' => $page['fullurl'] ?? null,
                                'thumbnail' => $page['thumbnail']['source'] ?? null,
                                'pageid' => $page['pageid'] ?? null,
                            ];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la récupération du résumé Wikipedia: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Recherche des articles Wikipedia liés à un sujet
     */
    public function searchArticles(string $query, string $language = 'fr', int $limit = 5): ?array
    {
        try {
            $response = $this->httpClient->request('GET', 'https://' . $language . '.wikipedia.org/w/api.php', [
                'query' => [
                    'action' => 'query',
                    'list' => 'search',
                    'srsearch' => $query,
                    'format' => 'json',
                    'srlimit' => $limit,
                    'srprop' => 'snippet|titlesnippet|size|wordcount|timestamp',
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                $data = $response->toArray();
                $articles = [];

                if (!empty($data['query']['search'])) {
                    foreach ($data['query']['search'] as $result) {
                        $articles[] = [
                            'title' => $result['title'],
                            'snippet' => $result['snippet'],
                            'title_snippet' => $result['titlesnippet'],
                            'size' => $result['size'],
                            'wordcount' => $result['wordcount'],
                            'timestamp' => $result['timestamp'],
                            'pageid' => $result['pageid'],
                        ];
                    }
                }

                return $articles;
            }
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la recherche d\'articles Wikipedia: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Récupère le contenu complet d'un article Wikipedia par son ID
     */
    public function getArticleContent(int $pageid, string $language = 'fr'): ?array
    {
        try {
            $response = $this->httpClient->request('GET', 'https://' . $language . '.wikipedia.org/w/api.php', [
                'query' => [
                    'action' => 'query',
                    'prop' => 'extracts|info|pageimages|categories',
                    'exintro' => false,
                    'explaintext' => true,
                    'inprop' => 'url|displaytitle',
                    'pithumbsize' => 500,
                    'pageids' => $pageid,
                    'format' => 'json',
                    'cllimit' => 10,
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                $data = $response->toArray();
                $pages = $data['query']['pages'];
                
                if (!empty($pages)) {
                    $page = reset($pages);
                    
                    return [
                        'title' => $page['title'] ?? null,
                        'displaytitle' => $page['displaytitle'] ?? null,
                        'content' => $page['extract'] ?? null,
                        'url' => $page['fullurl'] ?? null,
                        'thumbnail' => $page['thumbnail']['source'] ?? null,
                        'categories' => $this->extractCategories($page['categories'] ?? []),
                        'lastmodified' => $page['touched'] ?? null,
                        'length' => $page['length'] ?? null,
                    ];
                }
            }
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la récupération du contenu Wikipedia: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Extrait les noms des catégories (supprime le préfixe)
     */
    private function extractCategories(array $categories): array
    {
        $cleanCategories = [];
        foreach ($categories as $category) {
            if (isset($category['title'])) {
                // Supprime le préfixe "Catégorie:"
                $cleanCategories[] = str_replace('Catégorie:', '', $category['title']);
            }
        }
        return $cleanCategories;
    }

    /**
     * Nettoie et formate le résumé pour l'affichage
     */
    public function formatSummary(string $summary, int $maxLength = 500): string
    {
        // Supprime les références entre crochets [1], [2], etc.
        $summary = preg_replace('/\[\d+\]/', '', $summary);
        
        // Supprime les caractères spéciaux non désirés
        $summary = preg_replace('/\s+/', ' ', $summary);
        
        // Limite la longueur
        if (strlen($summary) > $maxLength) {
            $summary = substr($summary, 0, $maxLength);
            $lastSpace = strrpos($summary, ' ');
            if ($lastSpace !== false) {
                $summary = substr($summary, 0, $lastSpace);
            }
            $summary .= '...';
        }
        
        return trim($summary);
    }

    /**
     * Génère des mots-clés à partir du titre et du résumé
     */
    public function extractKeywords(string $title, string $summary): array
    {
        $text = strtolower($title . ' ' . $summary);
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);
        $words = explode(' ', $text);
        
        $stopWords = [
            'le', 'la', 'les', 'de', 'des', 'du', 'et', 'à', 'a', 'pour', 'dans', 'avec', 'sur', 'par',
            'que', 'qui', 'quoi', 'où', 'quand', 'comment', 'pourquoi', 'ce', 'cette', 'ces', 'cet',
            'une', 'un', 'vos', 'votre', 'nos', 'notre', 'leur', 'leurs', 'tout', 'tous', 'toute', 'toutes',
            'est', 'sont', 'été', 'être', 'avoir', 'plus', 'moins', 'très', 'bien', 'aussi', 'comme',
            'entre', 'contre', 'depuis', 'pendant', 'vers', 'jusqu', 'cela', 'celui', 'celle', 'ceux'
        ];
        
        $keywords = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 2 && !in_array($word, $stopWords);
        });
        
        $keywords = array_count_values($keywords);
        arsort($keywords);
        
        return array_slice(array_keys($keywords), 0, 10);
    }

    /**
     * Vérifie si un sujet est pertinent pour une recherche Wikipedia
     */
    public function isRelevantSubject(string $subject): bool
    {
        $irrelevantSubjects = ['cours', 'formation', 'apprentissage', 'leçon', 'tutoriel', 'guide'];
        $subjectLower = strtolower($subject);
        
        foreach ($irrelevantSubjects as $irrelevant) {
            if (strpos($subjectLower, $irrelevant) !== false) {
                return false;
            }
        }
        
        return true;
    }
}
