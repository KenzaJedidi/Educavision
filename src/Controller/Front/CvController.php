<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cv')]
class CvController extends AbstractController
{
    #[Route('/', name: 'cv_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('front/pages/cv/index.html.twig');
    }

    #[Route('/preview', name: 'cv_preview', methods: ['GET'])]
    public function preview(): Response
    {
        // Minimal preview placeholder
        $sample = [
            'nom' => 'John Doe',
            'email' => 'john@example.com',
            'telephone' => '+216 00 000 000',
            'experiences' => [
                [ 'poste' => 'Développeur', 'entreprise' => 'Acme', 'annees' => '2022-2024', 'description' => 'Développement d\'applications.' ]
            ]
        ];

        return $this->render('front/pages/cv/preview.html.twig', ['cv' => $sample]);
    }
}
