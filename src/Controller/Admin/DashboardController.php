<?php

namespace App\Controller\Admin;

use App\Repository\CoursRepository;
use App\Repository\InscriptionRepository;
use App\Repository\LogAuthentificationRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/dashboard')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard_index', methods: ['GET'])]
    public function index(
        UtilisateurRepository $utilisateurRepository,
        CoursRepository $coursRepository,
        InscriptionRepository $inscriptionRepository,
        LogAuthentificationRepository $logRepository,
    ): Response {
        // User stats
        $totalUsers = $utilisateurRepository->count([]);
        $admins = $utilisateurRepository->countByRole('admin');
        $profs = $utilisateurRepository->countByRole('professeur');
        $etudiants = $utilisateurRepository->countByRole('etudiant');
        $activeUsers = $utilisateurRepository->count(['actif' => true]);

        // Course stats
        $totalCours = $coursRepository->count([]);
        $coursPublies = $coursRepository->count(['statut' => 'publie']);
        $coursBrouillon = $coursRepository->count(['statut' => 'brouillon']);

        // Inscription stats
        $totalInscriptions = $inscriptionRepository->count([]);
        $inscriptionsEnCours = $inscriptionRepository->count(['statut' => 'en_cours']);
        $inscriptionsCompletes = $inscriptionRepository->count(['statut' => 'complete']);

        // Recent data
        $recentUsers = $utilisateurRepository->findBy([], ['dateInscription' => 'DESC'], 5);
        $recentCours = $coursRepository->findBy([], ['dateCreation' => 'DESC'], 5);
        $recentLogs = $logRepository->findBy([], ['dateConnexion' => 'DESC'], 10);

        return $this->render('admin/dashboard/index.html.twig', [
            'totalUsers' => $totalUsers,
            'admins' => $admins,
            'profs' => $profs,
            'etudiants' => $etudiants,
            'activeUsers' => $activeUsers,
            'totalCours' => $totalCours,
            'coursPublies' => $coursPublies,
            'coursBrouillon' => $coursBrouillon,
            'totalInscriptions' => $totalInscriptions,
            'inscriptionsEnCours' => $inscriptionsEnCours,
            'inscriptionsCompletes' => $inscriptionsCompletes,
            'recentUsers' => $recentUsers,
            'recentCours' => $recentCours,
            'recentLogs' => $recentLogs,
        ]);
    }
}
