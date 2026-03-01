<?php

namespace App\Service\AI;

use Psr\Log\LoggerInterface;

/**
 * Service d'appel à l'API DeepSeek pour la génération de contenu.
 * Compatible avec le format OpenAI (chat completions).
 * Documentation: https://platform.deepseek.com/docs
 */
class DeepSeekApiService
{
    private const BASE_URL = 'https://api.deepseek.com';
    private const MODEL = 'deepseek-chat';

    public function __construct(
        private ?string $apiKey = null,
        private ?LoggerInterface $logger = null
    ) {
        $this->apiKey = $apiKey ?? ($_ENV['DEEPSEEK_API_KEY'] ?? '');
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Appelle l'API DeepSeek Chat Completions (format OpenAI).
     */
    public function chat(array $messages, array $options = []): ?string
    {
        if (!$this->isConfigured()) {
            $this->logger?->warning('DeepSeek API: DEEPSEEK_API_KEY non configurée.');
            return null;
        }

        $payload = [
            'model' => $options['model'] ?? self::MODEL,
            'messages' => $messages,
            'stream' => false,
            'temperature' => $options['temperature'] ?? 0.7,
            'max_tokens' => $options['max_tokens'] ?? 4096,
        ];

        $timeout = (int) ($options['timeout'] ?? 150);
        $connectTimeout = min(15, (int) ($options['connect_timeout'] ?? 20));

        $ch = curl_init(self::BASE_URL . '/chat/completions');
        $opts = [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => $connectTimeout,
        ];
        // En dev local (Windows/XAMPP), éviter les erreurs SSL si DEEPSEEK_SKIP_SSL_VERIFY=1
        $skipSsl = $_ENV['DEEPSEEK_SKIP_SSL_VERIFY'] ?? null;
        if (($_ENV['APP_ENV'] ?? '') === 'dev' && ($skipSsl === '1' || $skipSsl === 'true')) {
            $opts[CURLOPT_SSL_VERIFYPEER] = false;
            $opts[CURLOPT_SSL_VERIFYHOST] = 0;
        }
        curl_setopt_array($ch, $opts);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            $this->logger?->error('DeepSeek API curl error: ' . $error);
            return null;
        }

        if ($httpCode !== 200) {
            $this->logger?->error('DeepSeek API HTTP ' . $httpCode . ': ' . substr($response, 0, 500));
            return null;
        }

        $data = json_decode($response, true);
        $content = $data['choices'][0]['message']['content'] ?? null;

        if (!$content) {
            $this->logger?->error('DeepSeek API: réponse invalide', ['response' => substr($response, 0, 500)]);
            return null;
        }

        return trim($content);
    }

    /**
     * Génère un résumé pédagogique pour un cours via DeepSeek.
     * Utilisé par les étudiants lorsqu'ils consultent un cours.
     */
    public function generateCourseSummary(string $title, string $description, string $category, ?string $chaptersContent = null): ?string
    {
        if (!$this->isConfigured()) {
            $this->logger?->warning('DeepSeek API: DEEPSEEK_API_KEY non configurée.');
            return null;
        }

        $contentPart = '';
        if (!empty($chaptersContent)) {
            // Le contenu peut être le PDF du cours ou les chapitres (déjà limité côté appelant)
            $text = mb_strlen($chaptersContent) > 30_000
                ? mb_substr($chaptersContent, 0, 30_000) . "\n[...]"
                : $chaptersContent;
            $contentPart = "\n\n" . $text;
        }

        $userPrompt = <<<PROMPT
Génère un résumé court (150-250 mots) et engageant pour ce cours éducatif.

**Titre du cours :** {$title}
**Catégorie :** {$category}
**Description :** {$description}
{$contentPart}

Exigences :
- Écris en français
- Sois concis et pédagogique
- Mets en avant les principaux apprentissages et notions clés
- Rends le résumé attrayant pour les étudiants
- Ne mets pas de titre "Résumé" ou autre, réponds directement par le texte du résumé

Résumé :
PROMPT;

        $content = $this->chat([
            ['role' => 'system', 'content' => 'Tu es un assistant pédagogique expert. Tu génères des résumés de cours clairs et utiles pour les étudiants. Tu réponds uniquement avec le texte du résumé, sans introduction ni conclusion.'],
            ['role' => 'user', 'content' => $userPrompt]
        ], ['temperature' => 0.6, 'max_tokens' => 500]);

        return $content ?: null;
    }

