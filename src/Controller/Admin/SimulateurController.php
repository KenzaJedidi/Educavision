<?php

namespace App\Controller\Admin;

use App\Entity\Simulation;
use App\Repository\SimulationRepository;
use App\Repository\FiliereRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/simulateur')]
class SimulateurController extends AbstractController
{
    #[Route('/', name: 'admin_simulateur_index', methods: ['GET'])]
    public function index(SimulationRepository $simulationRepository): Response
    {
        return $this->render('admin/simulateur/index.html.twig', [
            'simulations' => $simulationRepository->findAll(),
        ]);
    }

    #[Route('/calcul', name: 'admin_simulateur_calcul', methods: ['POST'])]
    public function calculer(
        Request $request, 
        FiliereRepository $filiereRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        // Récupérer les données du formulaire
        $moyenne = $request->request->get('moyenne');
        $specialites = $request->request->get('specialites', []);
        $preferences = $request->request->get('preferences', []);

        // Créer une nouvelle simulation
        $simulation = new Simulation();
        $simulation->setMoyenne($moyenne);
        $simulation->setSpecialites($specialites);
        $simulation->setPreferences($preferences);
        $simulation->setDateSimulation(new \DateTime());

        // Calcul du score pour chaque filière
        $filieres = $filiereRepository->findAll();
        $resultats = [];

        foreach ($filieres as $filiere) {
            $score = $this->calculerScore($moyenne, $specialites, $preferences, $filiere);
            $resultats[] = [
                'filiere' => $filiere->getNom(),
                'pourcentage' => $score
            ];
        }

        // Trier par pourcentage décroissant
        usort($resultats, function($a, $b) {
            return $b['pourcentage'] <=> $a['pourcentage'];
        });

        $simulation->setResultats($resultats);

        $entityManager->persist($simulation);
        $entityManager->flush();

        return $this->render('admin/simulateur/resultats.html.twig', [
            'resultats' => $resultats,
            'simulation' => $simulation
        ]);
    }

    private function calculerScore($moyenne, $specialites, $preferences, $filiere): int
    {
        $score = 0;

        // Score basé sur la moyenne (40% du score total)
        if ($moyenne >= 16) {
            $score += 40;
        } elseif ($moyenne >= 14) {
            $score += 30;
        } elseif ($moyenne >= 12) {
            $score += 20;
        } else {
            $score += 10;
        }

        // Score basé sur les spécialités (40% du score total)
        // TODO: Ajouter la logique selon les spécialités correspondantes à chaque filière
        $score += count($specialites) * 10;

        // Score basé sur les préférences (20% du score total)
        // TODO: Comparer les préférences avec le nom/description de la filière
        $score += count($preferences) * 5;

        // Normaliser le score sur 100
        return min(100, $score);
    }
}
