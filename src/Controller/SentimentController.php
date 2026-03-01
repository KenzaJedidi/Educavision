<?php

namespace App\Controller;

use App\Service\SentimentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SentimentController extends AbstractController
{
    public function __construct(
        private readonly SentimentService $sentimentService
    ) {
    }

    #[Route('/api/sentiment', methods: ['POST'])]
    public function sentiment(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $text = $data['text'] ?? '';
        if (!$text) {
            return $this->json(['error' => 'Missing text'], 400);
        }

        $sentiment = $this->sentimentService->analyze($text);

        return $this->json(['sentiment' => $sentiment]);
    }
}
