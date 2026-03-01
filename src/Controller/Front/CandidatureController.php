<?php

namespace App\Controller\Front;

use App\Entity\Candidature;
use App\Entity\OffreStage;
use App\Form\CandidatureType;
use App\Service\AiRecruitmentService;
use App\Service\CvAnalyzerService;
use App\Repository\OffreStagERepository;
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
        EntityManagerInterface $em,
        CvAnalyzerService $cvAnalyzer,
        AiRecruitmentService $aiService
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

            $em->persist($candidature);
            $em->flush();

            // Analyse IA automatique après soumission (Scénario A)
            try {
                if ($candidature->getCv() && $aiService->isConfigured()) {
                    $analyseCv = $cvAnalyzer->analyserCv($candidature->getCv());
                    $candidature->setCompetencesDetectees($analyseCv);

                    $scoring = $aiService->scoreCandidature($candidature, $offre);
                    $candidature->setScoreIa((int) ($scoring['score'] ?? 0));

                    $resume = "Score: " . ($scoring['score'] ?? 0) . "/100\n";
                    $resume .= "Analyse: " . ($scoring['analyse'] ?? '') . "\n";
                    if (!empty($scoring['points_forts'])) {
                        $resume .= "Points forts: " . implode(', ', $scoring['points_forts']);
                    }
                    $candidature->setResumeIa($resume);

                    $em->flush();
                }
            } catch (\Throwable $e) {
                // L'analyse IA est optionnelle, ne pas bloquer la candidature
            }

            // Store email in session
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
    public function my(Request $request, EntityManagerInterface $em, OffreStagERepository $offreRepo, AiRecruitmentService $aiService, CvAnalyzerService $cvAnalyzer): Response
    {
        $email = $request->query->get('email') ?: $request->getSession()->get('candidature_email');

        if ($request->isMethod('POST')) {
            $emailPosted = trim((string)$request->request->get('email'));
            if ($emailPosted) {
                return $this->redirectToRoute('front_candidature_my', ['email' => $emailPosted]);
            }
        }

        $candidatures = [];
        $recommandations = [];

        if ($email) {
            $candidatures = $em->getRepository(Candidature::class)->findBy(['email' => $email], ['dateCandidature' => 'DESC']);

            // Scénario C : Recommandations personnalisées
            if (!empty($candidatures) && $aiService->isConfigured()) {
                try {
                    $derniereCandiature = $candidatures[0];
                    $competences = [];

                    // 1. Essayer les compétences déjà détectées
                    $analyseCv = $derniereCandiature->getCompetencesDetectees();
                    if ($analyseCv && is_array($analyseCv)) {
                        $competences = array_merge(
                            $analyseCv['competences_techniques'] ?? [],
                            $analyseCv['competences_soft'] ?? []
                        );
                    }

                    // 2. Si pas de compétences, analyser le CV à la volée
                    if (empty($competences) && $derniereCandiature->getCv()) {
                        try {
                            $analyseVolee = $cvAnalyzer->analyserCv($derniereCandiature->getCv());
                            $competences = array_merge(
                                $analyseVolee['competences_techniques'] ?? [],
                                $analyseVolee['competences_soft'] ?? []
                            );
                            $derniereCandiature->setCompetencesDetectees($analyseVolee);
                            $em->flush();
                        } catch (\Throwable $e) {
                            // Fallback ci-dessous
                        }
                    }

                    // 3. Si toujours vide, extraire des mots-clés depuis les offres postulées
                    if (empty($competences)) {
                        foreach ($candidatures as $c) {
                            $offre = $c->getOffreStage();
                            if ($offre) {
                                $competences[] = $offre->getTitre();
                                if ($offre->getCompetencesRequises()) {
                                    $competences = array_merge($competences, $offre->getCompetencesRequises());
                                }
                            }
                        }
                        $competences = array_unique(array_filter($competences));
                    }

                    if (!empty($competences)) {
                        $offresOuvertes = $offreRepo->findByStatut('Ouvert');
                        $offresPostulees = array_map(fn($c) => $c->getOffreStage()?->getId(), $candidatures);
                        $offresNonPostulees = array_filter($offresOuvertes, fn($o) => !in_array($o->getId(), $offresPostulees));

                        if (!empty($offresNonPostulees)) {
                            $recs = $aiService->recommanderOffres(
                                $competences,
                                $derniereCandiature->getNiveauEtude() ?? 'Non spécifié',
                                array_values($offresNonPostulees)
                            );

                            foreach ($recs as $rec) {
                                $offre = $offreRepo->find($rec['id'] ?? 0);
                                if ($offre) {
                                    $recommandations[] = [
                                        'offre' => $offre,
                                        'pertinence' => $rec['pertinence'] ?? 0,
                                        'raison' => $rec['raison'] ?? '',
                                    ];
                                }
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    // Recommandations IA optionnelles
                }
            }
        }

        return $this->render('front/pages/candidature/my.html.twig', [
            'email' => $email,
            'candidatures' => $candidatures,
            'recommandations' => $recommandations,
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
