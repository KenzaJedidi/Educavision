# 📘 Référence CSS - Module Quiz

## Table des matières
1. [Variables CSS](#variables-css)
2. [Classes de layout](#classes-de-layout)
3. [Classes de composants](#classes-de-composants)
4. [Classes utilitaires](#classes-utilitaires)
5. [Animations](#animations)
6. [Responsive](#responsive)

---

## Variables CSS

### Couleurs principales
```css
--quiz-primary: #ff6b35;      /* Orange principal */
--quiz-secondary: #f7931e;    /* Orange secondaire */
--quiz-accent: #fdc830;       /* Jaune accent */
```

### Couleurs de statut
```css
--quiz-success: #48bb78;      /* Vert succès */
--quiz-danger: #f56565;       /* Rouge erreur */
--quiz-warning: #fd7e14;      /* Orange avertissement */
--quiz-info: #4299e1;         /* Bleu information */
```

### Couleurs neutres
```css
--quiz-dark: #2d3748;         /* Texte sombre */
--quiz-gray: #718096;         /* Gris moyen */
--quiz-light: #f7fafc;        /* Fond clair */
--quiz-border: #e2e8f0;       /* Bordure */
```

### Ombres
```css
--quiz-shadow-sm: 0 5px 15px rgba(0, 0, 0, 0.08);
--quiz-shadow-md: 0 10px 30px rgba(0, 0, 0, 0.1);
--quiz-shadow-lg: 0 20px 40px rgba(0, 0, 0, 0.15);
```

### Transitions
```css
--quiz-transition-fast: all 0.2s ease;
--quiz-transition-normal: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
--quiz-transition-slow: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
```

---

## Classes de layout

### Bannière
```html
<div class="quiz-banner">
    <div class="container">
        <div class="quiz-banner-content">
            <h1>Titre</h1>
            <p>Description</p>
        </div>
    </div>
</div>
```

**Propriétés :**
- Dégradé orange/jaune
- Padding : 80px vertical
- Animation de vague en arrière-plan

### Grille de quiz
```html
<div class="quiz-grid">
    <!-- Cards ici -->
</div>
```

**Propriétés :**
- Display : grid
- Colonnes : auto-fill, min 320px
- Gap : 30px
- Responsive automatique

### Conteneur de quiz
```html
<div class="quiz-take-container">
    <!-- Contenu ici -->
</div>
```

**Propriétés :**
- Max-width : 900px
- Centré automatiquement
- Padding : 40px vertical

---

## Classes de composants

### Card de quiz

#### Structure complète
```html
<div class="quiz-card">
    <div class="quiz-card-header">
        <div class="quiz-card-icon">
            <i class="fas fa-brain"></i>
        </div>
        <h3 class="quiz-card-title">
            <a href="#">Titre du quiz</a>
        </h3>
    </div>
    <div class="quiz-card-body">
        <p class="quiz-card-description">Description...</p>
    </div>
    <div class="quiz-card-meta">
        <div class="quiz-meta-item">
            <i class="fas fa-question-circle"></i>
            <span>10 questions</span>
        </div>
    </div>
    <div class="quiz-card-footer">
        <a href="#" class="quiz-start-btn">
            <i class="fas fa-play"></i>
            <span>Commencer</span>
        </a>
    </div>
</div>
```

#### Propriétés
- **quiz-card** : Card principale avec ombre et bordure arrondie
- **quiz-card-header** : En-tête avec fond dégradé léger
- **quiz-card-icon** : Icône circulaire avec dégradé
- **quiz-card-title** : Titre en gras
- **quiz-card-body** : Corps avec padding
- **quiz-card-meta** : Métadonnées en bas
- **quiz-card-footer** : Pied avec bouton centré

### Question de quiz

#### Structure complète
```html
<div class="quiz-question-card">
    <div class="quiz-question-header">
        <div class="quiz-question-number">1</div>
        <div class="quiz-question-text">Question ?</div>
    </div>
    <ul class="quiz-options-list">
        <li class="quiz-option-item">
            <label class="quiz-option-label">
                <input type="radio" class="quiz-option-input" name="q1" value="1">
                <span class="quiz-option-text">Réponse A</span>
            </label>
        </li>
    </ul>
</div>
```

#### Propriétés
- **quiz-question-card** : Card de question avec ombre
- **quiz-question-number** : Badge circulaire numéroté
- **quiz-question-text** : Texte de la question
- **quiz-options-list** : Liste sans puces
- **quiz-option-label** : Label interactif avec hover
- **quiz-option-input** : Input radio stylisé
- **quiz-option-text** : Texte de l'option

### Barre de progression

```html
<div class="quiz-progress-container">
    <div class="quiz-progress-label">
        <span>Progression</span>
        <span id="progress-text">0 / 10</span>
    </div>
    <div class="quiz-progress-bar-container">
        <div class="quiz-progress-bar" style="width: 50%"></div>
    </div>
</div>
```

#### Propriétés
- **quiz-progress-container** : Conteneur avec fond blanc
- **quiz-progress-label** : Labels en flexbox
- **quiz-progress-bar-container** : Barre de fond
- **quiz-progress-bar** : Barre de progression avec dégradé

### Boutons

```html
<!-- Bouton principal -->
<button class="quiz-btn quiz-btn-primary">
    <i class="fas fa-check"></i>
    <span>Valider</span>
</button>

<!-- Bouton secondaire -->
<button class="quiz-btn quiz-btn-secondary">
    <i class="fas fa-arrow-left"></i>
    <span>Retour</span>
</button>
```

#### Variantes
- **quiz-btn-primary** : Dégradé orange, ombre
- **quiz-btn-secondary** : Gris, sans dégradé
- **quiz-btn-success** : Vert
- **quiz-btn-warning** : Jaune/orange

### Boîte d'information

```html
<div class="quiz-info-box">
    <i class="fas fa-info-circle"></i>
    Message d'information
</div>
```

**Propriétés :**
- Fond blanc
- Bordure gauche colorée
- Icône intégrée

### État vide

```html
<div class="quiz-empty-state">
    <div class="quiz-empty-icon">
        <i class="fas fa-question-circle"></i>
    </div>
    <h2 class="quiz-empty-title">Titre</h2>
    <p class="quiz-empty-message">Message</p>
</div>
```

**Propriétés :**
- Centré
- Icône circulaire grande
- Texte centré

---

## Classes utilitaires

### Couleurs de texte
```css
.quiz-text-primary    /* Orange */
.quiz-text-secondary  /* Orange foncé */
.quiz-text-success    /* Vert */
.quiz-text-danger     /* Rouge */
.quiz-text-warning    /* Orange clair */
.quiz-text-info       /* Bleu */
```

### Couleurs de fond
```css
.quiz-bg-primary      /* Fond orange */
.quiz-bg-secondary    /* Fond orange foncé */
.quiz-bg-success      /* Fond vert */
.quiz-bg-danger       /* Fond rouge */
.quiz-bg-warning      /* Fond orange clair */
.quiz-bg-info         /* Fond bleu */
```

### Bordures
```css
.quiz-border-primary    /* Bordure orange */
.quiz-border-secondary  /* Bordure orange foncé */
.quiz-border-accent     /* Bordure jaune */
```

### Dégradés
```css
.quiz-gradient-primary  /* Dégradé orange */
.quiz-gradient-accent   /* Dégradé jaune */
.quiz-gradient-success  /* Dégradé vert */
```

### Effets spéciaux
```css
.quiz-glass            /* Effet glassmorphism */
.quiz-neomorph         /* Effet néomorphique */
.quiz-neomorph-inset   /* Effet néomorphique inversé */
```

---

## Animations

### Animations de base

#### fadeIn
```css
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
```
**Usage :** Apparition en fondu

#### fadeInUp
```css
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
```
**Usage :** Apparition depuis le bas

#### fadeInDown
```css
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
```
**Usage :** Apparition depuis le haut

### Animations avancées

#### gradientMove
```css
@keyframes gradientMove {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}
```
**Usage :** Animation de dégradé

#### pulse
```css
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
```
**Usage :** Pulsation

#### bounce
```css
@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-20px); }
    60% { transform: translateY(-10px); }
}
```
**Usage :** Rebond

#### float
```css
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}
```
**Usage :** Flottement

#### shake
```css
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}
```
**Usage :** Secousse (erreur)

### Application des animations

```html
<!-- Animation au chargement -->
<div class="quiz-card" style="animation: fadeInUp 0.6s ease-out;">
    ...
</div>

<!-- Animation au survol -->
<button class="quiz-btn" style="animation: pulse 2s infinite;">
    ...
</button>

<!-- Animation conditionnelle -->
<div class="quiz-option-label success" style="animation: successPulse 0.6s;">
    ...
</div>
```

---

## Responsive

### Breakpoints

```css
/* Mobile (par défaut) */
/* < 576px */

/* Small devices (landscape phones) */
@media (min-width: 576px) { }

/* Medium devices (tablets) */
@media (min-width: 768px) { }

/* Large devices (desktops) */
@media (min-width: 992px) { }

/* Extra large devices */
@media (min-width: 1200px) { }
```

### Classes responsive

#### Grille
```css
/* Mobile : 1 colonne */
.quiz-grid {
    grid-template-columns: 1fr;
}

/* Tablet : 2 colonnes */
@media (min-width: 768px) {
    .quiz-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Desktop : 3 colonnes */
@media (min-width: 992px) {
    .quiz-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}
```

#### Typographie
```css
/* Mobile */
.quiz-banner h1 {
    font-size: 28px;
}

/* Tablet */
@media (min-width: 768px) {
    .quiz-banner h1 {
        font-size: 36px;
    }
}

/* Desktop */
@media (min-width: 992px) {
    .quiz-banner h1 {
        font-size: 48px;
    }
}
```

---

## Exemples d'utilisation

### Card de quiz personnalisée

```html
<div class="quiz-card" style="animation: fadeInUp 0.6s ease-out;">
    <div class="quiz-card-header">
        <div class="quiz-card-icon quiz-bg-success">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <h3 class="quiz-card-title quiz-text-success">
            <a href="#">Quiz Avancé</a>
        </h3>
    </div>
    <div class="quiz-card-body">
        <p class="quiz-card-description">
            Un quiz pour tester vos connaissances avancées.
        </p>
    </div>
    <div class="quiz-card-footer">
        <a href="#" class="quiz-btn quiz-btn-success">
            <i class="fas fa-rocket"></i>
            <span>Commencer</span>
        </a>
    </div>
</div>
```

### Question avec feedback

```html
<div class="quiz-question-card">
    <div class="quiz-question-header">
        <div class="quiz-question-number">1</div>
        <div class="quiz-question-text">Quelle est la capitale de la France ?</div>
    </div>
    <ul class="quiz-options-list">
        <li class="quiz-option-item">
            <label class="quiz-option-label success">
                <input type="radio" class="quiz-option-input" checked>
                <span class="quiz-option-text">Paris ✓</span>
            </label>
        </li>
        <li class="quiz-option-item">
            <label class="quiz-option-label">
                <input type="radio" class="quiz-option-input">
                <span class="quiz-option-text">Londres</span>
            </label>
        </li>
    </ul>
</div>
```

### Barre de progression animée

```html
<div class="quiz-progress-container">
    <div class="quiz-progress-label">
        <span>Progression</span>
        <span id="progress-text">7 / 10</span>
    </div>
    <div class="quiz-progress-bar-container">
        <div class="quiz-progress-bar" 
             style="width: 70%; animation: progressFill 1s ease-out;">
        </div>
    </div>
</div>
```

---

## Bonnes pratiques

### 1. Utiliser les variables CSS
```css
/* ✅ Bon */
.my-element {
    color: var(--quiz-primary);
}

/* ❌ Mauvais */
.my-element {
    color: #ff6b35;
}
```

### 2. Préfixer les classes personnalisées
```css
/* ✅ Bon */
.quiz-my-custom-class { }

/* ❌ Mauvais */
.my-custom-class { }
```

### 3. Utiliser les transitions
```css
/* ✅ Bon */
.quiz-btn {
    transition: var(--quiz-transition-normal);
}

/* ❌ Mauvais */
.quiz-btn {
    transition: all 0.3s;
}
```

### 4. Respecter la hiérarchie
```html
<!-- ✅ Bon -->
<div class="quiz-card">
    <div class="quiz-card-header">
        <h3 class="quiz-card-title">...</h3>
    </div>
</div>

<!-- ❌ Mauvais -->
<div class="quiz-card">
    <h3 class="quiz-card-title">...</h3>
</div>
```

---

## Accessibilité

### Focus visible
```css
.quiz-option-input:focus-visible + .quiz-option-text {
    outline: 3px solid var(--quiz-primary);
    outline-offset: 2px;
}
```

### Contraste élevé
```css
@media (prefers-contrast: high) {
    .quiz-option-label {
        border-width: 2px;
        border-color: #000;
    }
}
```

### Réduction des animations
```css
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
```

---

**Référence complète pour le module Quiz** 📚
