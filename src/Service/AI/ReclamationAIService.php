<?php

namespace App\Service\AI;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Psr\Log\LoggerInterface;

/**
 * Service d'Intelligence Artificielle pour les Réclamations
 * - Analyse automatique des réclamations
 * - Génération de réponses intelligentes
 * - Détection de la priorité et du sentiment
 * - Recommandations de résolution
 */
class ReclamationAIService
{
    private string $apiKey;
    private string $apiUrl = 'https://api.openai.com/v1/chat/completions';
    private string $lastError = '';
    private const MAX_RETRIES = 3;
    private const RETRY_DELAY_MS = 5000;

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private CacheInterface $cache,
        private EntityManagerInterface $entityManager,
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
     * Analyse le texte d'une réclamation pour extraire des informations
     */
    public function analyzeReclamation(Reclamation $reclamation): array
    {
        $this->lastError = '';
        
        if (!$this->isConfigured()) {
            return $this->generateMockAnalysis($reclamation);
        }

        $cacheKey = 'reclamation_analysis_' . $reclamation->getId();
        
        try {
            $analysis = $this->cache->get($cacheKey, function (ItemInterface $item) use ($reclamation) {
                $item->expiresAfter(24 * 60 * 60); // 24 heures
                
                $prompt = $this->buildAnalysisPrompt($reclamation);
                
                $response = $this->callOpenAI($prompt, 'analysis');
                
                if ($response) {
                    return $this->parseAnalysisResponse($response);
                }
                
                return $this->generateMockAnalysis($reclamation);
            });
            
            return $analysis;
            
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de l\'analyse IA: ' . $e->getMessage());
            return $this->generateMockAnalysis($reclamation);
        }
    }

    /**
     * Génère une réponse intelligente pour une réclamation
     */
    public function generateResponse(Reclamation $reclamation): ?string
    {
        $this->lastError = '';
        
        if (!$this->isConfigured()) {
            return $this->generateMockResponse($reclamation);
        }

        $cacheKey = 'reclamation_response_' . $reclamation->getId();
        
        try {
            $response = $this->cache->get($cacheKey, function (ItemInterface $item) use ($reclamation) {
                $item->expiresAfter(12 * 60 * 60); // 12 heures
                
                $prompt = $this->buildResponsePrompt($reclamation);
                
                $aiResponse = $this->callOpenAI($prompt, 'response');
                
                if ($aiResponse) {
                    return $aiResponse;
                }
                
                return $this->generateMockResponse($reclamation);
            });
            
            return $response;
            
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la génération de réponse IA: ' . $e->getMessage());
            return $this->generateMockResponse($reclamation);
        }
    }

    /**
     * Détecte la priorité d'une réclamation
     */
    public function detectPriority(Reclamation $reclamation): string
    {
        $analysis = $this->analyzeReclamation($reclamation);
        return $analysis['priority'] ?? 'medium';
    }

    /**
     * Détecte le sentiment d'une réclamation
     */
    public function detectSentiment(Reclamation $reclamation): string
    {
        $analysis = $this->analyzeReclamation($reclamation);
        return $analysis['sentiment'] ?? 'neutral';
    }

    /**
     * Suggère des actions de résolution
     */
    public function suggestResolution(Reclamation $reclamation): array
    {
        $analysis = $this->analyzeReclamation($reclamation);
        return $analysis['suggested_actions'] ?? [];
    }

    /**
     * Construit le prompt pour l'analyse
     */
    private function buildAnalysisPrompt(Reclamation $reclamation): string
    {
        $texte = $reclamation->getTexte() ?? '';
        $type = $reclamation->getType() ?? '';
        
        return "Analyse cette réclamation et extrais les informations suivantes au format JSON:
{
    \"priority\": \"high|medium|low\",
    \"sentiment\": \"positive|neutral|negative\",
    \"category\": \"technique|pedagogique|administratif|autre\",
    \"urgency\": \"immediate|soon|normal\",
    \"complexity\": \"simple|medium|complex\",
    \"suggested_actions\": [\"action1\", \"action2\", \"action3\"],
    \"keywords\": [\"mot1\", \"mot2\", \"mot3\"]
}

Type: {$type}
Texte: {$texte}";
    }

    /**
     * Construit le prompt pour la génération de réponse
     */
    private function buildResponsePrompt(Reclamation $reclamation): string
    {
        $texte = $reclamation->getTexte() ?? '';
        $type = $reclamation->getType() ?? '';
        $analysis = $this->analyzeReclamation($reclamation);
        
        return "En tant que service client professionnel, génère une réponse empathique et constructive pour cette réclamation:

Type: {$type}
Texte: {$texte}
Priorité: {$analysis['priority']}
Sentiment: {$analysis['sentiment']}

La réponse doit:
- Être empathique et professionnelle
- Reconnaître le problème
- Proposer des solutions concrètes
- Être concise (max 200 mots)
- Utiliser un ton adapté au sentiment détecté";
    }

    /**
     * Appelle l'API OpenAI
     */
    private function callOpenAI(string $prompt, string $type): ?string
    {
        $attempt = 0;
        
        while ($attempt < self::MAX_RETRIES) {
            try {
                $temperature = $type === 'analysis' ? 0.3 : 0.7;
                $maxTokens = $type === 'analysis' ? 500 : 300;

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
                                'content' => $type === 'analysis' 
                                    ? 'Tu es un expert en analyse de réclamations clients. Sois précis et factuel.'
                                    : 'Tu es un agent de service client professionnel et empathique.'
                            ],
                            [
                                'role' => 'user',
                                'content' => $prompt
                            ]
                        ],
                        'temperature' => $temperature,
                        'max_tokens' => $maxTokens,
                    ],
                    'timeout' => 30,
                ]);

                if ($response->getStatusCode() === 200) {
                    $data = $response->toArray();
                    return trim($data['choices'][0]['message']['content']);
                }

                if ($response->getStatusCode() === 429) {
                    $attempt++;
                    if ($attempt < self::MAX_RETRIES) {
                        $delayMs = self::RETRY_DELAY_MS * (2 ** ($attempt - 1));
                        usleep($delayMs * 1000);
                        continue;
                    }
                }

                $this->lastError = "OpenAI API erreur: Status {$response->getStatusCode()}";
                return null;

            } catch (\Exception $e) {
                $attempt++;
                if ($attempt < self::MAX_RETRIES) {
                    usleep(self::RETRY_DELAY_MS * 1000);
                    continue;
                }
                $this->lastError = "Exception OpenAI: " . $e->getMessage();
                return null;
            }
        }
        
        return null;
    }

    /**
     * Parse la réponse d'analyse
     */
    private function parseAnalysisResponse(string $response): array
    {
        try {
            $data = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return [
                    'priority' => $data['priority'] ?? 'medium',
                    'sentiment' => $data['sentiment'] ?? 'neutral',
                    'category' => $data['category'] ?? 'autre',
                    'urgency' => $data['urgency'] ?? 'normal',
                    'complexity' => $data['complexity'] ?? 'medium',
                    'suggested_actions' => $data['suggested_actions'] ?? [],
                    'keywords' => $data['keywords'] ?? []
                ];
            }
        } catch (\Exception $e) {
            $this->logger->warning('Erreur parsing JSON: ' . $e->getMessage());
        }
        
        return $this->generateMockAnalysis(new Reclamation());
    }

    /**
     * Génère une analyse mock
     */
    private function generateMockAnalysis(Reclamation $reclamation): array
    {
        $texte = strtolower($reclamation->getTexte() ?? '');
        
        // Détection simple de priorité
        $priority = 'medium';
        if (str_contains($texte, 'urgent') || str_contains($texte, 'immédiat')) {
            $priority = 'high';
        } elseif (str_contains($texte, 'information') || str_contains($texte, 'question')) {
            $priority = 'low';
        }
        
        // Détection simple de sentiment
        $sentiment = 'neutral';
        if (str_contains($texte, 'mécontent') || str_contains($texte, 'problème') || str_contains($texte, 'erreur')) {
            $sentiment = 'negative';
        } elseif (str_contains($texte, 'merci') || str_contains($texte, 'satisfait')) {
            $sentiment = 'positive';
        }
        
        return [
            'priority' => $priority,
            'sentiment' => $sentiment,
            'category' => $reclamation->getType() ?? 'autre',
            'urgency' => $priority === 'high' ? 'immediate' : 'normal',
            'complexity' => 'medium',
            'suggested_actions' => [
                'Analyser la demande en détail',
                'Contacter l\'utilisateur si nécessaire',
                'Proposer une solution adaptée'
            ],
            'keywords' => ['réclamation', 'support', 'résolution']
        ];
    }

    /**
     * Génère une réponse mock
     */
    private function generateMockResponse(Reclamation $reclamation): string
    {
        $type = $reclamation->getType() ?? '';
        
        $responses = [
            'technique' => "Nous avons bien reçu votre réclamation technique. Notre équipe technique va analyser le problème et vous contacter dans les plus brefs délais pour trouver une solution.",
            'pedagogique' => "Merci pour votre retour concernant notre contenu pédagogique. Nous allons examiner votre demande et améliorer nos cours en fonction de vos suggestions.",
            'administratif' => "Votre demande administrative a bien été enregistrée. Notre service administratif va traiter votre dossier et vous informera des prochaines étapes.",
            'default' => "Nous avons bien reçu votre réclamation et vous en remercions. Notre équipe va étudier votre demande et vous apportera une réponse dans les meilleurs délais."
        ];
        
        return $responses[$type] ?? $responses['default'];
    }

    /**
     * Retourne la dernière erreur
     */
    public function getLastError(): string
    {
        return $this->lastError;
    }
}
