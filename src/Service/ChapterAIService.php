<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class ChapterAIService
{
    private string $geminiApiKey;
    private string $geminiModel = 'gemini-pro';
    private string $geminiEndpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';
    private bool $useMock = false; // Flag pour utiliser le service MOCK

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private ChapterAIMockService $mockService,
        string $geminiApiKey
    ) {
        $this->geminiApiKey = $geminiApiKey;
        // Si clé vide, utiliser le MOCK
        if (empty($geminiApiKey) || $geminiApiKey === 'none') {
            $this->useMock = true;
            $this->logger->info('🔄 ChapterAIService configuré en mode MOCK (pour développement)');
        }
    }

    /**
     * Enrichit le contenu d'un chapitre via IA (avec fallback MOCK)
     */
    public function enrichChapterContent(string $title, string $originalContent, string $category = ''): ?string
    {
        try {
            if ($this->useMock) {
                return $this->mockService->enrichChapterContent($title, $originalContent, $category);
            }
            
            $prompt = $this->buildEnrichmentPrompt($title, $originalContent, $category);
            $response = $this->callGemini($prompt);
            
            $this->logger->info('✅ Chapitre enrichi avec succès (Gemini)', [
                'title' => $title,
                'original_length' => strlen($originalContent),
            ]);

            return $response;
        } catch (\Exception $e) {
            $this->logger->warning('⚠️ Enrichissement Gemini échoué, utilisation du MOCK', [
                'error' => $e->getMessage(),
                'title' => $title,
            ]);
            // Fallback sur le service MOCK
            return $this->mockService->enrichChapterContent($title, $originalContent, $category);
        }
    }

    /**
     * Détecte automatiquement le niveau de difficulté (avec fallback MOCK)
     */
    public function detectDifficultyLevel(string $title, string $content): ?string
    {
        try {
            if ($this->useMock) {
                return $this->mockService->detectDifficultyLevel($title, $content);
            }
            
            $prompt = $this->buildDifficultyPrompt($title, $content);
            $response = $this->callGemini($prompt);
            
            // Extraire le niveau de la réponse
            $level = null;
            if (str_contains(strtolower($response), 'débutant')) {
                $level = 'débutant';
            } elseif (str_contains(strtolower($response), 'intermédiaire')) {
                $level = 'intermédiaire';
            } elseif (str_contains(strtolower($response), 'avancé')) {
                $level = 'avancé';
            }

            $this->logger->info('✅ Niveau détecté (Gemini)', [
                'title' => $title,
                'level' => $level,
            ]);

            return $level;
        } catch (\Exception $e) {
            $this->logger->warning('⚠️ Détection Gemini échouée, utilisation du MOCK', [
                'error' => $e->getMessage(),
            ]);
            // Fallback sur le service MOCK
            return $this->mockService->detectDifficultyLevel($title, $content);
        }
    }

    /**
     * Génère un plan structuré (outline) pour le chapitre (avec fallback MOCK)
     */
    public function generateStructuredOutline(string $title, string $content): ?string
    {
        try {
            if ($this->useMock) {
                return $this->mockService->generateStructuredOutline($title, $content);
            }
            
            $prompt = $this->buildOutlinePrompt($title, $content);
            $response = $this->callGemini($prompt);
            
            $this->logger->info('✅ Plan structuré généré (Gemini)', [
                'title' => $title,
            ]);

            return $response;
        } catch (\Exception $e) {
            $this->logger->warning('⚠️ Génération de plan Gemini échouée, utilisation du MOCK', [
                'error' => $e->getMessage(),
            ]);
            // Fallback sur le service MOCK
            return $this->mockService->generateStructuredOutline($title, $content);
        }
    }

    /**
     * Crée le prompt pour enrichissement
     */
    private function buildEnrichmentPrompt(string $title, string $content, string $category = ''): string
    {
        $categoryContext = $category ? "Catégorie: $category\n" : '';
        
        return <<<PROMPT
Tu es un expert en création de contenu éducatif. 

Titre du chapitre: $title
$categoryContext

Contenu original:
"$content"

Enrichis ce contenu en:
1. Clarifiant les concepts principaux
2. Ajoutant des exemples concrets et pertinents
3. Structurant mieux les idées avec des sous-titres
4. Ajoutant des notes importantes entre crochets [Important: ...]
5. Intégrant des conseils pratiques
6. Améliorant la lisibilité générale

Retourne uniquement le contenu enrichi, sans explications additionnelles.
PROMPT;
    }

    /**
     * Crée le prompt pour la détection du niveau
     */
    private function buildDifficultyPrompt(string $title, string $content): string
    {
        return <<<PROMPT
Analyse ce contenu éducatif et détermine son niveau de difficulté.

Titre: $title
Contenu: "$content"

Réponds UNIQUEMENT par l'un de ces mots:
- débutant (pour les novices, concepts de base, vocabulaire simple)
- intermédiaire (pour les apprenants avec base, concepts intermédiaires, vocabulaire technique modéré)
- avancé (pour les experts, concepts complexes, vocabulaire avancé)

Justifie brièvement ta réponse.
PROMPT;
    }

    /**
     * Crée le prompt pour la génération du plan
     */
    private function buildOutlinePrompt(string $title, string $content): string
    {
        return <<<PROMPT
Crée un plan structuré (outline) détaillé pour ce chapitre éducatif.

Titre: $title
Contenu: "$content"

Format le plan ainsi:
## Section 1
- Point 1
- Point 2
  - Sous-point 2.1
  - Sous-point 2.2

## Section 2
- Point 1
- Point 2

Et ainsi de suite...

Retourne uniquement le plan structuré, sans autres commentaires.
PROMPT;
    }

    /**
     * Appelle l'API Google Gemini
     */
    private function callGemini(string $prompt): string
    {
        try {
            $response = $this->httpClient->request('POST', $this->geminiEndpoint . '?key=' . $this->geminiApiKey, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 2000,
                    ]
                ],
                'timeout' => 30,
            ]);

            $data = $response->toArray();

            // Vérifier erreurs API
            if (isset($data['error'])) {
                $this->logger->error('Erreur Gemini API', ['error' => $data['error']]);
                throw new \Exception('Erreur Gemini: ' . json_encode($data['error']));
            }

            if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                $this->logger->error('Structure réponse Gemini invalide', ['response' => $data]);
                throw new \Exception('Réponse Gemini invalide');
            }

            return trim($data['candidates'][0]['content']['parts'][0]['text']);
        } catch (\Exception $e) {
            $this->logger->error('Erreur appel Gemini', [
                'error' => $e->getMessage(),
                'endpoint' => $this->geminiEndpoint
            ]);
            throw $e;
        }
    }
}
