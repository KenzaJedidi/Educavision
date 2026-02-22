<?php
namespace App\Controller\Front;

use App\Entity\Reponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ReponseController extends AbstractController
{
    #[Route('/reponse/rate', name: 'front_reponse_rate', methods: ['POST'])]
    public function rate(Request $request, EntityManagerInterface $em, CsrfTokenManagerInterface $csrf): JsonResponse
    {
        $id = (int) $request->request->getInt('id');
        $rating = (int) $request->request->getInt('rating');
        $token = (string) $request->request->get('_token');

        if (!$csrf->isTokenValid(new CsrfToken('rate_response', $token))) {
            return new JsonResponse(['error' => 'Jeton CSRF invalide'], Response::HTTP_FORBIDDEN);
        }

        if ($rating < 1 || $rating > 5) {
            return new JsonResponse(['error' => 'Valeur de note invalide'], Response::HTTP_BAD_REQUEST);
        }

        $reponse = $em->getRepository(Reponse::class)->find($id);
        if (!$reponse) {
            return new JsonResponse(['error' => 'Réponse introuvable'], Response::HTTP_NOT_FOUND);
        }

        $reponse->setRating($rating);
        $em->persist($reponse);
        $em->flush();

        // map rating to satisfaction label
        $label = 'Neutre';
        if ($rating >= 4) $label = 'Très satisfait';
        elseif ($rating == 3) $label = 'Satisfait';
        elseif ($rating == 2) $label = 'Insatisfait';
        elseif ($rating <= 1) $label = 'Très insatisfait';

        return new JsonResponse(['message' => 'Merci pour votre note', 'rating' => $rating, 'label' => $label]);
    }
}
