<?php
namespace App\Controller\Admin;

use App\Entity\Reclamation;
use App\Repository\ReclamationRepository;
use App\Service\ResumeAutoService;
use App\Service\SentimentAutoService;
use App\Service\ResolutionTimePredictionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ReclamationAnalysisController extends AbstractController
{
    #[Route('/admin/reclamation/analyze-all', name: 'admin_reclamation_analyze_all', methods: ['POST'])]
    public function analyzeAll(Request $request,
        ReclamationRepository $repo,
        ResumeAutoService $resumeService,
        SentimentAutoService $sentimentService,
        ResolutionTimePredictionService $predictor,
        EntityManagerInterface $em,
        CsrfTokenManagerInterface $csrf
    ): JsonResponse
    {
        $token = $request->request->get('_token');
        if (!$csrf->isTokenValid(new \Symfony\Component\Security\Csrf\CsrfToken('analyze_all', $token))) {
            return new JsonResponse(['error' => 'Jeton CSRF invalide'], Response::HTTP_FORBIDDEN);
        }

        $reclamations = $repo->findAll();
        $count = 0;

        foreach ($reclamations as $r) {
            /** @var Reclamation $r */
            $desc = (string) $r->getDescription();
            $summary = $resumeService->summarize($desc);
            $sent = $sentimentService->analyze($desc);
            $hours = $predictor->predict($desc);

            $r->setResumeAuto($summary);
            $r->setSentimentAuto($sent);
            $r->setTempsResolutionAuto($hours);
            $em->persist($r);
            $count++;

            // flush in batches to avoid memory growth
            if ($count % 20 === 0) {
                $em->flush();
                $em->clear();
            }
        }

        $em->flush();

        return new JsonResponse(['message' => 'Analyse terminée', 'count' => $count]);
    }
}
