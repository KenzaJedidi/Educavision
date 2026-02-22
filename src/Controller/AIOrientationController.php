<?php

namespace App\Controller;

use App\Repository\FiliereRepository;
use App\Service\ProfileAnalysisService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/orientation/ia', name: 'ai_orientation_')]
class AIOrientationController extends AbstractController
{
    /**
     * Page principale du Conseiller d'Orientation Intelligent
     */
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('ai_orientation/index.html.twig');
    }

    /**
     * Endpoint AJAX : reçoit le texte du profil, appelle l'IA, compare avec les filières en BDD.
     */
    #[Route('/analyser', name: 'analyser', methods: ['POST'])]
    public function analyser(
        Request $request,
        ProfileAnalysisService $profileService,
        FiliereRepository $filiereRepository
    ): JsonResponse {
        // Récupération du texte depuis le body JSON ou les données de formulaire
        $body = json_decode($request->getContent(), true);
        $texte = trim($body['texte'] ?? $request->request->get('texte', ''));

        if (empty($texte)) {
            return new JsonResponse(['error' => 'Veuillez fournir un texte décrivant votre profil.'], 400);
        }

        if (strlen($texte) < 20) {
            return new JsonResponse(['error' => 'Veuillez écrire au moins quelques phrases pour décrire votre profil.'], 400);
        }

        if (!$profileService->isConfigured()) {
            return new JsonResponse([
                'error' => 'Service IA non configuré. Ajoutez OPENAI_API_KEY ou GEMINI_API_KEY dans le fichier .env.',
            ], 503);
        }

        // Appel au service IA
        $analyse = $profileService->analyserProfil($texte);

        if ($analyse === null) {
            return new JsonResponse([
                'error' => 'Analyse IA impossible : ' . $profileService->getLastError(),
            ], 500);
        }

        // Matching des domaines recommandés avec les filières en base de données
        $toutesLesFilieres = $filiereRepository->findAll();
        $filieresSuggerees = $this->matcherFilieres($analyse['domaines_recommandes'], $toutesLesFilieres);

        return new JsonResponse([
            'success'                  => true,
            'competences_detectees'    => $analyse['competences_detectees'],
            'filiere_suggerees'        => $filieresSuggerees,
            'niveau_technique_estime'  => $analyse['niveau_technique_estime'],
            'domaines_recommandes'     => $analyse['domaines_recommandes'],
        ]);
    }

    /**
     * Compare les domaines recommandés par l'IA avec les filières disponibles en base.
     * Retourne un tableau de filières correspondantes avec leur nom et description.
     */
    private function matcherFilieres(array $domainesRecommandes, array $filieres): array
    {
        if (empty($domainesRecommandes) || empty($filieres)) {
            return [];
        }

        // Normaliser les mots-clés de domaines pour la comparaison
        $keywords = [];
        foreach ($domainesRecommandes as $domaine) {
            // Décomposer en mots individuels et normaliser
            $mots = preg_split('/[\s,\-_\/]+/', mb_strtolower($domaine));
            foreach ($mots as $mot) {
                $mot = trim($mot);
                if (strlen($mot) >= 3) {
                    $keywords[] = $mot;
                }
            }
        }

        $matches = [];
        foreach ($filieres as $filiere) {
            $nomNorm  = mb_strtolower($filiere->getNom() ?? '');
            $descNorm = mb_strtolower(substr($filiere->getDescription() ?? '', 0, 500));
            $haystack = $nomNorm . ' ' . $descNorm;

            $score = 0;
            foreach ($keywords as $keyword) {
                if (str_contains($haystack, $keyword)) {
                    $score++;
                }
            }

            if ($score > 0) {
                $matches[] = [
                    'id'          => $filiere->getId(),
                    'nom'         => $filiere->getNom(),
                    'description' => mb_substr($filiere->getDescription() ?? '', 0, 150) . '...',
                    'score'       => $score,
                ];
            }
        }

        // Trier par score décroissant et limiter à 5 résultats
        usort($matches, fn($a, $b) => $b['score'] - $a['score']);
        return array_slice($matches, 0, 5);
    }
}