    /**
     * Génère des mots-clés pour un cours via DeepSeek.
     */
    public function generateCourseKeywords(string $title, string $description, string $category): ?string
    {
        if (!$this->isConfigured()) {
            return null;
        }

        $desc = mb_strlen($description) > 600 ? mb_substr($description, 0, 600) . '...' : $description;

        $userPrompt = <<<PROMPT
Génère 8-12 mots-clés pertinents séparés par des virgules pour ce cours éducatif.

Titre: {$title}
Catégorie: {$category}
Description: {$desc}

Exigences:
- Retourne UNIQUEMENT les mots-clés séparés par des virgules
- Pas de numérotation, pas de texte supplémentaire
- Mots en minuscules (sauf noms propres)

Exemple: python, programmation, développement web, api rest

Réponse:
PROMPT;

        $content = $this->chat([
            ['role' => 'system', 'content' => 'Tu es un expert SEO. Réponds uniquement par les mots-clés, rien d\'autre.'],
            ['role' => 'user', 'content' => $userPrompt]
        ], ['temperature' => 0.3, 'max_tokens' => 200, 'timeout' => 60, 'connect_timeout' => 15]);

        if (!$content) {
            return null;
        }

        $content = preg_replace('/^[\d\.\-\s]+/', '', $content);
        return trim($content);
    }

