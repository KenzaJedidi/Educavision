# 🤖 Système de Quiz Intelligent avec IA

## Vue d'ensemble

Ce système utilise l'Intelligence Artificielle pour créer une expérience d'apprentissage adaptative et personnalisée.

## 🎯 Fonctionnalités IA

### 1. Correction Intelligente des Réponses Ouvertes

**Service**: `OpenAnswerCorrectorService`

**Capacités**:
- ✅ Analyse sémantique des réponses textuelles
- ✅ Calcul de similarité avec la réponse attendue
- ✅ Détection des mots-clés importants
- ✅ Feedback personnalisé et constructif
- ✅ Suggestions d'amélioration

**Exemple d'utilisation**:
```php
$corrector = new OpenAnswerCorrectorService();

$result = $corrector->correctOpenAnswer(
    userAnswer: "L'IA est une technologie qui permet aux machines d'apprendre",
    expectedAnswer: "L'intelligence artificielle permet aux machines d'apprendre et de s'adapter",
    keywords: ['intelligence artificielle', 'machines', 'apprendre', 'adapter']
);

// Résultat:
// [
//     'score' => 85.5,
//     'isCorrect' => true,
//     'feedback' => "✅ Bonne réponse! Votre compréhension est correcte."
// ]
```

### 2. Détection Automatique du Niveau

**Service**: `QuizAIService::detectStudentLevel()`

**Niveaux détectés**:
- 🌱 **Novice** (< 40%) - Débutant absolu
- 📚 **Beginner** (40-60%) - Bases en cours d'acquisition
- 🎓 **Intermediate** (60-80%) - Bonne maîtrise
- 🏆 **Expert** (80%+) - Maîtrise avancée

**Algorithme**:
```
1. Récupère les 10 derniers résultats de l'étudiant
2. Calcule la moyenne des scores
3. Classifie selon les seuils définis
4. Retourne le niveau avec recommandations
```

### 3. Génération Automatique de Questions

**Service**: `QuestionGeneratorService`

**Types de questions générées**:
- 📖 **Définition** - "Qu'est-ce que X?"
- 🔧 **Application** - "Comment utiliser X dans Y?"
- ⚖️ **Comparaison** - "Différence entre X et Y?"
- 🔍 **Analyse** - "Analysez l'impact de X"

**Adaptation par difficulté**:
```php
$generator = new QuestionGeneratorService();

$quiz = $generator->generateAdaptiveQuiz(
    level: 'intermediate',
    topic: 'Intelligence Artificielle',
    questionCount: 10
);
```

### 4. Adaptation de la Difficulté

**Service**: `QuizAIService::adaptDifficulty()`

**Logique d'adaptation**:

| Score | Niveau suivant | Recommandations |
|-------|---------------|-----------------|
| 90%+ | Hard | Défis complexes, concepts avancés |
| 70-89% | Medium | Approfondissement, pratique |
| 40-69% | Easy | Révision des bases |
| < 40% | Very Easy | Fondamentaux + aide supplémentaire |

**Exemple de recommandations**:
```php
$recommendations = $aiService->adaptDifficulty(45.0);

// Résultat:
// [
//     'level' => 'beginner',
//     'nextQuizDifficulty' => 'easy',
//     'suggestedTopics' => ['Révision des bases', 'Concepts fondamentaux'],
//     'motivationalMessage' => '💪 Ne lâchez rien! La pratique mène à la perfection!',
//     'additionalHelp' => [...]
// ]
```

## 🧠 Compétences IA Utilisées

### 1. Machine Learning
- **Classification** - Détection du niveau de l'étudiant
- **Prédiction** - Estimation du temps d'amélioration
- **Clustering** - Regroupement des étudiants par niveau

### 2. NLP (Natural Language Processing)
- **Analyse de similarité** - Algorithme de Levenshtein
- **Extraction de mots-clés** - Identification des concepts importants
- **Analyse sémantique** - Compréhension du sens des réponses
- **Tokenization** - Découpage et analyse des textes

