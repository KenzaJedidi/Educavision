<?php
namespace App\Controller\Admin;

use App\Entity\Quiz;
use App\Entity\Result;
use App\Form\QuizType;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/quiz')]
class QuizController extends AbstractController
{
    #[Route('/', name: 'admin_quiz_index', methods: ['GET'])]
    public function index(QuizRepository $quizRepository): Response
    {
        return $this->render('admin/quiz/index.html.twig', [
            'quizs' => $quizRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_quiz_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            // CSRF validation
            if (!$this->isCsrfTokenValid('quiz_new', $request->request->get('_token'))) {
                $this->addFlash('error', 'Jeton CSRF invalide.');
                return $this->render('admin/quiz/new.html.twig');
            }

            // Server-side validation
            $titre = trim($request->request->get('titre', ''));
            $description = trim($request->request->get('description', ''));
            $duree = $request->request->get('duree');

            $errors = [];
            if (empty($titre)) {
                $errors[] = 'Le titre du quiz est obligatoire.';
            } elseif (mb_strlen($titre) < 3) {
                $errors[] = 'Le titre doit contenir au moins 3 caractères.';
            } elseif (mb_strlen($titre) > 255) {
                $errors[] = 'Le titre ne doit pas dépasser 255 caractères.';
            }
            if ($duree !== null && $duree !== '' && (!is_numeric($duree) || $duree < 1)) {
                $errors[] = 'La durée doit être un nombre positif.';
            }
            if (mb_strlen($description) > 1000) {
                $errors[] = 'La description ne doit pas dépasser 1000 caractères.';
            }

            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error);
                }
                return $this->render('admin/quiz/new.html.twig');
            }

            $quiz = new Quiz();
            $quiz->setTitre($titre);
            $quiz->setDescription($description ?: null);
            $quiz->setVisible($request->request->get('visible', false));
            $quiz->setDuree($duree ? (int)$duree : 30);

            $questions = $request->request->all('questions');
            if ($questions) {
                foreach ($questions as $qData) {
                    if (empty(trim($qData['texte'] ?? ''))) {
                        continue; // Ignorer les questions vides
                    }
                    
                    $question = new \App\Entity\Question();
                    $question->setTexte($qData['texte']);
                    $question->setQuiz($quiz);
                    $question->setPoints($qData['points'] ?? 10); // 10 points par défaut
                    
                    // Récupérer les indices des réponses correctes
                    $correctAnswers = $qData['correct_answers'] ?? [];
                    
                    if (isset($qData['answers'])) {
                        foreach ($qData['answers'] as $index => $answerText) {
                            if (empty(trim($answerText))) {
                                continue; // Ignorer les réponses vides
                            }
                            
                            $answer = new \App\Entity\Answer();
                            $answer->setTexte($answerText);
                            $answer->setCorrect(in_array($index, $correctAnswers));
                            $answer->setQuestion($question);
                            $em->persist($answer);
                            $question->addAnswer($answer);
                        }
                    }
                    
                    // Vérifier que la question a au moins 2 réponses
                    if ($question->getAnswers()->count() >= 2) {
                        $em->persist($question);
                        $quiz->addQuestion($question);
                    }
                }
            }
            
