<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/admin/search')]
class SearchController extends AbstractController
{
    #[Route('/', name: 'admin_search_global', methods: ['GET'])]
    public function globalSearch(Request $request, EntityManagerInterface $em): Response
    {
        $searchTerm = $request->query->get('search', '');
        
        if (empty($searchTerm)) {
            return $this->render('admin/search/results.html.twig', [
                'searchTerm' => $searchTerm,
                'results' => [],
                'totalResults' => 0
            ]);
        }
        
        $results = [];
        
        // Recherche dans les Quiz
        $quizRepository = $em->getRepository(\App\Entity\Quiz::class);
        $quizzes = $quizRepository->createQueryBuilder('q')
            ->where('q.titre LIKE :search OR q.description LIKE :search')
            ->setParameter('search', '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();
        
        foreach ($quizzes as $quiz) {
            $results['quiz'][] = [
                'type' => 'quiz',
                'title' => $quiz->getTitre(),
                'description' => substr($quiz->getDescription(), 0, 100) . '...',
                'url' => $this->generateUrl('admin_quiz_show', ['idquiz' => $quiz->getIdquiz()]),
                'icon' => 'fa-question-circle',
                'badge' => $quiz->isVisible() ? 'success' : 'secondary',
                'badge_text' => $quiz->isVisible() ? 'Visible' : 'Masqué'
            ];
        }
        
        // Recherche dans les Offres de Stage
        $offreRepository = $em->getRepository(\App\Entity\OffreStage::class);
        $offres = $offreRepository->createQueryBuilder('o')
            ->where('o.titre LIKE :search OR o.description LIKE :search OR o.entreprise LIKE :search')
            ->setParameter('search', '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();
        
        foreach ($offres as $offre) {
            $results['offre_stage'][] = [
                'type' => 'offre_stage',
                'title' => $offre->getTitre(),
                'description' => substr($offre->getDescription(), 0, 100) . '...',
                'entreprise' => $offre->getEntreprise(),
                'url' => $this->generateUrl('admin_offre_stage_show', ['id' => $offre->getId()]),
                'icon' => 'fa-briefcase',
                'badge' => $offre->getStatut() === 'Ouvert' ? 'success' : ($offre->getStatut() === 'Fermé' ? 'danger' : 'warning'),
                'badge_text' => $offre->getStatut()
            ];
        }
        
        // Recherche dans les Candidatures
        $candidatureRepository = $em->getRepository(\App\Entity\Candidature::class);
        $candidatures = $candidatureRepository->createQueryBuilder('c')
            ->where('c.nom LIKE :search OR c.email LIKE :search OR c.lettreMotivation LIKE :search')
            ->setParameter('search', '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();
        
        foreach ($candidatures as $candidature) {
            $results['candidature'][] = [
                'type' => 'candidature',
                'title' => $candidature->getNom(),
                'description' => substr($candidature->getLettreMotivation(), 0, 100) . '...',
                'email' => $candidature->getEmail(),
                'statut' => $candidature->getStatut(),
                'url' => $this->generateUrl('admin_candidature_show', ['id' => $candidature->getId()]),
                'icon' => 'fa-users',
                'badge' => $this->getStatusBadgeColor($candidature->getStatut()),
                'badge_text' => $candidature->getStatut()
            ];
        }
        
        // Recherche dans les Réclamations
        $reclamationRepository = $em->getRepository(\App\Entity\Reclamation::class);
        $reclamations = $reclamationRepository->createQueryBuilder('r')
            ->where('r.titre LIKE :search OR r.description LIKE :search OR r.nom LIKE :search OR r.prenom LIKE :search')
            ->setParameter('search', '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();
        
        foreach ($reclamations as $reclamation) {
            $results['reclamation'][] = [
                'type' => 'reclamation',
                'title' => $reclamation->getTitre(),
                'description' => substr($reclamation->getDescription(), 0, 100) . '...',
                'nom' => $reclamation->getNom() . ' ' . $reclamation->getPrenom(),
                'status' => $reclamation->getStatus(),
                'url' => $this->generateUrl('reclamation_show', ['id' => $reclamation->getId()]),
                'icon' => 'fa-exclamation-circle',
                'badge' => $this->getStatusBadgeColor($reclamation->getStatus()),
                'badge_text' => $reclamation->getStatus()
            ];
        }
        
        // Recherche dans les Filières
        $filiereRepository = $em->getRepository(\App\Entity\Filiere::class);
        $filieres = $filiereRepository->createQueryBuilder('f')
            ->where('f.nom LIKE :search OR f.description LIKE :search')
            ->setParameter('search', '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();
        
        foreach ($filieres as $filiere) {
            $results['filiere'][] = [
                'type' => 'filiere',
                'title' => $filiere->getNom(),
                'description' => substr($filiere->getDescription(), 0, 100) . '...',
                'url' => $this->generateUrl('admin_filiere_show', ['id' => $filiere->getId()]),
                'icon' => 'fa-graduation-cap',
                'badge' => 'info',
                'badge_text' => 'Filière'
            ];
        }
        
        // Recherche dans les Formations
        $formationRepository = $em->getRepository(\App\Entity\Formation::class);
        $formations = $formationRepository->createQueryBuilder('f')
            ->where('f.nom LIKE :search OR f.description LIKE :search')
            ->setParameter('search', '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();
        
        foreach ($formations as $formation) {
            $results['formation'][] = [
                'type' => 'formation',
                'title' => $formation->getNom(),
                'description' => substr($formation->getDescription(), 0, 100) . '...',
                'duree' => $formation->getDuree(),
                'url' => $this->generateUrl('admin_formation_show', ['id' => $formation->getId()]),
                'icon' => 'fa-book',
                'badge' => 'primary',
                'badge_text' => 'Formation'
            ];
        }
        
        // Calcul du total des résultats
        $totalResults = 0;
        foreach ($results as $moduleResults) {
            $totalResults += count($moduleResults);
        }
        
        return $this->render('admin/search/results.html.twig', [
            'searchTerm' => $searchTerm,
            'results' => $results,
            'totalResults' => $totalResults
        ]);
    }
    
    private function getStatusBadgeColor($status): string
    {
        $colors = [
            'En attente' => 'warning',
            'Acceptée' => 'success',
            'Refusée' => 'danger',
            'en cours de traitement' => 'warning',
            'traiter' => 'success',
            'Ouvert' => 'success',
            'Fermé' => 'danger'
        ];
        
        return $colors[$status] ?? 'secondary';
    }
}
