# 🤖 Générateur de Quiz par IA

## 📋 Vue d'ensemble

Système complet de génération automatique de quiz à partir de texte utilisant l'Intelligence Artificielle.

## ✨ Fonctionnalités

### 1. Génération Automatique
- **À partir de texte**: Collez n'importe quel cours, document ou PDF
- **Analyse intelligente**: Extraction automatique des concepts clés
- **Types de questions**:
  - QCM (Questions à Choix Multiples)
  - Vrai/Faux
  - Questions Ouvertes (expérimental)

### 2. Personnalisation
- Nombre de questions (5-50)
- Difficulté (Facile, Moyen, Difficile)
- Durée du quiz
- Types de questions à inclure

### 3. Prévisualisation
- Voir toutes les questions générées
- Analyse du texte source
- Statistiques détaillées
- Validation avant sauvegarde

## 🚀 Utilisation

### Pour l'Étudiant/Professeur

1. **Accéder au générateur**
   - Aller sur `https://127.0.0.1:8000/quiz`
   - Cliquer sur "Créer un Quiz avec l'IA"

2. **Remplir le formulaire**
   - Titre du quiz
   - Description
   - Coller le texte source (minimum 100 caractères)
   - Choisir le nombre de questions
   - Sélectionner la difficulté
   - Définir la durée
   - Cocher les types de questions

3. **Générer**
   - Cliquer sur "Générer le Quiz avec l'IA"
   - Attendre quelques secondes

4. **Prévisualiser**
   - Voir toutes les questions générées
   - Vérifier les réponses correctes
   - Consulter l'analyse du texte

5. **Sauvegarder**
   - Cliquer sur "Sauvegarder et Publier le Quiz"
   - Le quiz apparaît dans la liste

## 🔧 Architecture Technique

### Services

#### `QuizGeneratorFromTextService`
Service principal de génération de quiz.

**Méthodes principales:**
```php
// Générer un quiz à partir de texte
generateQuizFromText(string $text, array $options): array

// Analyser le texte
analyzeText(string $text): array

// Extraire les concepts clés
extractKeyConcepts(string $text, array $analysis): array

// Générer les questions
generateQuestions(string $text, array $concepts, array $options): array

// Sauvegarder le quiz
saveGeneratedQuiz(array $quizData, ?string $createdBy): Quiz
```

**Algorithmes utilisés:**
- Extraction de mots-clés
- Analyse de complexité
- Identification de sujets
- Génération de distracteurs (mauvaises réponses)

### Contrôleur

#### `AIQuizGeneratorController`
Gère les routes de création de quiz par IA.

**Routes:**
- `GET /quiz/ai/create` - Page de création
- `POST /quiz/ai/generate` - Génération du quiz
- `POST /quiz/ai/save` - Sauvegarde du quiz

### Templates

1. **`ai_create.html.twig`**
   - Formulaire de création
   - Options de personnalisation
   - Présentation des fonctionnalités

2. **`ai_preview.html.twig`**
   - Prévisualisation du quiz
   - Analyse du texte
   - Liste des questions
   - Actions (Sauvegarder/Annuler)

## 📊 Analyse du Texte

### Métriques Calculées
- **Nombre de mots**: Total de mots dans le texte
- **Nombre de phrases**: Phrases détectées
- **Concepts extraits**: Idées principales identifiées
- **Complexité**: Simple, Moyen, Complexe
- **Sujets**: Domaines identifiés (informatique, maths, sciences...)

### Extraction de Concepts
L'IA recherche:
- Phrases avec mots-clés ("définition", "concept", "principe")
- Phrases importantes (> 20 caractères)
- Mots significatifs (> 4 caractères, hors mots vides)

## 🎯 Types de Questions

### 1. QCM (60% des questions)
**Structure:**
- 1 question
- 4 réponses (1 correcte + 3 incorrectes)
- Points selon difficulté

