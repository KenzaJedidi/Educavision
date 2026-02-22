<?php

namespace App\Controller\Api;

use App\Service\TranslationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/translation')]
class TranslationController extends AbstractController
{
    private TranslationService $translationService;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    #[Route('/translate', name: 'api_translation_translate', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function translate(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['text']) || !isset($data['targetLang'])) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Missing required parameters: text, targetLang'
            ], 400);
        }

        $text = $data['text'];
        $targetLang = $data['targetLang'];
        $sourceLang = $data['sourceLang'] ?? 'auto';

        if (empty($text) || empty($targetLang)) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Text and targetLang cannot be empty'
            ], 400);
        }

        $result = $this->translationService->translate($text, $targetLang, $sourceLang);

        return new JsonResponse($result);
    }

    #[Route('/languages', name: 'api_translation_languages', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function getSupportedLanguages(): JsonResponse
    {
        $result = $this->translationService->getSupportedLanguages();

        return new JsonResponse($result);
    }

    #[Route('/detect', name: 'api_translation_detect', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function detectLanguage(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['text'])) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Missing required parameter: text'
            ], 400);
        }

        $text = $data['text'];

        if (empty($text)) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Text cannot be empty'
            ], 400);
        }

        $result = $this->translationService->detectLanguage($text);

        return new JsonResponse($result);
    }
}
