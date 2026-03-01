<?php

namespace App\Controller;

use App\Service\SummarizationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SummarizationController extends AbstractController
{
    public function __construct(
        private readonly SummarizationService $summarizationService
    ) {
    }

    #[Route('/api/summarize', methods: ['POST'])]
    public function summarize(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $text = $data['text'] ?? '';
        if (!$text) {
            return $this->json(['error' => 'Missing text'], 400);
        }

        $summary = $this->summarizationService->summarize($text);

        return $this->json(['summary' => $summary]);
    }
}
