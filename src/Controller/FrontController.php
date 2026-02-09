<?php
// src/Controller/FrontController.php

namespace App\Controller;

use App\Entity\Simulation;
use App\Repository\FiliereRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\OffreStagERepository;
use App\Entity\OffreStage;

#[Route('/')]
class FrontController extends AbstractController
{
    #[Route('/', name: 'front_home')]
    public function index(): Response
    {
        return $this->render('front/pages/home.html.twig');
    }

    #[Route('/cours-ancien', name: 'front_cours_legacy')]
    public function cours(): Response
    {
        return $this->redirectToRoute('front_cours_list');
    }

    #[Route('/a-propos', name: 'front_about')]
    public function about(): Response
    {
        return $this->render('front/pages/about.html.twig');
    }

    #[Route('/formations', name: 'front_formations')]
    public function formations(): Response
    {
        return $this->render('front/pages/formations.html.twig');
    }

    #[Route('/filieres', name: 'front_filieres')]
    public function filieres(): Response
    {
        return $this->render('front/pages/filieres.html.twig');
    }

    #[Route('/stages', name: 'front_stages')]
    public function stages(Request $request, OffreStagERepository $offreRepo): Response
    {
        $search = $request->query->get('search');
        $minSalary = $request->query->get('minSalary');
        $maxSalary = $request->request->get('maxSalary');
        $minDays = $request->query->get('minDays');
        $maxDays = $request->query->get('maxDays');

        $minSalaryVal = is_numeric($minSalary) ? (float)$minSalary : null;
        $maxSalaryVal = is_numeric($maxSalary) ? (float)$maxSalary : null;
        $minDaysVal = is_numeric($minDays) ? (int)$minDays : null;
        $maxDaysVal = is_numeric($maxDays) ? (int)$maxDays : null;

        $offres_stage = $offreRepo->searchWithFilters($search, $minSalaryVal, $maxSalaryVal, $minDaysVal, $maxDaysVal);

        // Creative front stats for offers
        $totalOffers = \count($offres_stage);
        $open = 0; $closed = 0; $filled = 0;
        $companies = [];
        $salaries = [];
        $durations = [];
        foreach ($offres_stage as $o) {
            $status = (string) $o->getStatut();
            if ($status === 'Ouvert') { $open++; }
            elseif ($status === 'Fermé') { $closed++; }
            else { $filled++; }

            $comp = (string) ($o->getEntreprise() ?? '—');
            if (!isset($companies[$comp])) { $companies[$comp] = 0; }
            $companies[$comp]++;

            if (null !== $o->getSalaire()) { $salaries[] = (float) $o->getSalaire(); }
            if (null !== $o->getDureeJours()) { $durations[] = (int) $o->getDureeJours(); }
        }
        arsort($companies);
        $topCompanies = [];
        foreach (array_slice($companies, 0, 5, true) as $name => $count) {
            $topCompanies[] = ['name' => $name, 'count' => $count];
        }
        $avgSalary = !empty($salaries) ? round(array_sum($salaries) / max(1, count($salaries)), 2) : null;
        sort($durations);
        $medianDuration = null;
        if (!empty($durations)) {
            $mid = (int) floor((count($durations) - 1) / 2);
            if (count($durations) % 2 === 1) { $medianDuration = $durations[$mid]; }
            else { $medianDuration = (int) round(($durations[$mid] + $durations[$mid + 1]) / 2); }
        }
        $statusBreakdown = [
            ['label' => 'Ouvert', 'value' => $open, 'color' => '#20c997'],
            ['label' => 'Fermé', 'value' => $closed, 'color' => '#e74c3c'],
            ['label' => 'Pourvu', 'value' => $filled, 'color' => '#f1c40f'],
        ];

        return $this->render('front/pages/stages.html.twig', [
            'offres_stage' => $offres_stage,
            'search' => $search,
            'minSalary' => $minSalary,
            'maxSalary' => $maxSalary,
            'minDays' => $minDays,
            'maxDays' => $maxDays,
            'total_offers' => $totalOffers,
            'status_breakdown' => $statusBreakdown,
            'top_companies' => $topCompanies,
            'avg_salary' => $avgSalary,
            'median_duration' => $medianDuration,
        ]);
    }

