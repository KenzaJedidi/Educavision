<?php

namespace App\Controller\Front;

use App\Entity\Candidature;
use App\Entity\OffreStage;
use App\Form\CandidatureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CandidatureController extends AbstractController
{
    #[Route('/stages/{id}/postuler', name: 'front_candidature_new')]
    public function new(
        int $id,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $offre = $em->getRepository(OffreStage::class)->find($id);
        if (!$offre) {
            throw $this->createNotFoundException('Offre de stage introuvable');
        }

        $candidature = new Candidature();
        $form = $this->createForm(CandidatureType::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $candidature->setOffreStage($offre);
            $candidature->setStatut('En attente');
            $candidature->setDateCandidature(new \DateTime());

            $cvFile = $form->get('cv')->getData();
            if ($cvFile) {
                $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/cv';
                if (!is_dir($uploadsDir)) {
                    @mkdir($uploadsDir, 0775, true);
                }
                $newFilename = uniqid('cv_') . '.' . $cvFile->guessExtension();
                try {
                    $cvFile->move($uploadsDir, $newFilename);
                    $candidature->setCv('/uploads/cv/' . $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', "Échec de l'upload du CV : " . $e->getMessage());
                    return $this->render('front/pages/candidature/new.html.twig', [
                        'offre' => $offre,
                        'form' => $form->createView(),
                    ]);
                }
            }

            // Vocal recording removed: no audio processing

            $em->persist($candidature);
            $em->flush();

            // Store email in session for quick access to "Mes postulations"
            $request->getSession()->set('candidature_email', $candidature->getEmail());

            $this->addFlash('success', 'Votre candidature a été envoyée avec succès !');
            return $this->redirectToRoute('front_stages');
        }

        return $this->render('front/pages/candidature/new.html.twig', [
            'offre' => $offre,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/mes-postulations', name: 'front_candidature_my', methods: ['GET', 'POST'])]
    public function my(Request $request, EntityManagerInterface $em): Response
    {
        $email = $request->query->get('email') ?: $request->getSession()->get('candidature_email');

        if ($request->isMethod('POST')) {
            $emailPosted = trim((string)$request->request->get('email'));
            if ($emailPosted) {
                return $this->redirectToRoute('front_candidature_my', ['email' => $emailPosted]);
            }
        }

        $candidatures = [];
        if ($email) {
            $candidatures = $em->getRepository(Candidature::class)->findBy(['email' => $email], ['dateCandidature' => 'DESC']);
        }

        return $this->render('front/pages/candidature/my.html.twig', [
            'email' => $email,
            'candidatures' => $candidatures,
        ]);
    }

    #[Route('/mes-postulations/{id}/modifier', name: 'front_candidature_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $candidature = $em->getRepository(Candidature::class)->find($id);
        if (!$candidature) {
            throw $this->createNotFoundException('Candidature introuvable');
        }

        $email = $request->query->get('email') ?: $request->getSession()->get('candidature_email');
        if (!$email || $email !== $candidature->getEmail()) {
            $this->addFlash('error', 'Vous ne pouvez modifier que vos propres candidatures.');
            return $this->redirectToRoute('front_candidature_my', ['email' => $email]);
        }

        $form = $this->createForm(CandidatureType::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cvFile = $form->get('cv')->getData();
            if ($cvFile) {
                $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/cv';
                if (!is_dir($uploadsDir)) {
                    @mkdir($uploadsDir, 0775, true);
                }
                $newFilename = uniqid('cv_') . '.' . $cvFile->guessExtension();
                try {
                    $cvFile->move($uploadsDir, $newFilename);
                    $candidature->setCv('/uploads/cv/' . $newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', "Échec de l'upload du CV : " . $e->getMessage());
                    return $this->render('front/pages/candidature/edit.html.twig', [
                        'offre' => $candidature->getOffreStage(),
                        'form' => $form->createView(),
                        'candidature' => $candidature,
                        'email' => $email,
                    ]);
                }
            }

            // Vocal recording removed: no audio processing

            $em->flush();
            $this->addFlash('success', 'Votre candidature a été mise à jour.');
            return $this->redirectToRoute('front_candidature_my', ['email' => $email]);
        }

        return $this->render('front/pages/candidature/edit.html.twig', [
            'offre' => $candidature->getOffreStage(),
            'form' => $form->createView(),
            'candidature' => $candidature,
            'email' => $email,
        ]);
    }

    #[Route('/mes-postulations/{id}/supprimer', name: 'front_candidature_delete', methods: ['POST'])]
    public function delete(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $candidature = $em->getRepository(Candidature::class)->find($id);
        if (!$candidature) {
            throw $this->createNotFoundException('Candidature introuvable');
        }

        $email = $request->request->get('email') ?: $request->getSession()->get('candidature_email');
        if (!$email || $email !== $candidature->getEmail()) {
            $this->addFlash('error', 'Vous ne pouvez supprimer que vos propres candidatures.');
            return $this->redirectToRoute('front_candidature_my', ['email' => $email]);
        }

        $token = (string) $request->request->get('token');
        if (!$this->isCsrfTokenValid('candidature_delete_' . $candidature->getId(), $token)) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('front_candidature_my', ['email' => $email]);
        }

        $em->remove($candidature);
        $em->flush();

        $this->addFlash('success', 'Votre candidature a été supprimée.');
        return $this->redirectToRoute('front_candidature_my', ['email' => $email]);
    }
}