    /**
     * Génère des questions de quiz à partir d'un texte via DeepSeek.
     * Retourne un tableau de questions au format attendu par le système.
     */
    public function generateQuizQuestions(
        string $sourceText,
        int $questionCount,
        string $difficulty,
        bool $includeQCM = true,
        bool $includeTrueFalse = true,
        bool $includeOpen = false
    ): array {
        $sourceText = $this->prepareSourceText($sourceText);

        $diffLabel = match ($difficulty) {
            'easy' => 'facile',
            'medium' => 'moyenne',
            'hard' => 'difficile',
            default => 'moyenne'
        };

        $types = [];
        if ($includeQCM) $types[] = 'QCM (questions à choix multiples, 4 propositions, 1 seule correcte)';
        if ($includeTrueFalse) $types[] = 'Vrai/Faux';
        if ($includeOpen) $types[] = 'Question ouverte (avec réponse attendue)';

        $typesStr = implode(', ', $types);
        $points = match ($difficulty) {
            'easy' => 5,
            'medium' => 10,
            'hard' => 15,
            default => 10
        };

        $systemPrompt = <<<SYS
Tu es un expert pédagogique et rédacteur créant des questions de quiz. Tu écris en français parfait.

EXIGENCES OBLIGATOIRES :

1. ORTHOGRAPHE ET GRAMMAIRE : Zéro faute. Accents (é, è, ê, à, ù, ç), accords (singulier/pluriel, masculin/féminin), conjugaisons correctes. Relis chaque phrase mentalement avant de l'écrire.

2. CLARTÉ : Chaque question doit être compréhensible au premier coup d'œil : une seule idée, phrase courte et directe, sans ambiguïté. Commencer par "D'après le cours," ou "Selon le document," pour ancrer le contexte.

3. QCM : La réponse ne doit JAMAIS être déductible de la question. L'étudiant doit avoir lu le cours pour répondre. Interdit : répéter un extrait en question et en réponse, ou formuler une question dont la réponse est évidente. Ex. : "D'après le cours, quelle est la définition de [terme] ?" (la réponse apporte la définition), "Selon le document, quelle est la première étape pour [X] ?" (il faut avoir lu pour savoir).

4. VRAI/FAUX : Une seule affirmation testable. Pas de double négation.

5. Réponds UNIQUEMENT avec un tableau JSON valide. Aucun texte avant ou après.
SYS;

        $userPrompt = <<<PROMPT
## DOCUMENT (cours du professeur)

Analyse ce document. Identifie les notions clés, définitions, exemples, étapes et faits importants.

---
{$sourceText}
---

## MISSION
Génère EXACTEMENT {$questionCount} questions de quiz qui ont du SENS et évaluent la compréhension de ce document.

### Types
{$typesStr}

### Difficulté
{$diffLabel}

### QUALITÉ OBLIGATOIRE
- LA RÉPONSE NE DOIT PAS ÊTRE DANS LA QUESTION : l'étudiant doit avoir lu le cours. Pas de répétition question/réponse.
- ORTHOGRAPHE PARFAITE : aucune faute. Vérifie accents et accords.
- CLARTÉ : une seule idée par question, phrase courte, compréhensible immédiatement.
- Chaque réponse (QCM) = affirmation concrète, correcte grammaticalement.
- Questions basées sur des informations PRÉCISES du document.
- Réponds UNIQUEMENT avec le tableau JSON. Pas de \`\`\`json, pas d'explication.

### Format JSON
[
  {"type": "qcm", "text": "Question claire avec sens (ex: D'après le cours, quelle est la définition de X ?)", "points": {$points}, "answers": [{"text": "Réponse correcte (citation ou reformulation du document)", "isCorrect": true}, {"text": "Fausse réponse plausible", "isCorrect": false}, {"text": "Fausse réponse plausible", "isCorrect": false}, {"text": "Fausse réponse plausible", "isCorrect": false}]},
  {"type": "true_false", "text": "Affirmation précise tirée du document (ex: Le document indique que...)", "points": 5, "answers": [{"text": "Vrai", "isCorrect": true}, {"text": "Faux", "isCorrect": false}]}
]
PROMPT;

        $this->logger?->info('DeepSeek API: génération de quiz démarrée', ['questions' => $questionCount, 'chars' => mb_strlen($sourceText)]);

        $start = microtime(true);
        $content = $this->chat([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt]
        ], ['temperature' => 0.2, 'max_tokens' => 8192]);
        $elapsed = round(microtime(true) - $start, 2);

        if (!$content) {
            $this->logger?->error('DeepSeek API: génération échouée', ['elapsed' => $elapsed]);
            return [];
        }

        $this->logger?->info('DeepSeek API: génération terminée', ['elapsed' => $elapsed . 's', 'response_length' => strlen($content)]);

        // Nettoyer d'éventuels markdown ou texte parasite
        if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/', $content, $m)) {
            $content = $m[1];
        }
        $content = preg_replace('/^[\s\S]*?\[/', '[', $content);
        $content = preg_replace('/\][\s\S]*$/', ']', $content);
        $content = trim($content);

        $questions = json_decode($content, true);
        if (!is_array($questions)) {
            $this->logger?->error('DeepSeek: impossible de parser le JSON', ['content' => substr($content, 0, 500)]);
            return [];
        }

        // Valider et normaliser chaque question (rejeter celles qui n'ont pas de sens)
        $validQuestions = [];
        foreach ($questions as $q) {
            $normalized = $this->normalizeQuestion($q, $points, $questionCount);
            if ($normalized && $this->isQuestionValid($normalized)) {
                $validQuestions[] = $normalized;
            }
        }

        // S'assurer d'avoir exactement questionCount questions (tronquer ou compléter si besoin)
        if (count($validQuestions) > $questionCount) {
            $validQuestions = array_slice($validQuestions, 0, $questionCount);
        }
        // Si moins, on garde ce qu'on a (l'IA n'a pas respecté le nombre)

        return $validQuestions;
    }

    /**
     * Prépare le texte source pour l'envoi à l'API.
     * Tronque intelligemment les documents très longs (limite contexte ~128K tokens).
     */
    private function prepareSourceText(string $text): string
    {
        $maxChars = 100_000;
        if (mb_strlen($text) <= $maxChars) {
            return $text;
        }

        $originalLen = mb_strlen($text);
        $prefix = mb_substr($text, 0, (int)($maxChars * 0.7));
        $suffix = mb_substr($text, - (int)($maxChars * 0.2));
        $result = $prefix . "\n\n[... Document tronqué pour analyse (texte trop long) ...]\n\n" . $suffix;

        $this->logger?->info('DeepSeek: texte source tronqué', ['original' => $originalLen, 'sent' => mb_strlen($result)]);

        return $result;
    }

    /**
     * Vérifie qu'une question a du sens (pas de placeholder, pas de réponse générique).
     */
    private function isQuestionValid(array $q): bool
    {
        $text = $q['text'] ?? '';
        if (mb_strlen($text) < 15) {
            return false;
        }

        // Rejeter les questions trop génériques ou placeholder
        $bad = ['question précise', 'question claire', 'réponse correcte', 'distracteur plausible', 'affirmation tirée'];
        foreach ($bad as $b) {
            if (stripos($text, $b) !== false) {
                return false;
            }
        }

        if (isset($q['answers']) && is_array($q['answers'])) {
            foreach ($q['answers'] as $a) {
                $answerText = $a['text'] ?? '';
                if (stripos($answerText, 'cette affirmation est incorrecte') !== false) {
                    return false;
                }
                if (stripos($answerText, 'interprétation erronée') !== false) {
                    return false;
                }
            }
            // Rejeter si la bonne réponse répète ou contient une grande partie de la question (matching inutile)
            foreach ($q['answers'] as $a) {
                if (($a['isCorrect'] ?? false) && !empty($a['text'])) {
                    $ans = mb_strtolower($a['text']);
                    $qText = mb_strtolower($q['text'] ?? '');
                    if (preg_match('/«\s*([^»]+)\s*»/', $qText, $m)) {
                        $extrait = mb_strtolower(trim($m[1]));
                        if (mb_strlen($extrait) > 15 && (str_contains($ans, $extrait) || str_contains($extrait, $ans))) {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    private function normalizeQuestion(array $q, int $defaultPoints, int $maxCount): ?array
    {
        $type = $q['type'] ?? 'qcm';
        if (!in_array($type, ['qcm', 'true_false', 'open'])) {
            $type = 'qcm';
        }
        $text = trim((string)($q['text'] ?? ''));
        if ($text === '') return null;

        $points = (int)($q['points'] ?? $defaultPoints);
        if ($points < 1) $points = $defaultPoints;

        $result = [
            'type' => $type,
            'text' => $text,
            'points' => $points,
        ];

        if ($type === 'open') {
            $result['expectedAnswer'] = trim((string)($q['expectedAnswer'] ?? ''));
            $result['keywords'] = is_array($q['keywords'] ?? null) ? $q['keywords'] : [];
            $result['answers'] = []; // Pour la compatibilité avec saveGeneratedQuiz
        } else {
            $answers = $q['answers'] ?? [];
            if (!is_array($answers)) $answers = [];
            $hasCorrect = false;
            $normalizedAnswers = [];
            foreach ($answers as $a) {
                if (!is_array($a)) continue;
                $textA = trim((string)($a['text'] ?? ''));
                if ($textA === '') continue;
                $isCorrect = (bool)($a['isCorrect'] ?? false);
                if ($isCorrect) $hasCorrect = true;
                $normalizedAnswers[] = ['text' => $textA, 'isCorrect' => $isCorrect];
            }
            if (!$hasCorrect && count($normalizedAnswers) > 0) {
                $normalizedAnswers[0]['isCorrect'] = true;
            }
            if (count($normalizedAnswers) < 2 && $type === 'qcm') return null;
            if (count($normalizedAnswers) < 2 && $type === 'true_false') {
                $normalizedAnswers = [
                    ['text' => 'Vrai', 'isCorrect' => true],
                    ['text' => 'Faux', 'isCorrect' => false]
                ];
            }
            $result['answers'] = $normalizedAnswers;
        }

        return $result;
    }
}
