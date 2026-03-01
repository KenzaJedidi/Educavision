<?php

namespace App\Controller\Front;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'front_reclamation', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, UtilisateurRepository $utilisateurRepository, \App\Service\ResumeAutoService $resumeService, \App\Service\SentimentAutoService $sentimentService, \App\Service\ResolutionTimePredictionService $predictionService, \App\Service\CategoryAutoService $categoryService): Response
    {
        $reclamation = new Reclamation();
        $reclamation->setDateReclamation(new \DateTime());
        if (method_exists($reclamation, 'setStatus') && !$reclamation->getStatus()) {
            $reclamation->setStatus('en cours de traitement');
        }

        // Disable HTML5 validation, rely purely on Symfony Validator constraints
        $form = $this->createForm(ReclamationType::class, $reclamation, [
            'attr' => ['novalidate' => 'novalidate'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier que l'email est inscrit sur le site
            $email = $reclamation->getEmail();
            $utilisateur = $utilisateurRepository->findOneBy(['email' => $email]);
            if (!$utilisateur) {
                $form->get('email')->addError(new FormError(
                    'Cet email n\'est pas inscrit sur le site. Seuls les utilisateurs ayant un compte peuvent déposer une réclamation. Veuillez vous inscrire d\'abord.'
                ));
            } else {
                // Détection automatique du rôle depuis le compte utilisateur
                $roleUtilisateur = $utilisateur->getRole();
                $reclamation->setRole(in_array($roleUtilisateur, ['etudiant', 'professeur']) ? $roleUtilisateur : 'professeur');

                // Analyse automatique
                $desc = $reclamation->getDescription();
                $reclamation->setResumeAuto($resumeService->summarize($desc));
                $reclamation->setSentimentAuto($sentimentService->analyze($desc));
                $reclamation->setTempsResolutionAuto($predictionService->predict($desc));
                $reclamation->setCategory($categoryService->categorize($desc));

                $em->persist($reclamation);
                $em->flush();
                if (method_exists($reclamation, 'getEmail') && $reclamation->getEmail()) {
                    $request->getSession()->set('reclamation_email', $reclamation->getEmail());
                }
                $this->addFlash('success', 'Votre réclamation a été envoyée. Merci, nous vous répondrons prochainement.');

                return $this->redirectToRoute('front_reclamation');
            }
        }

        return $this->render('front/pages/reclamation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/mes-reclamations', name: 'front_reclamation_my', methods: ['GET'])]
    public function my(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $email = $user->getUserIdentifier(); // email for Utilisateur

        $items = $em->getRepository(Reclamation::class)->findBy(
            ['email' => $email],
            ['dateReclamation' => 'DESC']
        );

        return $this->render('front/pages/reclamation/my.html.twig', [
            'reclamations' => $items,
        ]);
    }
}
