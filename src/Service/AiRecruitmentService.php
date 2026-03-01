<?php

namespace App\Service;

use App\Entity\Candidature;
use App\Entity\OffreStage;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

/**
 * Service d'Intelligence Artificielle pour le recrutement.
 * Utilise l'API Gemini de Google pour :
 * - Scorer les candidatures par rapport aux offres
 * - Générer des résumés automatiques de candidatures
 * - Générer des descriptions d'offres de stage
 * - Recommander des offres aux candidats
 */
class AiRecruitmentService
{
    private string $apiKey;
    private string $apiUrl;

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        string $geminiApiKey = ''
    ) {
        $this->apiKey = $geminiApiKey ?: ($_ENV['GEMINI_API_KEY'] ?? '');
        $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
    }

    /**
     * Vérifie si l'API IA est configurée
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Appel générique à l'API Gemini
     */
    private string $lastError = '';

    public function getLastError(): string
    {
        return $this->lastError;
    }

    /**
     * Nettoie une réponse avant json_decode : supprime markdown, caractères de contrôle, etc.
     */
    private function cleanJsonResponse(string $raw): string
    {
        // Supprimer blocs markdown
        $raw = preg_replace('/```json\s*/i', '', $raw);
        $raw = preg_replace('/```\s*/', '', $raw);
        // Remplacer les vrais sauts de ligne et tabulations par des espaces
        $raw = str_replace(["\r\n", "\r", "\n", "\t"], ' ', $raw);
        // Supprimer TOUS les caractères de contrôle (bytes 0x00-0x1F et 0x7F)
        $raw = preg_replace('/[\x00-\x1F\x7F]/', '', $raw);
        // Assurer encodage UTF-8 valide
        if (function_exists('mb_convert_encoding')) {
            $raw = mb_convert_encoding($raw, 'UTF-8', 'UTF-8');
        }
        // Extraire uniquement le bloc JSON {...}
        if (preg_match('/\{.*\}/s', $raw, $m)) {
            $raw = $m[0];
        }
        return trim($raw);
    }

    /**
     * Tente de réparer un JSON tronqué (réponse coupée par maxOutputTokens)
     */
    private function repairTruncatedJson(string $json): ?array
    {
        // Extraire les objets complets {"id":..., "pertinence":..., "raison":"..."}
        preg_match_all('/\{\s*"id"\s*:\s*(\d+)\s*,\s*"pertinence"\s*:\s*(\d+)\s*,\s*"raison"\s*:\s*"([^"]*)"\s*\}/', $json, $matches, PREG_SET_ORDER);

        if (!empty($matches)) {
            $results = [];
            foreach ($matches as $m) {
                $results[] = [
                    'id' => (int)$m[1],
                    'pertinence' => (int)$m[2],
                    'raison' => $m[3],
                ];
            }
            return $results;
        }
        return null;
    }

    private function callGemini(string $prompt): ?string
    {
        $this->lastError = '';

        if (!$this->isConfigured()) {
            $this->lastError = 'Clé API Gemini non configurée';
            $this->logger->warning('AiRecruitmentService: ' . $this->lastError);
            return null;
        }

        try {
            $response = $this->httpClient->request('POST', $this->apiUrl . '?key=' . $this->apiKey, [
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
                        'maxOutputTokens' => 2048,
                    ]
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $data = $response->toArray(false);

            if ($statusCode === 429) {
                $this->lastError = 'Quota API dépassé (429). Créez une nouvelle clé sur https://aistudio.google.com/apikey avec un nouveau projet.';
                $this->logger->error('AiRecruitmentService: ' . $this->lastError);
                return null;
            }

            if ($statusCode !== 200) {
                $errorMsg = $data['error']['message'] ?? 'Erreur HTTP ' . $statusCode;
                $this->lastError = $errorMsg;
                $this->logger->error('AiRecruitmentService Gemini error: ' . $errorMsg);
                return null;
            }

            return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        } catch (\Throwable $e) {
            $this->lastError = $e->getMessage();
            $this->logger->error('AiRecruitmentService Gemini API error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Score une candidature par rapport à une offre (0-100)
     * Retourne un tableau avec le score et l'analyse
     */
    public function scoreCandidature(Candidature $candidature, OffreStage $offre): array
    {
        $competences = $offre->getCompetencesRequises() ? implode(', ', $offre->getCompetencesRequises()) : 'non spécifiées';

        $prompt = <<<PROMPT
Tu es un expert en ressources humaines. Analyse cette candidature par rapport à l'offre de stage et donne un score de compatibilité entre 0 et 100.

OFFRE DE STAGE:
- Titre: {$offre->getTitre()}
- Description: {$offre->getDescription()}
- Entreprise: {$offre->getEntreprise()}
- Compétences requises: {$competences}

CANDIDAT:
- Nom: {$candidature->getNom()} {$candidature->getPrenom()}
- Niveau d'étude: {$candidature->getNiveauEtude()}
- Lettre de motivation: {$candidature->getLettreMotivation()}

Réponds UNIQUEMENT au format JSON suivant (sans markdown, sans backticks):
{"score": <nombre entre 0 et 100>, "analyse": "<analyse courte en 2-3 phrases>", "points_forts": ["point1", "point2"], "points_faibles": ["point1", "point2"]}
PROMPT;

        $result = $this->callGemini($prompt);

        if ($result) {
            // Nettoyer la réponse
            $result = $this->cleanJsonResponse($result);

            $decoded = json_decode($result, true, 512, JSON_INVALID_UTF8_SUBSTITUTE);
            if ($decoded && isset($decoded['score'])) {
                return $decoded;
            }
        }

        return [
            'score' => 0,
            'analyse' => 'Analyse IA indisponible',
            'points_forts' => [],
            'points_faibles' => [],
        ];
    }

    /**
     * Génère un résumé automatique d'une candidature (pour le dashboard admin)
     */
    public function genererResumeCandidature(Candidature $candidature): string
    {
        $offre = $candidature->getOffreStage();
        $offreTitre = $offre ? $offre->getTitre() : 'N/A';

        $prompt = <<<PROMPT
Tu es un assistant RH. Génère un résumé concis (3-4 lignes maximum) de cette candidature pour un responsable recrutement.

Candidat: {$candidature->getNom()} {$candidature->getPrenom()}
Email: {$candidature->getEmail()}
Niveau d'étude: {$candidature->getNiveauEtude()}
Offre visée: {$offreTitre}
Lettre de motivation: {$candidature->getLettreMotivation()}

Résumé (en français, concis et professionnel):
PROMPT;

        $result = $this->callGemini($prompt);

        return $result ?: 'Résumé IA non disponible.';
    }

    /**
     * Génère une description d'offre de stage à partir d'un titre et de mots-clés
     */
    public function genererDescriptionOffre(string $titre, string $entreprise, string $motsCles = '', string $lieu = ''): string
    {
        $prompt = <<<PROMPT
Tu es un expert en rédaction d'offres d'emploi. Génère une description professionnelle et attractive pour cette offre de stage.

Titre du poste: {$titre}
Entreprise: {$entreprise}
Lieu: {$lieu}
Mots-clés / Compétences: {$motsCles}

La description doit inclure:
1. Présentation courte du poste
2. Missions principales (3-5 points)
3. Profil recherché (3-4 points)
4. Ce que nous offrons (2-3 points)

Écris en français, de manière professionnelle et engageante. Ne mets pas de titre, juste le contenu.
PROMPT;

        $result = $this->callGemini($prompt);

        return $result ?: 'Description IA non disponible.';
    }

    /**
     * Analyse un texte de CV et extrait les compétences
     */
    public function analyserCvTexte(string $texte): array
    {
        $prompt = <<<PROMPT
Tu es un expert en analyse de CV. Analyse le texte suivant extrait d'un CV et identifie les compétences clés.

TEXTE DU CV:
{$texte}

Réponds UNIQUEMENT au format JSON suivant (sans markdown, sans backticks):
{"competences_techniques": ["comp1", "comp2", "comp3"], "competences_soft": ["comp1", "comp2"], "langues": ["langue1", "langue2"], "niveau_estime": "Junior/Intermédiaire/Senior", "resume": "<résumé en 2 phrases du profil>"}
PROMPT;

        $result = $this->callGemini($prompt);

        if ($result) {
            $result = $this->cleanJsonResponse($result);

            $decoded = json_decode($result, true, 512, JSON_INVALID_UTF8_SUBSTITUTE);
            if ($decoded) {
                return $decoded;
            }
        }

        return [
            'competences_techniques' => [],
            'competences_soft' => [],
            'langues' => [],
            'niveau_estime' => 'Non déterminé',
            'resume' => 'Analyse IA non disponible.',
        ];
    }

    /**
     * Recommande des offres pertinentes pour un candidat basé sur ses compétences
     * Retourne les IDs des offres triées par pertinence
     */
    public function recommanderOffres(array $competencesCandidat, string $niveauEtude, array $offres): array
    {
        $offresTexte = '';
        foreach ($offres as $offre) {
            $comp = $offre->getCompetencesRequises() ? implode(', ', $offre->getCompetencesRequises()) : 'non spécifiées';
            $offresTexte .= "- ID:{$offre->getId()} | Titre: {$offre->getTitre()} | Compétences: {$comp} | Entreprise: {$offre->getEntreprise()}\n";
        }

        $competencesTexte = implode(', ', $competencesCandidat);

        $prompt = <<<PROMPT
Tu es un expert en orientation professionnelle. Classe ces offres de stage par pertinence pour ce candidat.

PROFIL DU CANDIDAT:
- Compétences: {$competencesTexte}
- Niveau d'étude: {$niveauEtude}

OFFRES DISPONIBLES:
{$offresTexte}

Réponds UNIQUEMENT au format JSON suivant (sans markdown, sans backticks):
{"recommandations": [{"id": <id_offre>, "pertinence": <score 0-100>, "raison": "<5 mots max>"}]}
Trie par pertinence décroissante. Maximum 3 offres. Raisons très courtes (5 mots max).
PROMPT;

        $result = $this->callGemini($prompt);

        if ($result) {
            $cleaned = $this->cleanJsonResponse($result);

            $decoded = json_decode($cleaned, true, 512, JSON_INVALID_UTF8_SUBSTITUTE);

            if ($decoded && isset($decoded['recommandations'])) {
                return $decoded['recommandations'];
            }

            // Tentative de réparation si JSON tronqué
            $repaired = $this->repairTruncatedJson($cleaned);
            if ($repaired) {
                $this->logger->info('recommanderOffres: JSON réparé, ' . count($repaired) . ' résultats');
                return $repaired;
            }

            $this->logger->warning('recommanderOffres parse fail: ' . json_last_error_msg());
        } else {
            $this->logger->warning('recommanderOffres: callGemini returned null, err=' . $this->lastError);
        }

        return [];
    }
}
