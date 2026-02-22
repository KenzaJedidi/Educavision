<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

/**
 * Service d'Analyse de Profil Étudiant par Intelligence Artificielle.
 * Utilise l'API Gemini de Google (configurée) ou OpenAI GPT-4o-mini (si clé fournie).
 * 
 * Analyse le texte libre d'un étudiant et retourne :
 * - compétences détectées
 * - domaines recommandés
 * - niveau technique estimé
 */
class ProfileAnalysisService
{
    private string $openaiKey;
    private string $geminiKey;
    private string $lastError = '';

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        string $openaiApiKey = '',
        string $geminiApiKey = ''
    ) {
        $this->openaiKey  = $openaiApiKey  ?: ($_ENV['OPENAI_API_KEY']  ?? '');
        $this->geminiKey  = $geminiApiKey  ?: ($_ENV['GEMINI_API_KEY']  ?? '');
    }

    public function isConfigured(): bool
    {
        return !empty($this->openaiKey) || !empty($this->geminiKey);
    }

    public function getLastError(): string
    {
        return $this->lastError;
    }

    /**
     * Analyse le profil textuel d'un étudiant et retourne un tableau structuré.
     *
     * @return array{competences_detectees: string[], domaines_recommandes: string[], niveau_technique_estime: string}|null
     */
    public function analyserProfil(string $texteEtudiant): ?array
    {
        $this->lastError = '';

        $systemPrompt = <<<PROMPT
Tu es un conseiller d'orientation académique.
Analyse le texte fourni par l'étudiant.
Retourne uniquement un JSON valide avec :
- competences_detectees (liste)
- domaines_recommandes (liste)
- niveau_technique_estime (Débutant / Intermédiaire / Avancé)
Ne retourne aucun texte explicatif en dehors du JSON.
PROMPT;

        // Essai OpenAI en priorité, avec fallback automatique sur Gemini
        if (!empty($this->openaiKey)) {
            $result = $this->callOpenAI($systemPrompt, $texteEtudiant);
            if ($result !== null) {
                return $result;
            }
            // Si quota dépassé (429) ou autre erreur OpenAI → fallback Gemini
            if (!empty($this->geminiKey)) {
                $this->logger->warning('[ProfileAnalysisService] OpenAI failed, falling back to Gemini', [
                    'reason' => $this->lastError,
                ]);
                return $this->callGemini($systemPrompt, $texteEtudiant);
            }
            return null;
        }

        if (!empty($this->geminiKey)) {
            return $this->callGemini($systemPrompt, $texteEtudiant);
        }

        $this->lastError = 'Aucune clé API configurée (OPENAI_API_KEY ou GEMINI_API_KEY).';
        return null;
    }

    // -------------------------------------------------------------------------
    // OpenAI GPT-4o-mini
    // -------------------------------------------------------------------------
    private function callOpenAI(string $systemPrompt, string $userText): ?array
    {
        try {
            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->openaiKey,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'model'       => 'gpt-4o-mini',
                    'temperature' => 0.3,
                    'messages'    => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user',   'content' => $userText],
                    ],
                ],
                'timeout' => 30,
            ]);

            $data = $response->toArray();
            $raw  = $data['choices'][0]['message']['content'] ?? '';
            return $this->parseJsonResponse($raw);

        } catch (\Throwable $e) {
            $this->lastError = 'OpenAI : ' . $e->getMessage();
            $this->logger->error('[ProfileAnalysisService] OpenAI error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    // -------------------------------------------------------------------------
    // Google Gemini (fallback)
    // -------------------------------------------------------------------------
    private function callGemini(string $systemPrompt, string $userText): ?array
    {
        try {
            $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $this->geminiKey;

            $prompt = $systemPrompt . "\n\nTexte de l'étudiant :\n" . $userText;

            $response = $this->httpClient->request('POST', $url, [
                'headers' => ['Content-Type' => 'application/json'],
                'json'    => [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [
                        'temperature'     => 0.3,
                        'maxOutputTokens' => 1024,
                        'responseMimeType' => 'application/json',
                    ],
                ],
                'timeout' => 30,
            ]);

            $data = $response->toArray();
            $raw  = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            return $this->parseJsonResponse($raw);

        } catch (\Throwable $e) {
            $this->lastError = 'Gemini : ' . $e->getMessage();
            $this->logger->error('[ProfileAnalysisService] Gemini error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    // -------------------------------------------------------------------------
    // Parsing / nettoyage JSON
    // -------------------------------------------------------------------------
    private function parseJsonResponse(string $raw): ?array
    {
        // 1. Forcer l'encodage UTF-8 propre
        if (function_exists('mb_convert_encoding')) {
            $raw = mb_convert_encoding($raw, 'UTF-8', 'UTF-8');
        }

        // 2. Supprimer les blocs markdown ```json ... ```
        $cleaned = preg_replace('/```json\s*/i', '', $raw) ?? $raw;
        $cleaned = preg_replace('/```+/', '', $cleaned) ?? $cleaned;

        // 3. Remplacer tous les caractères de contrôle (y compris \r \n \t et bytes 0x00-0x1F, 0x7F)
        //    SAUF à l'intérieur des valeurs string JSON — on nettoie globalement d'abord
        $cleaned = preg_replace('/\r\n|\r|\n/', ' ', $cleaned) ?? $cleaned;
        $cleaned = preg_replace('/\t/', ' ', $cleaned) ?? $cleaned;
        // Supprimer tout caractère de contrôle restant (0x00-0x08, 0x0B-0x0C, 0x0E-0x1F, 0x7F)
        $cleaned = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $cleaned) ?? $cleaned;

        // 4. Extraire uniquement le bloc JSON { ... }
        if (preg_match('/\{.*\}/s', $cleaned, $m)) {
            $cleaned = $m[0];
        }

        $cleaned = trim($cleaned);

        // 5. Tentative de décodage
        $decoded = json_decode($cleaned, true);

        // 6. Si échec, second passage : supprimer tous les caractères non-ASCII safe
        if (!is_array($decoded)) {
            $cleaned2 = preg_replace('/[^\x20-\x7E\x{00A0}-\x{FFFD}]/u', '', $cleaned) ?? $cleaned;
            // Ré-extraire le JSON au cas où
            if (preg_match('/\{.*\}/s', $cleaned2, $m)) {
                $cleaned2 = $m[0];
            }
            $decoded = json_decode(trim($cleaned2), true);
        }

        if (!is_array($decoded)) {
            $this->lastError = 'Réponse IA non valide : ' . json_last_error_msg();
            $this->logger->warning('[ProfileAnalysisService] Invalid JSON after cleanup', [
                'json_error' => json_last_error_msg(),
                'raw_excerpt' => mb_substr($raw, 0, 300),
            ]);
            return null;
        }

        return [
            'competences_detectees'   => (array)($decoded['competences_detectees']   ?? []),
            'domaines_recommandes'    => (array)($decoded['domaines_recommandes']    ?? []),
            'niveau_technique_estime' => (string)($decoded['niveau_technique_estime'] ?? 'Inconnu'),
        ];
    }
}
