<?php
// src/Controller/Admin/SimulateurController.php

namespace App\Controller\Admin;

use App\Entity\Simulation;
use App\Repository\SimulationRepository;
use App\Service\SimulatorConfigService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/simulateur')]
class SimulateurController extends AbstractController
{
    private SimulatorConfigService $configService;
    private array $filieresFixes;

    public function __construct(SimulatorConfigService $configService)
    {
        $this->configService = $configService;
        $this->filieresFixes = $this->getFilieresFixes();
    }

    #[Route('/', name: 'admin_simulateur_index', methods: ['GET'])]
    public function index(SimulationRepository $simulationRepository): Response
    {
        $simulations = $simulationRepository->findBy([], ['dateSimulation' => 'DESC'], 10);
        $config = $this->configService->getConfig();
        
        return $this->render('admin/simulateur/index.html.twig', [
            'simulations' => $simulations,
            'config' => $config
        ]);
    }

    #[Route('/config/save-weights', name: 'admin_simulateur_config_save_weights', methods: ['POST'])]
    public function saveWeightsConfig(Request $request): Response
    {
        $weights = [
            'moyenne' => (int) $request->request->get('weight_moyenne', 40),
            'specialites' => (int) $request->request->get('weight_specialites', 40),
            'preferences' => (int) $request->request->get('weight_preferences', 20)
        ];
        
        $total = array_sum($weights);
        if ($total !== 100) {
            $this->addFlash('error', 'Le total des poids doit être exactement 100% (actuellement: ' . $total . '%)');
            return $this->redirectToRoute('admin_simulateur_index');
        }
        
        $this->configService->updateWeights($weights);
        
        $this->addFlash('success', 'Configuration des poids sauvegardée avec succès !');
        return $this->redirectToRoute('admin_simulateur_index');
    }

    #[Route('/calcul', name: 'admin_simulateur_calcul', methods: ['POST'])]
    public function calculer(
        Request $request, 
        EntityManagerInterface $entityManager
    ): Response
    {
        $moyenne = (float) $request->request->get('moyenne');
        $specialites = $request->request->all('specialites');
        $preferences = $request->request->all('preferences') ?? [];
        
        if (count($specialites) !== 3) {
            $this->addFlash('error', 'Veuillez sélectionner exactement 3 spécialités.');
            return $this->redirectToRoute('admin_simulateur_index');
        }
        
        if ($moyenne < 0 || $moyenne > 20) {
            $this->addFlash('error', 'La moyenne doit être comprise entre 0 et 20.');
            return $this->redirectToRoute('admin_simulateur_index');
        }
        
        $config = $this->configService->getConfig();
        
        $simulation = new Simulation();
        $simulation->setMoyenne((string) $moyenne);
        $simulation->setSpecialites($specialites);
        $simulation->setPreferences($preferences);
        $simulation->setDateSimulation(new \DateTime());
        
        $resultats = $this->calculateResults($moyenne, $specialites, $preferences, $config);
        
        $simulation->setResultats($resultats);
        
        $entityManager->persist($simulation);
        $entityManager->flush();
        
        return $this->render('admin/simulateur/resultats.html.twig', [
            'resultats' => $resultats,
            'simulation' => $simulation,
            'moyenne' => $moyenne,
            'specialites' => $specialites,
            'preferences' => $preferences,
            'config' => $config
        ]);
    }

    #[Route('/resultats/{id}', name: 'admin_simulateur_resultats', methods: ['GET'])]
    public function resultats(Simulation $simulation): Response
    {
        $config = $this->configService->getConfig();
        
        $resultats = $simulation->getResultats() ?? [];
        foreach ($resultats as &$result) {
            if (!isset($result['pourcentage']) && isset($result['taux_acces'])) {
                $result['pourcentage'] = $result['taux_acces'];
            }
        }
        
        return $this->render('admin/simulateur/resultats.html.twig', [
            'simulation' => $simulation,
            'resultats' => $resultats,
            'moyenne' => (float) $simulation->getMoyenne(),
            'specialites' => $simulation->getSpecialites(),
            'preferences' => $simulation->getPreferences() ?? [],
            'config' => $config
        ]);
    }

