<?php

namespace App\Service\External;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Psr\Log\LoggerInterface;

class UnsplashService
{
    private HttpClientInterface $httpClient;
    private ParameterBagInterface $parameterBag;
    private string $accessKey;
    private LoggerInterface $logger;

    public function __construct(
        HttpClientInterface $httpClient,
        ParameterBagInterface $parameterBag,
        LoggerInterface $logger
    ) {
        $this->httpClient = $httpClient;
        $this->parameterBag = $parameterBag;
        $this->accessKey = $parameterBag->resolveValue('%unsplash_access_key%');
        $this->logger = $logger;

        // Vérifier si la clé est configurée
        if (!$this->accessKey || $this->accessKey === 'your_unsplash_access_key_here') {
            $this->logger->warning('Unsplash access key is not configured. Please set UNSPLASH_ACCESS_KEY in your .env file.');
        }
    }

    /**
     * Récupère une image aléatoire basée sur un mot-clé
     */
    public function getRandomImage(string $query, ?int $width = 800, ?int $height = 600): ?array
    {
        // Vérifier si la clé API est configurée
        if (!$this->accessKey || $this->accessKey === 'your_unsplash_access_key_here') {
            $this->logger->warning('Unsplash access key is not configured. Cannot fetch random image.');
            return null;
        }

        try {
            $response = $this->httpClient->request('GET', 'https://api.unsplash.com/photos/random', [
                'query' => [
                    'query' => $query,
                    'w' => $width,
                    'h' => $height,
                    'orientation' => 'landscape',
                    'content_filter' => 'high',
                ],
                'headers' => [
                    'Authorization' => 'Client-ID ' . $this->accessKey,
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                $data = $response->toArray();
                return [
                    'url' => $data['urls']['regular'],
                    'download_url' => $data['links']['download_location'],
                    'photographer' => $data['user']['name'],
                    'photographer_url' => $data['user']['links']['html'],
                    'description' => $data['description'] ?? $data['alt_description'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la récupération de l\'image Unsplash: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Recherche des images basées sur un mot-clé
     */
    public function searchImages(string $query, int $page = 1, int $perPage = 10): ?array
    {
        // Vérifier si la clé API est configurée
        if (!$this->accessKey || $this->accessKey === 'your_unsplash_access_key_here') {
            $this->logger->warning('Unsplash access key is not configured. Cannot search images.');
            return null;
        }

        try {
            $response = $this->httpClient->request('GET', 'https://api.unsplash.com/search/photos', [
                'query' => [
                    'query' => $query,
                    'page' => $page,
                    'per_page' => $perPage,
                    'orientation' => 'landscape',
                    'content_filter' => 'high',
                ],
                'headers' => [
                    'Authorization' => 'Client-ID ' . $this->accessKey,
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                $data = $response->toArray();
                $images = [];

                foreach ($data['results'] as $result) {
                    $images[] = [
                        'id' => $result['id'],
                        'url' => $result['urls']['regular'],
                        'download_url' => $result['links']['download_location'],
                        'photographer' => $result['user']['name'],
                        'photographer_url' => $result['user']['links']['html'],
                        'description' => $result['description'] ?? $result['alt_description'] ?? null,
                        'likes' => $result['likes'],
                    ];
                }

                return [
                    'images' => $images,
                    'total' => $data['total'],
                    'total_pages' => $data['total_pages'],
                ];
            }
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la recherche d\'images Unsplash: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Télécharge une image et la sauvegarde localement
     */
    public function downloadAndSaveImage(string $imageUrl, string $filename): ?string
    {
        try {
            $response = $this->httpClient->request('GET', $imageUrl);
            
            if ($response->getStatusCode() === 200) {
                $uploadDir = $this->parameterBag->resolveValue('%kernel.project_dir%') . '/public/uploads/courses/images/';
                
                // Créer le répertoire s'il n'existe pas
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $filepath = $uploadDir . $filename;
                file_put_contents($filepath, $response->getContent());
                
                return $filename;
            }
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors du téléchargement de l\'image: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Génère un nom de fichier unique pour l'image
     */
    public function generateImageFilename(string $title): string
    {
        $slug = strtolower(preg_replace('/[^a-z0-9]/', '-', $title));
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        return $slug . '-' . uniqid() . '.jpg';
    }

    /**
     * Extrait des mots-clés d'un titre pour la recherche d'images
     */
    public function extractKeywords(string $title): string
    {
        $words = explode(' ', strtolower($title));
        $stopWords = ['le', 'la', 'les', 'de', 'des', 'du', 'et', 'à', 'a', 'pour', 'dans', 'avec', 'sur', 'par', 'un', 'une', 'cours', 'formation', 'apprendre'];
        
        $keywords = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 2 && !in_array($word, $stopWords);
        });
        
        return implode(' ', array_slice($keywords, 0, 3));
    }
}
