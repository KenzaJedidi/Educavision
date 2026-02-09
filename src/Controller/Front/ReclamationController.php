<?php

namespace App\Controller\Front;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'front_reclamation', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
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
            $em->persist($reclamation);
            $em->flush();
            // Memorize email to prefill 'Mes réclamations'
            if (method_exists($reclamation, 'getEmail') && $reclamation->getEmail()) {
                $request->getSession()->set('reclamation_email', $reclamation->getEmail());
            }
            $this->addFlash('success', 'Votre réclamation a été envoyée. Merci, nous vous répondrons prochainement.');
            return $this->redirectToRoute('front_reclamation');
        }

        return $this->render('front/pages/reclamation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/mes-reclamations', name: 'front_reclamation_my', methods: ['GET', 'POST'])]
    public function my(Request $request, EntityManagerInterface $em): Response
    {
        $email = $request->query->get('email') ?: $request->getSession()->get('reclamation_email');

        if ($request->isMethod('POST')) {
            $emailPosted = trim((string)$request->request->get('email'));
            if ($emailPosted) {
                return $this->redirectToRoute('front_reclamation_my', ['email' => $emailPosted]);
            }
        }

        $items = [];
        if ($email) {
            $items = $em->getRepository(Reclamation::class)->findBy(['email' => $email], ['dateReclamation' => 'DESC']);
        }

        return $this->render('front/pages/reclamation/my.html.twig', [
            'email' => $email,
            'reclamations' => $items,
        ]);
    }
}
