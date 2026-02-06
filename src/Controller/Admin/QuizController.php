<?php
namespace App\Controller\Admin;

use App\Entity\Quiz;
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
            $quiz = new Quiz();
            $quiz->setTitre($request->request->get('titre'));
            $quiz->setDescription($request->request->get('description'));

            $questions = $request->request->all('questions');
            if ($questions) {
                foreach ($questions as $qData) {
                    $question = new \App\Entity\Question();
                    $question->setTexte($qData['texte'] ?? '');
                    $question->setQuiz($quiz);
                    if (isset($qData['answers'])) {
                        foreach ($qData['answers'] as $aData) {
                            $answer = new \App\Entity\Answer();
                            $answer->setTexte($aData['texte'] ?? '');
                            $answer->setCorrect(isset($aData['correct']));
                            $answer->setQuestion($question);
                            $em->persist($answer);
                            $question->addAnswer($answer);
                        }
                    }
                    $em->persist($question);
                    $quiz->addQuestion($question);
                }
            }
            $em->persist($quiz);
            $em->flush();
            $this->addFlash('success', 'Quiz créé avec succès !');
            return $this->redirectToRoute('admin_quiz_index');
        }
        return $this->render('admin/quiz/new.html.twig');
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
            $this->addFlash('success', 'Le quiz a été modifié avec succès !');
            return $this->redirectToRoute('admin_quiz_index');
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
            $this->addFlash('success', 'Le quiz a été supprimé avec succès !');
        }
        return $this->redirectToRoute('admin_quiz_index');
    }
}
