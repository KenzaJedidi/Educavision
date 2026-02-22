<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SummarizationController extends AbstractController
{
    /**
     * @Route("/api/summarize", methods={"POST"})
     */
    public function summarize(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $text = $data['text'] ?? '';
        if (!$text) {
            return $this->json(['error' => 'Missing text'], 400);
        }

        // Call summarization service (to be implemented)
        $summary = $this->get('App\Service\SummarizationService')->summarize($text);

        return $this->json(['summary' => $summary]);
    }
}