**Exemple:**
```
Question: Quelle affirmation est correcte concernant : la programmation orientée objet ?
Réponses:
✓ La programmation orientée objet permet d'organiser le code en classes et objets
✗ Cette affirmation est incorrecte dans ce contexte
✗ Ceci représente une interprétation erronée du concept
✗ Cette définition ne correspond pas au sujet traité
```

### 2. Vrai/Faux (30% des questions)
**Structure:**
- 1 affirmation
- 2 réponses (Vrai/Faux)
- 5 points

**Exemple:**
```
Question: La programmation orientée objet permet d'organiser le code en classes et objets
Réponses:
✓ Vrai
✗ Faux
```

### 3. Questions Ouvertes (10% des questions)
**Structure:**
- 1 question ouverte
- Réponse attendue
- Mots-clés pour correction
- 10 points

**Exemple:**
```
Question: Expliquez en quelques phrases : la programmation orientée objet
Réponse attendue: La programmation orientée objet permet d'organiser le code en classes et objets
Mots-clés: programmation, orientée, objet, classes, code
```

## 💡 Exemples d'Utilisation

### Exemple 1: Cours de Programmation
**Texte source:**
```
La programmation orientée objet (POO) est un paradigme de programmation 
informatique. Elle consiste en la définition et l'interaction de briques 
logicielles appelées objets. Un objet représente un concept, une idée ou 
toute entité du monde physique.
```

**Quiz généré:**
- 6 QCM sur les concepts de POO
- 3 Vrai/Faux sur les définitions
- 1 Question ouverte sur l'explication

### Exemple 2: Cours d'Histoire
**Texte source:**
```
La Révolution française est une période de bouleversements sociaux et 
politiques en France qui débute en 1789. Elle marque la fin de l'Ancien 
Régime et le début d'une nouvelle ère politique.
```

**Quiz généré:**
- 6 QCM sur les dates et événements
- 3 Vrai/Faux sur les faits historiques
- 1 Question ouverte sur l'impact

## 🎨 Interface Utilisateur

### Page de Création
- **En-tête**: Titre avec icône robot
- **Formulaire**: Champs clairs et organisés
- **Aide**: Tooltips et messages d'aide
- **Fonctionnalités**: 3 cartes explicatives

### Page de Prévisualisation
- **Statistiques**: Analyse du texte
- **Questions**: Liste complète avec réponses
- **Actions**: Sauvegarder ou Annuler
- **Design**: Moderne et professionnel

## 🔒 Validation

### Côté Client
- Texte minimum: 100 caractères
- Questions: 5-50
- Durée: 5-180 minutes
- Au moins un type de question coché

### Côté Serveur
- Validation des données
- Gestion des erreurs
- Messages flash informatifs

## 📈 Améliorations Futures

### Court Terme
- [ ] Support des fichiers PDF
- [ ] Support des fichiers Word
- [ ] Amélioration des distracteurs

### Moyen Terme
- [ ] Intégration d'un vrai modèle NLP (spaCy, NLTK)
- [ ] Génération de questions plus variées
- [ ] Correction automatique des questions ouvertes

### Long Terme
- [ ] Intégration d'un LLM (GPT, Claude)
- [ ] Génération d'explications pour chaque réponse
- [ ] Adaptation du quiz selon le niveau de l'étudiant

## 🐛 Dépannage

### Problème: "Le texte doit contenir au moins 100 caractères"
**Solution**: Collez un texte plus long (minimum 100 caractères)

### Problème: "Erreur lors de la génération"
**Solution**: Vérifiez que le texte est bien formaté et contient du contenu significatif

### Problème: Questions de mauvaise qualité
**Solution**: Utilisez un texte plus structuré avec des définitions claires

## 📝 Notes

- Le système est actuellement basé sur des algorithmes simples
- Pour de meilleurs résultats, utilisez des textes bien structurés
- Les questions ouvertes sont expérimentales
- La qualité dépend de la qualité du texte source

---

**Créé le**: 22/02/2026
**Version**: 1.0.0
**Statut**: ✅ Production Ready
