<?php
namespace App\Controller\Api;

use App\Service\ResumeAutoService;
use App\Service\SentimentAutoService;
use App\Service\ResolutionTimePredictionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReclamationAnalysisController extends AbstractController
{
    #[Route('/api/reclamation/analyze', name: 'api_reclamation_analyze', methods: ['POST'])]
    public function analyze(Request $request, ResumeAutoService $resumeService, SentimentAutoService $sentimentService, ResolutionTimePredictionService $predictionService): Response
    {
        $data = json_decode($request->getContent(), true);
        $text = trim($data['text'] ?? '');
        if ($text === '') {
            return $this->json(['error' => 'Missing text'], 400);
        }

        $summary = $resumeService->summarize($text, 12);
        $sentimentDetailed = $sentimentService->analyzeDetailed($text);
        $predicted = $predictionService->predict($text);

        return $this->json([
            'summary' => $summary,
            'sentiment' => $sentimentDetailed['sentiment'],
            'sentimentScore' => $sentimentDetailed['score'],
            'sentimentConfidence' => $sentimentDetailed['confidence'],
            'predictedHours' => $predicted,
        ]);
    }
}
