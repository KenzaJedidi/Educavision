# 🧪 GUIDE DE TEST - MODULE QUIZ

## 🚀 Comment tester le module Quiz complet

### ✅ ÉTAPE 1 : Vérifier que le serveur fonctionne

```bash
# Démarrer le serveur Symfony (si pas déjà démarré)
symfony server:start
```

Ou si vous utilisez le serveur PHP intégré :
```bash
php -S 127.0.0.1:8000 -t public
```

Le serveur devrait être accessible sur : `https://127.0.0.1:8000`

---

### ✅ ÉTAPE 2 : Tester la liste des quiz

1. Ouvrir le navigateur
2. Aller sur : `https://127.0.0.1:8000/quiz`
3. Vérifier :
   - ✅ Le design orange avec gradient
   - ✅ Le bouton "Créer un Quiz avec l'IA" en haut (violet)
   - ✅ Les cartes de quiz avec animations au survol
   - ✅ Les icônes et métadonnées (nombre de questions, date)

---

### ✅ ÉTAPE 3 : Tester le générateur IA

1. Cliquer sur **"Créer un Quiz avec l'IA"**
2. Vous arrivez sur : `https://127.0.0.1:8000/quiz/ai/create`
3. Remplir le formulaire :
   - **Titre** : "Quiz sur la Programmation"
   - **Description** : "Test de connaissances en programmation"
   - **Texte source** : Coller un texte de cours (minimum 100 caractères)
   
   Exemple de texte à coller :
   ```
   La programmation orientée objet (POO) est un paradigme de programmation informatique. 
   Elle consiste en la définition et l'interaction de briques logicielles appelées objets. 
   Un objet représente un concept, une idée ou toute entité du monde physique. 
   Les objets sont des instances de classes qui définissent leurs propriétés et comportements. 
   Une classe est un modèle qui définit la structure et le comportement des objets. 
   L'encapsulation permet de cacher les détails d'implémentation. 
   L'héritage permet de créer de nouvelles classes basées sur des classes existantes. 
   Le polymorphisme permet d'utiliser des objets de différentes classes de manière uniforme.
   ```

4. Configurer les options :
   - **Nombre de questions** : 10
   - **Difficulté** : Moyen
   - **Durée** : 30 minutes
   - **Types** : Cocher QCM et Vrai/Faux

5. Cliquer sur **"Générer le Quiz avec l'IA"**

6. Vérifier la page de prévisualisation :
   - ✅ Analyse du texte (nombre de mots, phrases, concepts)
   - ✅ Liste des questions générées
   - ✅ Réponses avec indication de la bonne réponse (icône verte)
   - ✅ Types de questions variés

7. Cliquer sur **"Sauvegarder et Publier le Quiz"**

8. Vous êtes redirigé vers `/quiz` avec un message de succès

---

### ✅ ÉTAPE 4 : Passer un quiz

1. Sur la page `/quiz`, cliquer sur **"Commencer"** d'un quiz
2. Vous arrivez sur : `https://127.0.0.1:8000/quiz/{id}/take`
3. Vérifier :
   - ✅ Barre de progression orange en haut (sticky)
   - ✅ Questions numérotées avec icône orange
   - ✅ Options de réponse avec effet hover
   - ✅ Sélection d'une réponse change la couleur (orange)
   - ✅ Le numéro de question devient vert quand répondu

4. Répondre à quelques questions
5. Cliquer sur **"Soumettre le Quiz"**

---

### ✅ ÉTAPE 5 : Voir les résultats avec IA

1. Après soumission, vous arrivez sur : `https://127.0.0.1:8000/quiz/{id}/result`
2. Vérifier les sections :

#### Section Header
- ✅ Titre du quiz
- ✅ Informations (date, durée, nombre de questions)
- ✅ Score circulaire avec couleur selon performance :
  - Vert si ≥ 80%
  - Orange si ≥ 60%
  - Rouge si < 60%

#### Section Statistiques
- ✅ 4 cartes avec icônes :
  - Points obtenus
  - Réponses correctes
  - Taux de réussite
  - Durée

#### Section Performance
- ✅ Icône selon performance (trophée, étoile, ou redo)
- ✅ Message motivationnel adapté

#### Section Graphiques
- ✅ Graphique en donut (répartition correctes/incorrectes)
- ✅ Graphique en barres (performance par question)

#### 🤖 Section IA - RECOMMANDATIONS INTELLIGENTES
C'est la section la plus importante à vérifier !

- ✅ **Header IA** avec icône robot animée
- ✅ **3 cartes colorées** :
  - Carte violette : Votre niveau (débutant/intermédiaire/avancé)
  - Carte orange : Prochain quiz suggéré (facile/moyen/difficile)
  - Carte verte : Motivation (niveau de performance)

- ✅ **Message d'analyse** dans une boîte avec icône ampoule
- ✅ **Message motivationnel** dans une boîte avec étoiles
- ✅ **Sujets à réviser** avec tags cliquables (si disponibles)
- ✅ **Aide supplémentaire** avec liste de recommandations (si disponibles)
- ✅ **Temps d'amélioration estimé** avec icône horloge (si disponible)
- ✅ **Quiz similaires recommandés** avec cartes cliquables (si disponibles)