            // Vérifier que le quiz a au moins une question
            if ($quiz->getQuestions()->count() > 0) {
                $em->persist($quiz);
                $em->flush();
                $this->addFlash('success', 'Quiz créé avec succès !');
                return $this->redirectToRoute('admin_quiz_index');
            } else {
                $this->addFlash('error', 'Le quiz doit contenir au moins une question avec au moins 2 réponses.');
            }
        }
        return $this->render('admin/quiz/new.html.twig');
    }

    #[Route('/statistics', name: 'admin_quiz_statistics', methods: ['GET'])]
    public function statistics(EntityManagerInterface $em): Response
    {
        $quizRepository = $em->getRepository(Quiz::class);
        
        $quizzes = $quizRepository->findAll();
        $statistiques = [];
        
        foreach ($quizzes as $quiz) {
            // Utiliser DQL pour récupérer les résultats
            $results = $em->getRepository(Result::class)->findBy(['quiz' => $quiz]);
            
            if (empty($results)) {
                continue;
            }
            
            $totalParticipants = count($results);
            $totalScore = 0;
            $totalQuestions = $quiz->getQuestions()->count();
            
            foreach ($results as $result) {
                $totalScore += $result->getScore();
            }
            
            $moyenne = $totalQuestions > 0 ? ($totalScore / ($totalQuestions * $totalParticipants)) * 100 : 0;
            
            $statistiques[] = [
                'quiz' => $quiz,
                'totalParticipants' => $totalParticipants,
                'moyenne' => round($moyenne, 1),
                'totalScore' => $totalScore,
                'totalQuestions' => $totalQuestions
            ];
        }
        
        // Trier par moyenne décroissante
        usort($statistiques, function($a, $b) {
            return $b['moyenne'] <=> $a['moyenne'];
        });
        
        return $this->render('admin/quiz/statistics.html.twig', [
            'statistiques' => $statistiques
        ]);
    }

    #[Route('/{idquiz}/export/csv', name: 'admin_quiz_export_csv', methods: ['GET'])]
    public function exportCsv(Quiz $quiz, EntityManagerInterface $em): Response
    {
        $results = $em->getRepository(Result::class)->findBy(['quiz' => $quiz], ['datepassage' => 'DESC']);
        
        $csvContent = "\xEF\xBB\xBF"; // UTF-8 BOM
        $csvContent .= "Quiz: " . $quiz->getTitre() . "\n";
        $csvContent .= "Description: " . $quiz->getDescription() . "\n";
        $csvContent .= "Date d'export: " . date('d/m/Y H:i:s') . "\n\n";
        
        $csvContent .= "Date de passage;Utilisateur;Score;Score Maximum;Pourcentage;Statut\n";
        
        foreach ($results as $result) {
            $totalQuestions = $quiz->getQuestions()->count();
            $percentage = $totalQuestions > 0 ? round(($result->getScore() / $totalQuestions) * 100, 1) : 0;
            $statut = $percentage >= 80 ? 'Excellent' : ($percentage >= 60 ? 'Bon' : 'À améliorer');
            
            $csvContent .= $result->getDatepassage()->format('d/m/Y H:i:s') . ';';
            $csvContent .= $result->getUtilisateur() . ';';
            $csvContent .= $result->getScore() . ';';
            $csvContent .= $totalQuestions . ';';
            $csvContent .= $percentage . '%;';
            $csvContent .= $statut . "\n";
        }
        
        // Statistiques récapitulatives
        $csvContent .= "\nStatistiques récapitulatives\n";
        $csvContent .= "Nombre total de participants;" . count($results) . "\n";
        
        if (!empty($results)) {
            $totalScore = 0;
            foreach ($results as $result) {
                $totalScore += $result->getScore();
            }
            $avgScore = $totalScore / count($results);
            $avgPercentage = round(($avgScore / $totalQuestions) * 100, 1);
            
            $csvContent .= "Score moyen;" . round($avgScore, 1) . "\n";
            $csvContent .= "Pourcentage moyen;" . $avgPercentage . "%\n";
        }
        
        $response = new Response($csvContent);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="quiz_' . $quiz->getIdquiz() . '_results.csv"');
        
        return $response;
    }

    #[Route('/{idquiz}/export/word', name: 'admin_quiz_export_word', methods: ['GET'])]
    public function exportWord(Quiz $quiz, EntityManagerInterface $em): Response
    {
        $results = $em->getRepository(Result::class)->findBy(['quiz' => $quiz], ['datepassage' => 'DESC']);
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Résultats Quiz - ' . htmlspecialchars($quiz->getTitre()) . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; border-bottom: 2px solid #ff9800; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 30px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .excellent { background-color: #d4edda; }
        .bon { background-color: #fff3cd; }
        .ameliorer { background-color: #f8d7da; }
        .stats { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>Résultats du Quiz</h1>
    <p><strong>Titre:</strong> ' . htmlspecialchars($quiz->getTitre()) . '</p>
    <p><strong>Description:</strong> ' . htmlspecialchars($quiz->getDescription() ?: 'Non spécifiée') . '</p>
    <p><strong>Date d\'export:</strong> ' . date('d/m/Y H:i:s') . '</p>
    
    <h2>Résultats détaillés</h2>
    <table>
        <tr>
            <th>Date de passage</th>
            <th>Utilisateur</th>
            <th>Score</th>
            <th>Score Maximum</th>
            <th>Pourcentage</th>
            <th>Statut</th>
        </tr>';
        
        foreach ($results as $result) {
            $totalQuestions = $quiz->getQuestions()->count();
            $percentage = $totalQuestions > 0 ? round(($result->getScore() / $totalQuestions) * 100, 1) : 0;
            $statut = $percentage >= 80 ? 'Excellent' : ($percentage >= 60 ? 'Bon' : 'À améliorer');
            $class = $percentage >= 80 ? 'excellent' : ($percentage >= 60 ? 'bon' : 'ameliorer');
            
            $html .= '
        <tr class="' . $class . '">
            <td>' . $result->getDatepassage()->format('d/m/Y H:i:s') . '</td>
            <td>' . htmlspecialchars($result->getUtilisateur()) . '</td>
            <td>' . $result->getScore() . '</td>
            <td>' . $totalQuestions . '</td>
            <td>' . $percentage . '%</td>
            <td>' . $statut . '</td>
        </tr>';
        }
        
        $html .= '
    </table>
    
    <div class="stats">
        <h2>Statistiques récapitulatives</h2>
        <p><strong>Nombre total de participants:</strong> ' . count($results) . '</p>';
        
        if (!empty($results)) {
            $totalScore = 0;
            foreach ($results as $result) {
                $totalScore += $result->getScore();
            }
            $avgScore = $totalScore / count($results);
            $avgPercentage = round(($avgScore / $totalQuestions) * 100, 1);
            
            $html .= '
        <p><strong>Score moyen:</strong> ' . round($avgScore, 1) . '</p>
        <p><strong>Pourcentage moyen:</strong> ' . $avgPercentage . '%</p>';
        }
        
        $html .= '
    </div>
    
    <p><em>Généré depuis EducaVision - ' . date('d/m/Y H:i:s') . '</em></p>
</body>
</html>';
        
        $response = new Response($html);
        $response->headers->set('Content-Type', 'application/vnd.ms-word');
        $response->headers->set('Content-Disposition', 'attachment; filename="quiz_' . $quiz->getIdquiz() . '_results.doc"');
        
        return $response;
    }

    #[Route('/results', name: 'admin_quiz_results', methods: ['GET'])]
    public function results(EntityManagerInterface $em, Request $request): Response
    {
        $resultRepository = $em->getRepository(Result::class);
        
        // Get filter parameters
        $search = $request->query->get('search', '');
        $quizId = $request->query->get('quiz', '');
        $performance = $request->query->get('performance', '');
        
        // Build query
        $qb = $resultRepository->createQueryBuilder('r')
            ->leftJoin('r.quiz', 'q')
            ->orderBy('r.datepassage', 'DESC');
        
        // Apply search filter
        if (!empty($search)) {
            $qb->andWhere('q.titre LIKE :search OR r.utilisateur LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }
        
        // Apply quiz filter
        if (!empty($quizId)) {
            $qb->andWhere('q.idquiz = :quizId')
               ->setParameter('quizId', $quizId);
        }
        
        $results = $qb->getQuery()->getResult();
        
        // Apply performance filter after getting results (simpler approach)
        if (!empty($performance)) {
            $filteredResults = [];
            foreach ($results as $result) {
                $totalQuestions = $result->getQuiz()->getQuestions()->count();
                if ($totalQuestions > 0) {
                    $percentage = ($result->getScore() / $totalQuestions) * 100;
                    
                    switch ($performance) {
                        case 'excellent':
                            if ($percentage >= 80) {
                                $filteredResults[] = $result;
                            }
                            break;
                        case 'bon':
                            if ($percentage >= 60 && $percentage < 80) {
                                $filteredResults[] = $result;
                            }
                            break;
                        case 'ameliorer':
                            if ($percentage < 60) {
                                $filteredResults[] = $result;
                            }
                            break;
                    }
                }
            }
            $results = $filteredResults;
        }
        
        return $this->render('admin/quiz/results.html.twig', [
            'results' => $results,
            'search' => $search,
            'quizId' => $quizId,
            'performance' => $performance,
        ]);
    }

    #[Route('/{idquiz}', name: 'admin_quiz_show', methods: ['GET'])]
    public function show(Quiz $quiz): Response
    {
        return $this->render('admin/quiz/show.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    #[Route('/{idquiz}/edit', name: 'admin_quiz_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Quiz $quiz, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            
            // Message de succès personnalisé selon la visibilité
            $visibilityMessage = $quiz->isVisible() ? 
                'Le quiz est maintenant visible pour les étudiants !' : 
                'Le quiz est maintenant caché pour les étudiants.';
            
            $this->addFlash('success', 'Le quiz a été modifié avec succès ! ' . $visibilityMessage);
            return $this->redirectToRoute('admin_quiz_show', ['idquiz' => $quiz->getIdquiz()]);
        }
        
        return $this->render('admin/quiz/edit.html.twig', [
            'quiz' => $quiz,
            'form' => $form,
        ]);
    }

    #[Route('/{idquiz}/delete', name: 'admin_quiz_delete', methods: ['POST'])]
    public function delete(Request $request, Quiz $quiz, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quiz->getIdquiz(), $request->request->get('_token'))) {
            $em->remove($quiz);
            $em->flush();
            $this->addFlash('success', 'Quiz supprimé avec succès !');
        }
        return $this->redirectToRoute('admin_quiz_index');
    }
}