    #[Route('/stages/{id}', name: 'front_stage_show', requirements: ['id' => '\\d+'])]
    public function stageShow(int $id, OffreStagERepository $offreRepo): Response
    {
        /** @var OffreStage|null $offre */
        $offre = $offreRepo->find($id);
        if (!$offre) {
            throw $this->createNotFoundException('Offre introuvable');
        }

        return $this->render('front/pages/stage_show.html.twig', [
            'offre' => $offre,
        ]);
    }

    #[Route('/stages/apply', name: 'front_stages_apply', methods: ['POST'])]
    public function applyStage(Request $request): RedirectResponse
    {
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $jobTitle = $request->request->get('job_title');

        if (!$name || !$email) {
            $this->addFlash('error', 'Veuillez renseigner votre nom et votre email.');
            return $this->redirectToRoute('front_stages');
        }

        $this->addFlash('success', sprintf('Candidature envoyée pour "%s". Nous vous recontacterons bientôt.', $jobTitle ?: 'l\'offre'));
        return $this->redirectToRoute('front_stages');
    }

    // =========================================================================
    // SIMULATEUR D'ORIENTATION - DÉBUT
    // =========================================================================

    #[Route('/simulateur', name: 'front_simulateur')]
    public function simulateur(): Response
    {
        return $this->render('front/pages/simulateur.html.twig');
    }