#### Section Détail des Réponses
- ✅ Liste de toutes les questions
- ✅ Bordure verte pour correctes, rouge pour incorrectes
- ✅ Votre réponse affichée
- ✅ Bonne réponse affichée si incorrect

#### Section Actions
- ✅ 3 boutons :
  - Retour aux Quiz (violet)
  - Recommencer (orange)
  - Accueil (vert)

---

### ✅ ÉTAPE 6 : Vérifier le responsive

1. Ouvrir les DevTools (F12)
2. Activer le mode responsive (Ctrl + Shift + M)
3. Tester sur différentes tailles :
   - Mobile (375px)
   - Tablette (768px)
   - Desktop (1200px)

4. Vérifier que :
   - ✅ Les cartes s'empilent correctement
   - ✅ Les boutons restent accessibles
   - ✅ Le texte reste lisible
   - ✅ Les animations fonctionnent

---

### ✅ ÉTAPE 7 : Vider le cache si problème CSS

Si les styles ne s'appliquent pas correctement :

```bash
# Vider le cache Symfony
php bin/console cache:clear

# Vider le cache du navigateur
# Chrome/Edge : Ctrl + Shift + Delete
# Firefox : Ctrl + Shift + Delete
# Ou utiliser le mode navigation privée
```

Ou forcer le rechargement :
- **Windows** : `Ctrl + Shift + R` ou `Ctrl + F5`
- **Mac** : `Cmd + Shift + R`

---

## 🎨 CHECKLIST VISUELLE

### Design général
- [ ] Thème orange (#ff6b35, #f7931e, #fdc830) appliqué
- [ ] Gradients visibles sur les boutons et cartes
- [ ] Animations fluides (float, pulse, bounce)
- [ ] Ombres et effets de profondeur
- [ ] Transitions douces au survol

### Page Index
- [ ] Bouton IA violet en haut
- [ ] Cartes blanches avec bordure orange
- [ ] Icônes circulaires avec gradient
- [ ] Effet hover sur les cartes (élévation)
- [ ] Bouton "Commencer" orange avec effet

### Page Take
- [ ] Barre de progression orange sticky
- [ ] Numéros de questions orange/vert
- [ ] Options avec bordure et hover
- [ ] Sélection change la couleur
- [ ] Boutons stylisés

### Page Result
- [ ] Score circulaire coloré
- [ ] Cartes statistiques avec icônes
- [ ] Graphiques Chart.js fonctionnels
- [ ] **Section IA complète et stylée**
- [ ] Détail des réponses avec couleurs

### Section IA spécifiquement
- [ ] Header avec icône robot animée
- [ ] 3 cartes avec gradients (violet, orange, vert)
- [ ] Boîte de message avec icône ampoule
- [ ] Boîte motivationnelle avec étoiles
- [ ] Tags de sujets cliquables
- [ ] Cartes de quiz similaires avec hover

---

## 🐛 PROBLÈMES COURANTS

### Le CSS ne se charge pas
**Solution** : Vider le cache navigateur + Symfony
```bash
php bin/console cache:clear
# Puis Ctrl + Shift + R dans le navigateur
```

### Les recommandations IA ne s'affichent pas
**Vérifier** :
1. Le contrôleur passe bien `aiRecommendations` au template
2. Le service `QuizAIService` est injecté
3. Pas d'erreur dans les logs Symfony

### Les routes ne fonctionnent pas
**Vérifier** :
1. Les annotations `#[Route]` dans les contrôleurs
2. Le namespace des contrôleurs
3. Vider le cache de routing

### Les animations ne fonctionnent pas
**Vérifier** :
1. Le fichier `quiz.css` est bien chargé
2. Pas de conflit avec d'autres CSS
3. Les classes CSS sont bien appliquées dans le HTML

---

## ✅ RÉSULTAT ATTENDU

Après tous ces tests, vous devriez avoir :

1. ✅ Un module Quiz fonctionnel avec design orange moderne
2. ✅ Un générateur de quiz par IA opérationnel
3. ✅ Des recommandations IA personnalisées sur la page résultats
4. ✅ Des animations fluides et professionnelles
5. ✅ Un design responsive sur tous les appareils
6. ✅ Une expérience utilisateur optimale

---

## 📸 CAPTURES D'ÉCRAN ATTENDUES

### Page Index
- Bannière orange avec gradient
- Bouton IA violet centré
- Grille de cartes blanches avec bordure orange

### Page Générateur IA
- Header violet avec icône robot
- Formulaire avec champs stylisés
- 3 cartes de fonctionnalités en bas

### Page Prévisualisation
- Header vert avec statistiques
- Analyse du texte en grille
- Questions avec réponses colorées

### Page Résultats
- Score circulaire coloré en haut
- 4 cartes statistiques
- **Section IA avec 8 composants**
- Graphiques Chart.js
- Liste détaillée des réponses

---

## 🎉 FÉLICITATIONS !

Si tous les tests passent, le module Quiz est **100% fonctionnel** ! 🚀

Vous avez maintenant :
- Un système de quiz complet
- Une génération automatique par IA
- Des recommandations personnalisées
- Un design moderne et attractif

**Le module est prêt pour la production !** ✨

---

*Guide de test créé le 22 février 2026*
*Version 1.0 - Module Quiz avec IA*
