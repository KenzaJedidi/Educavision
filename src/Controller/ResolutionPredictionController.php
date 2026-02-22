<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResolutionPredictionController extends AbstractController
{
    /**
     * @Route("/api/predict-resolution-time", methods={"POST"})
     */
    public function predict(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $text = $data['text'] ?? '';
        if (!$text) {
            return $this->json(['error' => 'Missing text'], 400);
        }

        // Call prediction service (to be implemented)
        $prediction = $this->get('App\Service\ResolutionPredictionService')->predict($text);

        return $this->json(['resolution_time' => $prediction]);
    }
}