### 3. Analyse de Performance
- **Scoring adaptatif** - Calcul intelligent des scores
- **Détection des points faibles** - Identification des lacunes
- **Recommandations personnalisées** - Suggestions sur mesure

## 📊 Architecture du Système

```
┌─────────────────────────────────────────┐
│         Interface Utilisateur           │
│  (Quiz, Résultats, Recommandations)     │
└──────────────┬──────────────────────────┘
               │
┌──────────────▼──────────────────────────┐
│         QuizController                   │
│  (Gestion des quiz et résultats)        │
└──────────────┬──────────────────────────┘
               │
┌──────────────▼──────────────────────────┐
│         Services IA                      │
├─────────────────────────────────────────┤
│  • QuizAIService                        │
│  • QuestionGeneratorService             │
│  • OpenAnswerCorrectorService           │
└──────────────┬──────────────────────────┘
               │
┌──────────────▼──────────────────────────┐
│         Base de Données                  │
│  (Quiz, Questions, Résultats, Users)    │
└─────────────────────────────────────────┘
```

## 🚀 Utilisation dans le Contrôleur

```php
use App\Service\AI\QuizAIService;
use App\Service\AI\OpenAnswerCorrectorService;
use App\Service\AI\QuestionGeneratorService;

class QuizController extends AbstractController
{
    public function __construct(
        private QuizAIService $aiService,
        private OpenAnswerCorrectorService $corrector,
        private QuestionGeneratorService $generator
    ) {}
    
    #[Route('/quiz/{id}/result', name: 'quiz_result')]
    public function result(Quiz $quiz, QuizResult $result): Response
    {
        // Détection du niveau
        $level = $this->aiService->detectStudentLevel($this->getUser());
        
        // Génération des recommandations
        $recommendations = $this->aiService->generateRecommendations($result);
        
        // Adaptation de la difficulté
        $nextQuizSuggestion = $this->aiService->adaptDifficulty($result->getPercentage());
        
        return $this->render('front/pages/quiz/result.html.twig', [
            'quiz' => $quiz,
            'result' => $result,
            'level' => $level,
            'recommendations' => $recommendations,
            'nextQuizSuggestion' => $nextQuizSuggestion
        ]);
    }
}
```

## 📈 Métriques et KPIs

Le système collecte et analyse:
- ✅ Taux de réussite par niveau
- ✅ Temps moyen de progression
- ✅ Points faibles récurrents
- ✅ Efficacité des recommandations
- ✅ Engagement des étudiants

## 🔮 Évolutions Futures

### Phase 2 - IA Avancée
- [ ] Intégration d'un modèle GPT pour génération de questions
- [ ] Analyse prédictive des performances
- [ ] Chatbot d'assistance personnalisé
- [ ] Détection de la triche par analyse comportementale

### Phase 3 - Machine Learning
- [ ] Modèle de recommandation collaborative
- [ ] Prédiction du taux de réussite
- [ ] Optimisation automatique des quiz
- [ ] Analyse des patterns d'apprentissage

## 🛠️ Configuration

### Variables d'environnement
```env
# AI Configuration
AI_ENABLED=true
AI_CONFIDENCE_THRESHOLD=0.7
AI_MIN_KEYWORDS_MATCH=0.5
```

### Paramètres ajustables
```yaml
# config/packages/quiz_ai.yaml
quiz_ai:
    levels:
        novice: { min: 0, max: 40 }
        beginner: { min: 40, max: 60 }
        intermediate: { min: 60, max: 80 }
        expert: { min: 80, max: 100 }
    
    difficulty_adaptation:
        enabled: true
        auto_adjust: true
```

## 📚 Ressources

- [Documentation NLP](https://www.nltk.org/)
- [Algorithmes de similarité](https://en.wikipedia.org/wiki/Levenshtein_distance)
- [Machine Learning pour l'éducation](https://www.edx.org/learn/machine-learning)

---

**Développé avec ❤️ pour une éducation intelligente et personnalisée**