    #[Route('/simulateur/calcul', name: 'front_simulateur_calcul', methods: ['POST'])]
    public function calculerSimulation(
        Request $request,
        EntityManagerInterface $entityManager,
        FiliereRepository $filiereRepository
    ): JsonResponse
    {
        try {
            // Debug: Activer les erreurs
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            
            // Récupérer les données JSON
            $content = $request->getContent();
            
            if (empty($content)) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Aucune donnée reçue'
                ], 400);
            }
            
            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Format JSON invalide'
                ], 400);
            }
            
            // Validation
            if (!isset($data['moyenne']) || !isset($data['specialites'])) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Données manquantes'
                ], 400);
            }
            
            $moyenne = (float) $data['moyenne'];
            $specialites = $data['specialites'];
            $preferences = $data['preferences'] ?? [];
            
            // Validation de la moyenne
            if ($moyenne < 0 || $moyenne > 20) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'La moyenne doit être entre 0 et 20'
                ], 400);
            }
            
            // Validation des spécialités
            if (!is_array($specialites) || count($specialites) !== 3) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Vous devez sélectionner exactement 3 spécialités'
                ], 400);
            }
            
            // Créer une nouvelle simulation
            $simulation = new Simulation();
            $simulation->setMoyenne((string) $moyenne);
            $simulation->setSpecialites($specialites);
            $simulation->setPreferences($preferences);
            $simulation->setDateSimulation(new \DateTime());
            
            // Calculer les résultats
            $resultats = $this->calculerResultats($moyenne, $specialites, $preferences, $filiereRepository);
            $simulation->setResultats($resultats);
            
            // Sauvegarder en base
            $entityManager->persist($simulation);
            $entityManager->flush();
            
            // Retourner le succès avec l'ID de simulation
            return new JsonResponse([
                'success' => true,
                'data' => [
                    'simulation_id' => $simulation->getId(),
                    'moyenne' => $moyenne,
                    'specialites' => $specialites,
                    'preferences' => $preferences,
                    'resultats_count' => count($resultats)
                ]
            ]);
            
        } catch (\Exception $e) {
            // Retourner l'erreur en JSON
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine()
            ], 500);
        }
    }

    #[Route('/simulateur/resultats/{id}', name: 'front_simulateur_resultats')]
    public function afficherResultats(int $id, EntityManagerInterface $entityManager): Response
    {
        $simulation = $entityManager->getRepository(Simulation::class)->find($id);
        
        if (!$simulation) {
            $this->addFlash('error', 'Simulation introuvable');
            return $this->redirectToRoute('front_simulateur');
        }
        
        $resultats = $simulation->getResultats() ?? [];
        
        // S'assurer que les résultats sont triés par score décroissant
        usort($resultats, function($a, $b) {
            $scoreA = $a['pourcentage'] ?? $a['taux_acces'] ?? 0;
            $scoreB = $b['pourcentage'] ?? $b['taux_acces'] ?? 0;
            return $scoreB <=> $scoreA;
        });
        
        return $this->render('front/pages/simulateur_resultats.html.twig', [
            'simulation' => $simulation,
            'resultats' => $resultats
        ]);
    }

    /**
     * Calcule les résultats de la simulation
     */
    private function calculerResultats(
        float $moyenne,
        array $specialites,
        array $preferences,
        FiliereRepository $filiereRepository
    ): array
    {
        // Récupérer toutes les filières de la base
        $filieres = $filiereRepository->findAll();
        
        $resultats = [];
        
        // Configuration des poids (comme dans le back)
        $poidsMoyenne = 30;
        $poidsSpecialites = 60;
        $poidsPreferences = 10;
        
        foreach ($filieres as $filiere) {
            // Calculer le score de la moyenne (sur 100)
            $scoreMoyenne = ($moyenne / 20) * 100;
            
            // Calculer le score des spécialités
            $scoreSpecialites = 0;
            $mappingSpecialites = $this->getMappingSpecialites($filiere);
            
            foreach ($specialites as $spe) {
                if (in_array($spe, $mappingSpecialites)) {
                    $scoreSpecialites += 33.33; // +33.33% par correspondance
                }
            }
            $scoreSpecialites = min(100, $scoreSpecialites);
            
            // Calculer le score des préférences
            $scorePreferences = 0;
            if (!empty($preferences)) {
                $preferencesFiliere = $this->getMappingPreferences($filiere);
                foreach ($preferences as $pref) {
                    if (in_array($pref, $preferencesFiliere)) {
                        $scorePreferences += (100 / count($preferences));
                    }
                }
                $scorePreferences = min(100, $scorePreferences);
            }
            
            // Calculer le score final pondéré
            $scoreFinal = 
                ($scoreMoyenne * $poidsMoyenne / 100) +
                ($scoreSpecialites * $poidsSpecialites / 100) +
                ($scorePreferences * $poidsPreferences / 100);
            
            $pourcentage = min(100, round($scoreFinal, 1));
            
            // Ne garder que les filières avec un score > 0
            if ($pourcentage > 0) {
                $resultats[] = [
                    'filiere' => [
                        'id' => $filiere->getId(),
                        'nom' => $filiere->getNom(),
                        'description' => $filiere->getDescription()
                    ],
                    'pourcentage' => $pourcentage,
                    'taux_acces' => $pourcentage,
                    'details' => [
                        'note_moyenne' => round($scoreMoyenne, 1),
                        'contribution_moyenne' => round($scoreMoyenne * $poidsMoyenne / 100, 1),
                        'score_specialites' => round($scoreSpecialites, 1),
                        'contribution_specialites' => round($scoreSpecialites * $poidsSpecialites / 100, 1),
                        'score_preferences' => round($scorePreferences, 1),
                        'contribution_preferences' => round($scorePreferences * $poidsPreferences / 100, 1),
                        'poids' => [
                            'moyenne' => $poidsMoyenne,
                            'specialites' => $poidsSpecialites,
                            'preferences' => $poidsPreferences
                        ]
                    ]
                ];
            }
        }
        
        // Trier par pourcentage décroissant
        usort($resultats, function($a, $b) {
            return $b['pourcentage'] <=> $a['pourcentage'];
        });
        
        return $resultats;
    }

    /**
     * Mapping des spécialités par filière
     */
    private function getMappingSpecialites($filiere): array
    {
        $mapping = [
            'Médecine' => ['SVT', 'Physique-Chimie', 'Mathématiques'],
            'Ingénierie' => ['Mathématiques', 'Physique-Chimie', 'NSI'],
            'Informatique' => ['Mathématiques', 'NSI', 'Physique-Chimie'],
            'Commerce' => ['SES', 'Mathématiques', 'Histoire-Géographie'],
            'Droit' => ['Littérature', 'SES', 'Histoire-Géographie'],
            'Avocat' => ['Littérature', 'SES', 'Histoire-Géographie'],
            'Architecture' => ['Mathématiques', 'Physique-Chimie', 'Arts'],
            'Architecte' => ['Mathématiques', 'Physique-Chimie', 'Arts'],
        ];
        
        $nomFiliere = $filiere->getNom();
        
        // Recherche exacte
        if (isset($mapping[$nomFiliere])) {
            return $mapping[$nomFiliere];
        }
        
        // Recherche partielle (si le nom de filière contient un des mots-clés)
        foreach ($mapping as $key => $specialites) {
            if (stripos($nomFiliere, $key) !== false) {
                return $specialites;
            }
        }
        
        // Par défaut: Mathématiques si pas de correspondance
        return ['Mathématiques'];
    }

    /**
     * Mapping des préférences par filière
     */
    private function getMappingPreferences($filiere): array
    {
        $mapping = [
            'Médecine' => ['Scientifique', 'Social', 'Humain'],
            'Ingénierie' => ['Scientifique', 'Technique', 'Analytique'],
            'Informatique' => ['Scientifique', 'Technique', 'Logique'],
            'Commerce' => ['Social', 'Relationnel'],
            'Droit' => ['Littéraire', 'Social', 'Analytique'],
            'Avocat' => ['Littéraire', 'Social', 'Analytique'],
            'Architecture' => ['Technique', 'Créatif', 'Scientifique'],
            'Architecte' => ['Technique', 'Créatif', 'Scientifique'],
        ];
        
        $nomFiliere = $filiere->getNom();
        
        // Recherche exacte
        if (isset($mapping[$nomFiliere])) {
            return $mapping[$nomFiliere];
        }
        
        // Recherche partielle
        foreach ($mapping as $key => $preferences) {
            if (stripos($nomFiliere, $key) !== false) {
                return $preferences;
            }
        }
        
        // Par défaut
        return ['Scientifique'];
    }

    // =========================================================================
    // SIMULATEUR D'ORIENTATION - FIN
    // =========================================================================

    #[Route('/contact', name: 'front_contact')]
    public function contact(): Response
    {
        return $this->render('front/pages/contact.html.twig');
    }

    #[Route('/contact/submit', name: 'front_contact_submit', methods: ['POST'])]
    public function contactSubmit(Request $request): RedirectResponse
    {
        $nom = $request->request->get('nom');
        $email = $request->request->get('email');
        $message = $request->request->get('message');

        if (empty($nom) || empty($email) || empty($message)) {
            $this->addFlash('error', 'Veuillez remplir tous les champs obligatoires.');
            return $this->redirectToRoute('front_contact');
        }

        $this->addFlash('success', 'Votre message a été envoyé. Nous vous répondrons sous 48h.');
        return $this->redirectToRoute('front_contact');
    }

    #[Route('/faq', name: 'front_faq')]
    public function faq(): Response
    {
        return $this->render('front/pages/faq.html.twig');
    }

    #[Route('/search', name: 'front_search')]
    public function search(): Response
    {
        return $this->render('front/pages/search.html.twig');
    }
}