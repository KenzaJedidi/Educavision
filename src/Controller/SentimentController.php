<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SentimentController extends AbstractController
{
    /**
     * @Route("/api/sentiment", methods={"POST"})
     */
    public function sentiment(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $text = $data['text'] ?? '';
        if (!$text) {
            return $this->json(['error' => 'Missing text'], 400);
        }

        // Call sentiment analysis service (to be implemented)
        $sentiment = $this->get('App\Service\SentimentService')->analyze($text);

        return $this->json(['sentiment' => $sentiment]);
    }
}