    #[Route('/modifier/{id}', name: 'admin_simulateur_modifier', methods: ['GET', 'POST'])]
    public function modifier(
        Request $request, 
        Simulation $simulation,
        EntityManagerInterface $entityManager
    ): Response
    {
        $config = $this->configService->getConfig();
        $resultats = $simulation->getResultats() ?? [];
        
        if ($request->isMethod('POST')) {
            // Récupérer les taux modifiés
            $newResultats = [];
            foreach ($this->filieresFixes as $filiere) {
                $filiereId = $filiere['id'];
                $nouveauTaux = (float) $request->request->get('taux_' . $filiereId, 0);
                
                // Trouver le résultat existant pour cette filière
                $ancienResultat = null;
                foreach ($resultats as $result) {
                    if ($result['filiere']['id'] == $filiereId) {
                        $ancienResultat = $result;
                        break;
                    }
                }
                
                // Créer le nouveau résultat
                $newResultats[] = [
                    'filiere' => [
                        'id' => $filiere['id'],
                        'nom' => $filiere['nom'],
                        'description' => $filiere['description']
                    ],
                    'pourcentage' => $nouveauTaux,
                    'taux_acces' => $nouveauTaux,
                    'details' => $ancienResultat['details'] ?? [
                        'note_moyenne' => ((float) $simulation->getMoyenne() / 20) * 100,
                        'contribution_moyenne' => 0,
                        'score_specialites' => 0,
                        'contribution_specialites' => 0,
                        'score_preferences' => 0,
                        'contribution_preferences' => 0,
                        'poids' => $config['weights']
                    ]
                ];
            }
            
            // Trier par pourcentage décroissant
            usort($newResultats, function($a, $b) {
                return $b['pourcentage'] <=> $a['pourcentage'];
            });
            
            // Mettre à jour la simulation
            $simulation->setResultats($newResultats);
            $entityManager->flush();
            
            $this->addFlash('success', 'Les taux d\'accès ont été modifiés avec succès !');
            return $this->redirectToRoute('admin_simulateur_resultats', ['id' => $simulation->getId()]);
        }
        
        return $this->render('admin/simulateur/modifier.html.twig', [
            'simulation' => $simulation,
            'resultats' => $resultats,
            'config' => $config,
            'filieres' => $this->filieresFixes
        ]);
    }

