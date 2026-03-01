<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class ChapterTranslationService
{
    private string $libreTranslateEndpoint = 'https://api.libretranslate.de/translate';
    
    // Codes de langue supportés
    private const SUPPORTED_LANGUAGES = [
        'en' => 'English',
        'es' => 'Español',
        'fr' => 'Français',
        'de' => 'Deutsch',
        'it' => 'Italiano',
        'pt' => 'Português',
        'ru' => 'Русский',
        'ja' => 'Japanese',
        'zh' => '中文',
        'ar' => 'العربية',
    ];

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger
    ) {}

    /**
     * Traduit le contenu d'un chapitre
     */
    public function translateContent(string $content, string $targetLanguage = 'en', string $sourceLanguage = 'fr'): ?string
    {
        if (!$this->isLanguageSupported($targetLanguage)) {
            $this->logger->warning('Langue cible non supportée', ['language' => $targetLanguage]);
            return null;
        }

        try {
            $response = $this->callLibreTranslate($content, $sourceLanguage, $targetLanguage);
            
            $this->logger->info('Contenu traduit avec succès', [
                'source' => $sourceLanguage,
                'target' => $targetLanguage,
                'length' => strlen($content),
            ]);

            return $response;
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la traduction', [
                'error' => $e->getMessage(),
                'language' => $targetLanguage,
            ]);
            return null;
        }
    }

    /**
     * Traduit le titre du chapitre
     */
    public function translateTitle(string $title, string $targetLanguage = 'en', string $sourceLanguage = 'fr'): ?string
    {
        return $this->translateContent($title, $targetLanguage, $sourceLanguage);
    }

    /**
     * Traduit en plusieurs langues simultanément
     */
    public function translateToMultiple(string $content, array $targetLanguages = ['en', 'es', 'de'], string $sourceLanguage = 'fr'): array
    {
        $translations = [];

        foreach ($targetLanguages as $language) {
            if ($this->isLanguageSupported($language)) {
                $translated = $this->translateContent($content, $language, $sourceLanguage);
                if ($translated) {
                    $translations[$language] = $translated;
                }
            }
        }

        return $translations;
    }

    /**
     * Retourne les langues supportées
     */
    public function getSupportedLanguages(): array
    {
        return self::SUPPORTED_LANGUAGES;
    }

    /**
     * Vérifie si une langue est supportée
     */
    public function isLanguageSupported(string $language): bool
    {
        return isset(self::SUPPORTED_LANGUAGES[$language]);
    }

    /**
     * Appelle l'API LibreTranslate
     */
    private function callLibreTranslate(string $text, string $sourceLang, string $targetLang): string
    {
        $response = $this->httpClient->request('POST', $this->libreTranslateEndpoint, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'q' => $text,
                'source' => $sourceLang,
                'target' => $targetLang,
                'format' => 'text', // ou 'html' si nécessaire
            ],
            'timeout' => 30,
        ]);

        $data = $response->toArray();

        if (!isset($data['translatedText'])) {
            throw new \Exception('Réponse LibreTranslate invalide');
        }

        return $data['translatedText'];
    }
}
