<?php

namespace App\Service\AI;

use App\Entity\Quiz;
use App\Entity\Question;
use App\Entity\Answer;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service de génération automatique de quiz à partir de texte
 * Utilise DeepSeek API pour créer des questions QCM, Vrai/Faux et ouvertes
 * Fallback sur génération heuristique si DeepSeek non configuré
 */
class QuizGeneratorFromTextService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ?DeepSeekApiService $deepSeekApi = null
    ) {
    }
    
    /**
     * Génère un quiz complet à partir d'un texte
     */
    public function generateQuizFromText(string $text, array $options = []): array
    {
        // Options par défaut
        $options = array_merge([
            'title' => 'Quiz généré automatiquement',
            'description' => 'Quiz créé par IA',
            'questionCount' => 10,
            'difficulty' => 'medium',
            'includeQCM' => true,
            'includeTrueFalse' => true,
            'includeOpen' => false,
            'duration' => 30
        ], $options);
        
        // 1. Analyser le texte (pour l'affichage)
        $analysis = $this->analyzeText($text);
        $concepts = $this->extractKeyConcepts($text, $analysis);
        $targetCount = (int) $options['questionCount'];

        // 2. Générer les questions via DeepSeek API si configuré
        $questions = [];
        $generatedBy = 'fallback';
        if ($this->deepSeekApi?->isConfigured()) {
            $questions = $this->deepSeekApi->generateQuizQuestions(
                $text,
                $options['questionCount'],
                $options['difficulty'],
                $options['includeQCM'],
                $options['includeTrueFalse'],
                $options['includeOpen']
            );
            if (count($questions) > $options['questionCount']) {
                $questions = array_slice($questions, 0, $options['questionCount']);
            }
            $deepSeekCount = count($questions);
            $generatedBy = $deepSeekCount >= $targetCount ? 'deepseek' : ($deepSeekCount > 0 ? 'mixed' : 'fallback');
        }

        // 3. Fallback ou complément : si DeepSeek n'a pas donné assez de questions, compléter avec l'heuristique
        if (count($questions) < $targetCount) {
            $generatedBy = count($questions) > 0 ? 'mixed' : 'fallback';
            $fallbackQuestions = $this->generateQuestions($text, $concepts, $options);
            $existingTexts = array_map(fn ($q) => $q['text'], $questions);
            foreach ($fallbackQuestions as $fq) {
                if (count($questions) >= $targetCount) break;
                if (!in_array($fq['text'], $existingTexts)) {
                    $questions[] = $fq;
                    $existingTexts[] = $fq['text'];
                }
            }
            $questions = array_slice($questions, 0, $targetCount);
        }

        return [
            'title' => $options['title'],
            'description' => $options['description'],
            'duration' => $options['duration'],
            'difficulty' => $options['difficulty'],
            'questions' => $questions,
            'concepts' => $concepts,
            'analysis' => $analysis,
            'generatedBy' => $generatedBy,
        ];
    }
    
    /**
     * Analyse le texte pour en extraire les informations
     */
    private function analyzeText(string $text): array
    {
        $words = str_word_count($text);
        $sentences = preg_split('/[.!?]+/', $text);
        $sentences = array_filter($sentences, fn($s) => strlen(trim($s)) > 0);
        
        // Extraire les mots significatifs
        $significantWords = $this->extractSignificantWords($text);
        
        return [
            'wordCount' => $words,
            'sentenceCount' => count($sentences),
            'significantWords' => $significantWords,
            'complexity' => $this->calculateComplexity($words, count($sentences)),
            'topics' => $this->identifyTopics($text)
        ];
    }
    
    /**
     * Extrait les concepts / passages du texte pour générer des questions.
     * Utilise les phrases ET les paragraphes pour couvrir tout le texte.
     */
    private function extractKeyConcepts(string $text, array $analysis): array
    {
        $concepts = [];
        $seen = [];

        // 1. Phrases avec mots-clés pédagogiques (priorité haute)
        $sentences = preg_split('/[.!?\n]+/', $text);
        $keywords = ['définition', 'concept', 'principe', 'méthode', 'technique', 'important', 'essentiel', 'installer', 'configurer', 'utiliser', 'commande', 'fonction', 'classe'];
        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if (strlen($sentence) < 15) continue;
            foreach ($keywords as $keyword) {
                if (stripos($sentence, $keyword) !== false) {
                    $key = md5(mb_substr($sentence, 0, 80));
                    if (!isset($seen[$key])) {
                        $seen[$key] = true;
                        $concepts[] = ['text' => $sentence, 'type' => 'definition', 'importance' => 'high'];
                    }
                    break;
                }
            }
        }

        // 2. Toutes les phrases significatives (longueur >= 25 caractères)
        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if (strlen($sentence) < 25) continue;
            $key = md5(mb_substr($sentence, 0, 80));
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $concepts[] = ['text' => $sentence, 'type' => 'content', 'importance' => 'medium'];
            }
        }

        // 3. Paragraphes (chunks par \n\n) si on n'a pas assez
        if (count($concepts) < 10) {
            $paragraphs = preg_split('/\n\s*\n/', $text);
            foreach ($paragraphs as $para) {
                $para = trim($para);
                if (strlen($para) < 40) continue;
                $key = md5(mb_substr($para, 0, 100));
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $concepts[] = ['text' => mb_substr($para, 0, 400), 'type' => 'paragraph', 'importance' => 'medium'];
                }
            }
        }

        // 4. Chunks de 200 caractères si encore pas assez
        if (count($concepts) < 20) {
            $chunkSize = 200;
            $len = mb_strlen($text);
            for ($i = 0; $i < $len; $i += (int)($chunkSize * 0.7)) {
                $chunk = mb_substr($text, $i, $chunkSize);
                $chunk = trim($chunk);
                if (mb_strlen($chunk) < 50) continue;
                $key = md5($chunk);
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $concepts[] = ['text' => $chunk, 'type' => 'chunk', 'importance' => 'low'];
                }
            }
        }

        return $concepts;
    }
    
    /**
     * Génère les questions selon les options.
     * Respecte TOUJOURS le nombre de questions demandé par le professeur.
     */
    private function generateQuestions(string $text, array $concepts, array $options): array
    {
        $questions = [];
        $questionCount = (int) $options['questionCount'];
        if ($questionCount < 1) $questionCount = 10;

        // S'assurer d'avoir assez de "sources" (concepts) - répéter si nécessaire
        $sources = $concepts;
        if (empty($sources)) {
            $sources = [['text' => mb_substr($text, 0, 300), 'type' => 'content', 'importance' => 'medium']];
        }
        while (count($sources) < $questionCount) {
            $sources = array_merge($sources, $sources);
        }

        // Répartition des types de questions
        $qcmCount = $options['includeQCM'] ? max(1, (int)($questionCount * 0.6)) : 0;
        $trueFalseCount = $options['includeTrueFalse'] ? max(0, (int)($questionCount * 0.3)) : 0;
        $openCount = $options['includeOpen'] ? ($questionCount - $qcmCount - $trueFalseCount) : 0;
        $openCount = max(0, $openCount);
        if ($qcmCount + $trueFalseCount + $openCount < $questionCount) {
            $qcmCount += ($questionCount - $qcmCount - $trueFalseCount - $openCount);
        }

        $idx = 0;
        for ($i = 0; $i < $qcmCount && count($questions) < $questionCount; $i++) {
            $q = $this->generateQCMQuestion($sources, $idx, $options['difficulty']);
            if ($q) {
                $questions[] = $q;
            }
            $idx++;
        }
        for ($i = 0; $i < $trueFalseCount && count($questions) < $questionCount; $i++) {
            $q = $this->generateTrueFalseQuestion($sources[$idx % count($sources)], $idx % 2 === 0);
            if ($q) {
                $questions[] = $q;
            }
            $idx++;
        }
        for ($i = 0; $i < $openCount && count($questions) < $questionCount; $i++) {
            $questions[] = $this->generateOpenQuestion($sources[$idx++ % count($sources)]);
        }

        while (count($questions) < $questionCount) {
            $q = $this->generateQCMQuestion($sources, $idx, $options['difficulty']);
            if ($q) {
                $questions[] = $q;
            }
            $idx++;
        }

        return array_slice($questions, 0, $questionCount);
    }
    
    /**
     * Génère une question QCM qui exige d'avoir lu le cours : la réponse ne doit JAMAIS être dans la question.
     * Format "X : Y" : on montre Y (contenu), la bonne réponse est X (section). Impossible à deviner sans le cours.
     */
    private function generateQCMQuestion(array $sources, int $idx, string $difficulty): ?array
    {
        if (empty($sources)) {
            return null;
        }

        $correctConcept = $sources[$idx % count($sources)];
        $correctText = trim($correctConcept['text'] ?? '');
        if (mb_strlen($correctText) < 25) {
            return null;
        }

        // Format "Section : contenu" — on montre le CONTENU, la réponse est la SECTION (pas dans la question)
        if (preg_match('/^([^:]{3,50})\s*:\s*(.+)$/us', $correctText, $m)) {
            $sectionName = trim($m[1]);
            $content = trim($m[2]);
            if (mb_strlen($content) < 15 || mb_strlen($sectionName) < 3) {
                return null;
            }
            // S'assurer que le contenu ne contient pas la réponse
            if (stripos($content, $sectionName) !== false) {
                return null;
            }
        } else {
            // Pas de format "X : Y" — impossible de séparer question/réponse proprement, on saute
            return null;
        }

        $contentExcerpt = $this->truncateAtSentenceBoundary($content, 100);

        // Distracteurs : noms de sections d'autres concepts
        $wrongOptions = [];
        $used = [$idx % count($sources)];
        for ($i = 1; $i <= count($sources); $i++) {
            $otherIdx = ($idx + $i) % count($sources);
            if (in_array($otherIdx, $used)) {
                continue;
            }
            $otherText = trim($sources[$otherIdx]['text'] ?? '');
            $otherSection = preg_match('/^([^:]{3,50})\s*:/u', $otherText, $om) ? trim($om[1]) : null;
            if ($otherSection && $otherSection !== $sectionName && !in_array($otherSection, $wrongOptions)) {
                $wrongOptions[] = $otherSection;
                $used[] = $otherIdx;
            }
            if (count($wrongOptions) >= 3) {
                break;
            }
        }

        while (count($wrongOptions) < 3) {
            $wrongOptions[] = 'Autre section du cours';
        }

        $answers = [
            ['text' => $sectionName, 'isCorrect' => true],
            ['text' => $wrongOptions[0], 'isCorrect' => false],
            ['text' => $wrongOptions[1], 'isCorrect' => false],
            ['text' => $wrongOptions[2], 'isCorrect' => false],
        ];
        shuffle($answers);

        return [
            'type' => 'qcm',
            'text' => "D'après le cours, dans quelle section trouve-t-on l'information suivante ? « " . $contentExcerpt . " »",
            'points' => $this->getPointsForDifficulty($difficulty),
            'answers' => $answers,
        ];
    }

    /**
     * Tronque à une frontière de phrase ou de mot, jamais au milieu d'un mot ou d'une préposition.
     */
    private function truncateAtSentenceBoundary(string $text, int $maxLen): string
    {
        if (mb_strlen($text) <= $maxLen) {
            return $text;
        }
        $cut = mb_substr($text, 0, $maxLen);
        $badEndings = [' en', ' de', ' du', ' des', ' et', ' ou', ' le', ' la', ' les', ' un', ' une'];
        foreach ($badEndings as $bad) {
            if (mb_substr($cut, -mb_strlen($bad)) === $bad) {
                $cut = mb_substr($cut, 0, -mb_strlen($bad));
            }
        }
        $lastSpace = mb_strrpos($cut, ' ');
        if ($lastSpace !== false && $lastSpace > $maxLen / 2) {
            $cut = mb_substr($cut, 0, $lastSpace);
        }
        return $cut . (mb_strlen($text) > $maxLen ? '…' : '');
    }
    
    /**
     * Génère une question Vrai/Faux à partir d'un passage du document.
     * @param bool $affirmative si true, l'affirmation est vraie (réponse = Vrai) ; si false, formulation négative (réponse = Faux)
     */
    private function generateTrueFalseQuestion(array $concept, bool $affirmative = true): ?array
    {
        $text = trim($concept['text'] ?? '');
        if (mb_strlen($text) < 25) {
            return null;
        }

        $sentence = mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);

        if ($affirmative) {
            $question = "D'après le cours : " . $sentence;
            $correctIsTrue = true;
        } else {
            $question = "Le cours ne dit pas que : " . mb_strtolower($sentence);
            $correctIsTrue = false;
        }

        return [
            'type' => 'true_false',
            'text' => $question,
            'points' => 5,
            'answers' => [
                ['text' => 'Vrai', 'isCorrect' => $correctIsTrue],
                ['text' => 'Faux', 'isCorrect' => !$correctIsTrue]
            ]
        ];
    }
    
    /**
     * Génère une question ouverte à partir d'un passage du document.
     */
    private function generateOpenQuestion(array $concept): array
    {
        $text = trim($concept['text'] ?? '');
        $words = preg_split('/\s+/u', $text, 8);
        $subject = implode(' ', array_slice($words, 0, min(6, count($words))));

        return [
            'type' => 'open',
            'text' => "D'après le cours, que pouvez-vous dire sur : " . $subject . " ?",
            'points' => 10,
            'expectedAnswer' => $text,
            'keywords' => $this->extractSignificantWords($text),
            'answers' => []
        ];
    }
    
    /**
     * Calcule la complexité du texte
     */
    private function calculateComplexity(int $wordCount, int $sentenceCount): string
    {
        if ($sentenceCount === 0) return 'simple';
        
        $avgWordsPerSentence = $wordCount / $sentenceCount;
        
        if ($avgWordsPerSentence > 20) return 'complex';
        if ($avgWordsPerSentence > 15) return 'medium';
        return 'simple';
    }
    
    /**
     * Identifie les sujets principaux
     */
    private function identifyTopics(string $text): array
    {
        // Mots-clés par domaine
        $domains = [
            'informatique' => ['programmation', 'code', 'algorithme', 'données', 'système'],
            'mathématiques' => ['équation', 'calcul', 'nombre', 'fonction', 'théorème'],
            'sciences' => ['expérience', 'hypothèse', 'observation', 'théorie', 'loi'],
            'histoire' => ['siècle', 'guerre', 'roi', 'révolution', 'empire'],
            'géographie' => ['pays', 'continent', 'climat', 'population', 'ville']
        ];
        
        $topics = [];
        $textLower = strtolower($text);
        
        foreach ($domains as $domain => $keywords) {
            $count = 0;
            foreach ($keywords as $keyword) {
                if (strpos($textLower, $keyword) !== false) {
                    $count++;
                }
            }
            if ($count > 0) {
                $topics[] = $domain;
            }
        }
        
        return empty($topics) ? ['général'] : $topics;
    }
    
    /**
     * Extrait les mots significatifs
     */
    private function extractSignificantWords(string $text): array
    {
        $stopWords = ['le', 'la', 'les', 'un', 'une', 'des', 'de', 'du', 'et', 'ou', 'mais', 'est', 'sont'];
        
        $words = str_word_count(strtolower($text), 1, 'àâäéèêëïîôùûüÿæœç');
        $significantWords = [];
        
        foreach ($words as $word) {
            if (strlen($word) > 4 && !in_array($word, $stopWords)) {
                $significantWords[] = $word;
            }
        }
        
        return array_unique(array_slice($significantWords, 0, 10));
    }
    
    /**
     * Retourne les points selon la difficulté
     */
    private function getPointsForDifficulty(string $difficulty): int
    {
        return match($difficulty) {
            'easy' => 5,
            'medium' => 10,
            'hard' => 15,
            default => 10
        };
    }
    
    /**
     * Sauvegarde le quiz généré en base de données
     */
    public function saveGeneratedQuiz(array $quizData, ?string $createdBy = null): Quiz
    {
        $quiz = new Quiz();
        $quiz->setTitre($quizData['title']);
        $quiz->setDescription($quizData['description']);
        $quiz->setDuree($quizData['duration']);
        $quiz->setVisible(true);
        $quiz->setDatecreation(new \DateTime());
        
        $this->entityManager->persist($quiz);
        
        // Créer les questions (exclure les questions sans réponses pour éviter "Quiz has questions without answers")
        $savedCount = 0;
        foreach ($quizData['questions'] as $index => $questionData) {
            $answers = $questionData['answers'] ?? [];
            if (empty($answers)) {
                continue; // Ne pas sauvegarder les questions ouvertes (sans choix de réponses)
            }

            $question = new Question();
            $question->setTexte($questionData['text']);
            $question->setPoints($questionData['points']);
            $question->setQuiz($quiz);
            
            $this->entityManager->persist($question);

            foreach ($answers as $answerData) {
                $answer = new Answer();
                $answer->setTexte($answerData['text']);
                $answer->setCorrect($answerData['isCorrect']);
                $answer->setQuestion($question);
                
                $this->entityManager->persist($answer);
            }
            $savedCount++;
        }

        if ($savedCount === 0) {
            throw new \InvalidArgumentException('Aucune question avec des réponses à sauvegarder. Désactivez les questions ouvertes ou régénérez le quiz.');
        }
        
        $this->entityManager->flush();
        
        return $quiz;
    }
}
