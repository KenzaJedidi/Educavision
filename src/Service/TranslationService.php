<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class TranslationService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;

    public function __construct(HttpClientInterface $httpClient, string $apiKey = null)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
    }

    /**
     * Translate text using Google Translate API (or similar)
     */
    public function translate(string $text, string $targetLang, string $sourceLang = 'auto'): ?array
    {
        if (!$this->apiKey) {
            return [
                'success' => false,
                'error' => 'API key not configured',
                'translatedText' => null
            ];
        }

        try {
            // Using Google Translate API as example
            $response = $this->httpClient->request('POST', 'https://translation.googleapis.com/language/translate/v2', [
                'query' => [
                    'key' => $this->apiKey,
                ],
                'json' => [
                    'q' => $text,
                    'source' => $sourceLang,
                    'target' => $targetLang,
                    'format' => 'text'
                ]
            ]);

            $data = $response->toArray();

            if (isset($data['data']['translations'][0])) {
                return [
                    'success' => true,
                    'translatedText' => $data['data']['translations'][0]['translatedText'],
                    'sourceLanguage' => $data['data']['translations'][0]['detectedSourceLanguage'] ?? $sourceLang
                ];
            }

            return [
                'success' => false,
                'error' => 'Translation failed',
                'translatedText' => null
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'translatedText' => null
            ];
        }
    }

    /**
     * Get supported languages
     */
    public function getSupportedLanguages(): array
    {
        if (!$this->apiKey) {
            return [
                'success' => false,
                'error' => 'API key not configured',
                'languages' => []
            ];
        }

        try {
            $response = $this->httpClient->request('GET', 'https://translation.googleapis.com/language/translate/v2/languages', [
                'query' => [
                    'key' => $this->apiKey,
                    'target' => 'en'
                ]
            ]);

            $data = $response->toArray();

            $languages = [];
            if (isset($data['data']['languages'])) {
                foreach ($data['data']['languages'] as $lang) {
                    $languages[] = [
                        'code' => $lang['language'],
                        'name' => $lang['name'] ?? $lang['language']
                    ];
                }
            }

            return [
                'success' => true,
                'languages' => $languages
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'languages' => []
            ];
        }
    }

    /**
     * Detect language of text
     */
    public function detectLanguage(string $text): ?array
    {
        if (!$this->apiKey) {
            return [
                'success' => false,
                'error' => 'API key not configured',
                'language' => null,
                'confidence' => null
            ];
        }

        try {
            $response = $this->httpClient->request('POST', 'https://translation.googleapis.com/language/translate/v2/detect', [
                'query' => [
                    'key' => $this->apiKey,
                ],
                'json' => [
                    'q' => $text
                ]
            ]);

            $data = $response->toArray();

            if (isset($data['data']['detections'][0][0])) {
                $detection = $data['data']['detections'][0][0];
                return [
                    'success' => true,
                    'language' => $detection['language'],
                    'confidence' => $detection['confidence'] ?? null,
                    'isReliable' => $detection['isReliable'] ?? false
                ];
            }

            return [
                'success' => false,
                'error' => 'Language detection failed',
                'language' => null,
                'confidence' => null
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'language' => null,
                'confidence' => null
            ];
        }
    }
}
