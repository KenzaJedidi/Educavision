<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Psr\Log\LoggerInterface;

/**
 * Service d'intégration OpenAI pour la génération de contenu cours
 * - Génération de résumés intelligents
 * - Extraction automatique de mots-clés
 * - Gestion du cache pour éviter les appels répétés
 * - Gestion du rate limiting (erreur 429) avec retry exponentiel
 */
class OpenAICourseService
{
    private string $apiKey;
    private string $apiUrl = 'https://api.openai.com/v1/chat/completions';
    private string $lastError = '';
    private const MAX_RETRIES = 5;
    private const RETRY_DELAY_MS = 10000; // 10 secondes de délai de base (augmenté pour respecter le rate limiting)

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private CacheInterface $cache,
        ?string $openaiApiKey = null
    ) {
        $this->apiKey = $openaiApiKey ?: ($_ENV['OPENAI_API_KEY'] ?? '');
    }

    /**
     * Vérifie si l'API OpenAI est configurée
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && $this->apiKey !== 'your_key_here';
    }

    /**
     * Retourne le dernier message d'erreur
     */
    public function getLastError(): string
    {
        return $this->lastError;
    }

    /**
     * Génère un résumé intelligent du cours basé sur titre et description
     * Utilise le cache pour éviter les appels répétés
     * Fallback automatique au service mock en cas d'erreur OpenAI
     * 
     * @return string|null Le résumé généré ou null en cas d'erreur
     */
    public function generateCourseSummary(string $title, string $description, string $category): ?string
    {
        $this->lastError = '';

        // Créer une clé de cache basée sur le contenu
        $cacheKey = $this->generateCacheKey('summary', $title, $description);

        try {
            // Vérifier le cache en premier
            $summary = $this->cache->get($cacheKey, function (ItemInterface $item) use ($title, $description, $category) {
                $item->expiresAfter(30 * 24 * 60 * 60); // 30 jours
                
                // Si OpenAI pas configurée, utiliser le mock directement
                if (!$this->isConfigured()) {
                    $this->logger->info("OpenAI non configurée, utilisation du service mock pour: {$title}");
                    return $this->generateMockSummary($title, $description, $category);
                }

                // Appeler l'API OpenAI
                $this->logger->info("Génération résumé OpenAI pour: {$title}");
                $summary = $this->callOpenAIWithRetry(
                    $this->buildSummaryPrompt($title, $description, $category),
                    'summary',
                    'Tu es un expert en contenu éducatif et tu dois créer des résumés concis et informatifs.',
                    $title,
                    $description,
                    $category
                );
                
                if ($summary) {
                    return $summary;
                }
                
                // Fallback au mock en cas d'erreur
                $this->logger->warning("Erreur OpenAI, fallback au mock pour: {$title}");
                return $this->generateMockSummary($title, $description, $category);
            });

            if ($summary) {
                $this->logger->info("Résumé cours généré avec succès pour: {$title}");
                return $summary;
            }
            
            return null;

        } catch (\Exception $e) {
            // En cas d'exception inévitable, utiliser le mock
            $this->logger->warning("Exception lors de la génération du résumé pour {$title}, utilisation du mock: " . $e->getMessage());
            $summary = $this->generateMockSummary($title, $description, $category);
            
            if ($summary) {
                try {
                    $this->cache->get($cacheKey, function (ItemInterface $item) use ($summary) {
                        $item->expiresAfter(30 * 24 * 60 * 60);
                        return $summary;
                    });
                } catch (\Exception $cacheEx) {
                    // Ignorer les erreurs de cache
                }
            }
            
            return $summary;
        }
    }

    /**
     * Génère automatiquement des mots-clés intelligents pour le cours
     * Utilise le cache pour éviter les appels répétés
     * Fallback automatique au service mock en cas d'erreur OpenAI
     * 
     * @return string|null Mots-clés séparés par des virgules ou null en cas d'erreur
     */
    public function generateKeywords(string $title, string $description, string $category): ?string
    {
        $this->lastError = '';

        // Créer une clé de cache basée sur le contenu
        $cacheKey = $this->generateCacheKey('keywords', $title, $description);

        try {
            // Vérifier le cache en premier
            $keywords = $this->cache->get($cacheKey, function (ItemInterface $item) use ($title, $description, $category) {
                $item->expiresAfter(30 * 24 * 60 * 60); // 30 jours
                
                // Si OpenAI pas configurée, utiliser le mock directement
                if (!$this->isConfigured()) {
                    $this->logger->info("OpenAI non configurée, utilisation du service mock pour: {$title}");
                    $keywords = $this->generateMockKeywords($title, $description, $category);
                    return $this->cleanKeywords($keywords);
                }

                // Appel API avec retry automatique pour l'erreur 429
                $keywords = $this->callOpenAIWithRetry(
                    $this->buildKeywordsPrompt($title, $description, $category),
                    'keywords',
                    'Tu es un expert en SEO et en classification de contenu éducatif.',
                    $title,
                    $description,
                    $category
                );

                if ($keywords) {
                    // Nettoyage des mots-clés
                    return $this->cleanKeywords($keywords);
                }
                
                // Fallback: utiliser le service mock si OpenAI échoue
                $this->logger->warning("Utilisation du service mock pour les mots-clés: {$title}");
                $keywords = $this->generateMockKeywords($title, $description, $category);
                if ($keywords) {
                    $this->lastError = ''; // Réinitialiser l'erreur après succès du fallback
                    return $this->cleanKeywords($keywords);
                }
                
                return null;
            });
            
            return $keywords;

        } catch (\Exception $e) {
            // En cas d'exception inévitable, utiliser le mock
            $this->logger->warning("Exception lors de la génération des mots-clés pour {$title}, utilisation du mock: " . $e->getMessage());
            $keywords = $this->generateMockKeywords($title, $description, $category);
            $keywords = $this->cleanKeywords($keywords);
            
            if ($keywords) {
                try {
                    $this->cache->get($cacheKey, function (ItemInterface $item) use ($keywords) {
                        $item->expiresAfter(30 * 24 * 60 * 60);
                        return $keywords;
                    });
                } catch (\Exception $cacheEx) {
                    // Ignorer les erreurs de cache
                }
            }
            
            return $keywords;
        }
    }

    /**
     * Appelle OpenAI avec retry automatique en cas d'erreur 429 (Too Many Requests)
     * Utilise un délai exponentiel pour éviter de surcharger l'API
     * En cas d'échec complet, retourne null (le fallback mock sera utilisé)
     */
    private function callOpenAIWithRetry(
        string $prompt, 
        string $type, 
        string $systemMessage,
        string $title = '',
        string $description = '',
        string $category = ''
    ): ?string {
        $attempt = 0;
        $totalWaitTime = 0;

        while ($attempt < self::MAX_RETRIES) {
            try {
                $temperature = $type === 'summary' ? 0.7 : 0.5;
                $maxTokens = $type === 'summary' ? 300 : 150;

                $response = $this->httpClient->request('POST', $this->apiUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'model' => 'gpt-4o-mini',
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => $systemMessage
                            ],
                            [
                                'role' => 'user',
                                'content' => $prompt
                            ]
                        ],
                        'temperature' => $temperature,
                        'max_tokens' => $maxTokens,
                    ],
                    'timeout' => 30, // Timeout de 30 secondes
                ]);

                if ($response->getStatusCode() === 200) {
                    $data = $response->toArray();
                    return trim($data['choices'][0]['message']['content']);
                }

                // Gestion spécifique de l'erreur 429
                if ($response->getStatusCode() === 429) {
                    $attempt++;
                    if ($attempt < self::MAX_RETRIES) {
                        // Délai exponentiel: 10s, 20s, 40s, 80s, 160s...
                        $delayMs = self::RETRY_DELAY_MS * (2 ** ($attempt - 1));
                        $delaySecs = $delayMs / 1000;
                        $totalWaitTime += $delaySecs;
                        
                        $this->logger->warning(
                            "Erreur 429 OpenAI ({$type}) - Tentative {$attempt}/" . self::MAX_RETRIES . 
                            ". Attente de {$delaySecs}s... (Temps total: {$totalWaitTime}s)"
                        );
                        usleep($delayMs * 1000); // Convertir ms en microsecondes
                        continue;
                    } else {
                        // Après trop de 429, utiliser le fallback
                        $this->logger->warning(
                            "OpenAI API bloquée (429) après " . self::MAX_RETRIES . 
                            " tentatives ({$totalWaitTime}s d'attente). Utilisation du fallback mock."
                        );
                        $this->lastError = "OpenAI temporairement indisponible (too many requests). Utilisation du contenu généré.";
                        return null;
                    }
                }

                // Autres erreurs HTTP
                $this->lastError = "OpenAI API erreur: Status {$response->getStatusCode()}";
                $this->logger->error($this->lastError);
                return null;

            } catch (\Exception $e) {
                $attempt++;
                if ($attempt < self::MAX_RETRIES) {
                    $delayMs = self::RETRY_DELAY_MS * (2 ** ($attempt - 1));
                    $delaySecs = $delayMs / 1000;
                    $totalWaitTime += $delaySecs;
                    
                    $this->logger->warning(
                        "Erreur OpenAI ({$type}): {$e->getMessage()} - Tentative {$attempt}/" . 
                        self::MAX_RETRIES . ". Attente de {$delaySecs}s..."
                    );
                    usleep($delayMs * 1000);
                } else {
                    $this->logger->warning(
                        "OpenAI indisponible après " . self::MAX_RETRIES . 
                        " tentatives ({$totalWaitTime}s d'attente). Utilisation du fallback mock."
                    );
                    $this->lastError = "OpenAI non disponible. Utilisation du contenu généré.";
                    return null;
                }
            }
        }

        return null;
    }

    /**
     * Génère un résumé avec le service mock
     */
    private function generateMockSummary(string $title, string $description, string $category): string
    {
        // Créer un résumé simple mais de qualité basé sur la description
        $words = array_filter(explode(' ', $description));
        $firstSentence = implode(' ', array_slice($words, 0, 15));
        
        return "Ce cours sur '{$title}' couvre les aspects essentiels de {$category}. " .
               trim($firstSentence, '.,!?;:') . ". " .
               "Vous apprendrez les concepts fondamentaux et les meilleures pratiques pour " .
               "maîtriser ce domaine. Ce cours est conçu pour tous les niveaux d'apprentissage.";
    }

    /**
     * Génère des mots-clés avec le service mock
     */
    private function generateMockKeywords(string $title, string $description, string $category): string
    {
        $keywords = [];
        
        // Ajouter la catégorie
        $keywords[] = strtolower($category);
        
        // Extraire les mots clés du titre
        $titleWords = array_filter(explode(' ', strtolower($title)));
        foreach ($titleWords as $word) {
            if (strlen($word) > 3 && !in_array($word, ['pour', 'avec', 'dans', 'vers'])) {
                $keywords[] = $word;
                if (count($keywords) >= 12) break;
            }
        }
        
        // Ajouter quelques mots clés génériques pertinents
        $genericKeywords = ['apprentissage', 'cours en ligne', 'formation', 'éducation', 'compétences'];
        foreach ($genericKeywords as $kw) {
            if (!in_array($kw, $keywords) && count($keywords) < 12) {
                $keywords[] = $kw;
            }
        }
        
        return implode(', ', array_slice($keywords, 0, 12));
    }

    /**
     * Génère une clé de cache basée sur le contenu
     */
    private function generateCacheKey(string $type, string $title, string $description): string
    {
        // Créer une clé de cache unique basée sur le type et le contenu
        $hash = md5($type . '|' . $title . '|' . $description);
        return "openai_{$type}_{$hash}";
    }

    /**
     * Construit le prompt pour la génération de résumé
     */
    private function buildSummaryPrompt(string $title, string $description, string $category): string
    {
        return <<<PROMPT
Génère un résumé court (150-200 mots) et engageant pour ce cours.

Titre du cours: $title
Catégorie: $category
Description fournie: $description

Exigences:
- Écris en français
- Sois concis et pédagogique
- Mets en avant les principaux apprentissages
- Rends le résumé attrayant pour les étudiants

Résumé:
PROMPT;
    }

    /**
     * Construit le prompt pour la génération de mots-clés
     */
    private function buildKeywordsPrompt(string $title, string $description, string $category): string
    {
        return <<<PROMPT
Génère 8-12 mots-clés pertinents et séparés par des virgules pour ce cours éducatif.

Titre du cours: $title
Catégorie: $category
Description: $description

Exigences:
- Retourne UNIQUEMENT les mots-clés séparés par des virgules
- Pas de numérotation, pas de formatage
- Mots-clés en minuscules (sauf noms propres)
- Aucun texte supplémentaire

Exemple format: python, programmation, développement web, backend, api rest

Réponse:
PROMPT;
    }

    /**
     * Nettoie et valide les mots-clés générés
     */
    private function cleanKeywords(string $keywords): string
    {
        // Supprime les caractères spéciaux et numérotation
        $keywords = preg_replace('/^[\d\.\-\s]+/', '', $keywords);
        $keywords = trim($keywords);

        // Divise et nettoie chaque mot-clé
        $keywordArray = array_map('trim', explode(',', $keywords));
        
        // Filtre les mots-clés vides et enlève caractères spéciaux
        $keywordArray = array_filter($keywordArray, function($keyword) {
            return !empty($keyword) && strlen($keyword) > 2;
        });

        // Limite à 15 mots-clés maximum
        $keywordArray = array_slice($keywordArray, 0, 15);

        return implode(',', $keywordArray);
    }
}