    #[Route('/supprimer/{id}', name: 'admin_simulateur_supprimer', methods: ['POST'])]
    public function supprimer(
        Request $request, 
        Simulation $simulation,
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($this->isCsrfTokenValid('delete' . $simulation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($simulation);
            $entityManager->flush();
            
            $this->addFlash('success', 'La simulation a été supprimée avec succès !');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }
        
        return $this->redirectToRoute('admin_simulateur_index');
    }

    #[Route('/reset-config', name: 'admin_simulateur_reset_config', methods: ['POST'])]
    public function resetConfig(): Response
    {
        $this->configService->resetConfig();
        $this->addFlash('success', 'Configuration réinitialisée avec succès !');
        return $this->redirectToRoute('admin_simulateur_index');
    }
    
    private function getFilieresFixes(): array
    {
        return [
            [
                'id' => 1,
                'nom' => 'Médecine',
                'description' => 'Formation aux métiers de la santé et de la médecine.',
                'specialites_correspondantes' => ['Physique-Chimie', 'SVT', 'Mathématiques'],
                'preferences_correspondantes' => ['Scientifique', 'Social', 'Humain'],
                'couleur' => 'primary'
            ],
            [
                'id' => 2,
                'nom' => 'Ingénierie',
                'description' => 'Formation aux métiers de l\'ingénierie et de la conception technique.',
                'specialites_correspondantes' => ['Mathématiques', 'Physique-Chimie', 'NSI'],
                'preferences_correspondantes' => ['Scientifique', 'Technique', 'Analytique'],
                'couleur' => 'info'
            ],
            [
                'id' => 3,
                'nom' => 'Informatique',
                'description' => 'Formation aux métiers du développement et des technologies numériques.',
                'specialites_correspondantes' => ['Mathématiques', 'NSI', 'Physique-Chimie'],
                'preferences_correspondantes' => ['Scientifique', 'Technique', 'Logique'],
                'couleur' => 'success'
            ],
            [
                'id' => 4,
                'nom' => 'Commerce',
                'description' => 'Formation aux métiers du management, marketing et gestion d\'entreprise.',
                'specialites_correspondantes' => ['SES', 'Mathématiques', 'Histoire-Géographie'],
                'preferences_correspondantes' => ['Social', 'Littéraire', 'Relationnel'],
                'couleur' => 'warning'
            ],
            [
                'id' => 5,
                'nom' => 'Avocat',
                'description' => 'Formation aux métiers du droit et de la justice.',
                'specialites_correspondantes' => ['Littérature', 'SES', 'Histoire-Géographie'],
                'preferences_correspondantes' => ['Littéraire', 'Social', 'Analytique'],
                'couleur' => 'danger'
            ],
            [
                'id' => 6,
                'nom' => 'Architecte',
                'description' => 'Formation aux métiers de l\'architecture et de l\'urbanisme.',
                'specialites_correspondantes' => ['Mathématiques', 'Physique-Chimie', 'Arts'],
                'preferences_correspondantes' => ['Technique', 'Créatif', 'Scientifique'],
                'couleur' => 'purple'
            ]
        ];
    }
    
    private function calculateResults(float $moyenne, array $specialites, array $preferences, array $config): array
    {
        $weights = $config['weights'];
        $results = [];
        
        $noteMoyenne = ($moyenne / 20) * 100;
        
        foreach ($this->filieresFixes as $filiere) {
            $scoreSpecialites = $this->calculateScoreSpecialites($specialites, $filiere['specialites_correspondantes']);
            $scorePreferences = $this->calculateScorePreferences($preferences, $filiere['preferences_correspondantes']);
            
            $scoreFinal = 
                ($noteMoyenne * $weights['moyenne'] / 100) +
                ($scoreSpecialites * $weights['specialites'] / 100) +
                ($scorePreferences * $weights['preferences'] / 100);
            
            $pourcentage = min(100, round($scoreFinal, 1));
            
            $results[] = [
                'filiere' => [
                    'id' => $filiere['id'],
                    'nom' => $filiere['nom'],
                    'description' => $filiere['description']
                ],
                'pourcentage' => $pourcentage,
                'taux_acces' => $pourcentage,
                'details' => [
                    'note_moyenne' => round($noteMoyenne, 1),
                    'contribution_moyenne' => round($noteMoyenne * $weights['moyenne'] / 100, 1),
                    'score_specialites' => $scoreSpecialites,
                    'contribution_specialites' => round($scoreSpecialites * $weights['specialites'] / 100, 1),
                    'score_preferences' => $scorePreferences,
                    'contribution_preferences' => round($scorePreferences * $weights['preferences'] / 100, 1),
                    'poids' => $weights
                ]
            ];
        }
        
        usort($results, function($a, $b) {
            return $b['pourcentage'] <=> $a['pourcentage'];
        });
        
        return $results;
    }
    
    private function calculateScoreSpecialites(array $specialitesEtudiant, array $specialitesFiliere): float
    {
        $correspondances = array_intersect($specialitesEtudiant, $specialitesFiliere);
        $nombreCorrespondances = count($correspondances);
        
        return ($nombreCorrespondances / 3) * 100;
    }
    
    private function calculateScorePreferences(array $preferencesEtudiant, array $preferencesFiliere): float
    {
        if (empty($preferencesEtudiant)) {
            return 0;
        }
        
        $correspondances = array_intersect($preferencesEtudiant, $preferencesFiliere);
        $nombreCorrespondances = count($correspondances);
        $nombrePreferences = count($preferencesEtudiant);
        
        return ($nombreCorrespondances / max(1, $nombrePreferences)) * 100;
    }
}