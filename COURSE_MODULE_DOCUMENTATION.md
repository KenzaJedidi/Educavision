# 📚 Module Cours - Documentation Complète

## 🎯 Aperçu

Module Symfony complet pour la gestion des cours avec intégration d'**APIs externes** :
- **OpenAI API** : Génération automatique de résumés et mots-clés
- **Wikipedia API** : Récupération de ressources complémentaires
- **Pagination dynamique** et **recherche avancée multicritère**

---

## 🏗️ Architecture & Structure

### 1. **Services (Couche Métier)**

#### `OpenAICourseService.php`
```
→ Générer résumé intelligent du cours
→ Générer mots-clés automatiques
→ Gestion des appels OpenAI avec fallback
→ Nettoyage et validation des réponses
```

#### `WikipediaResourceService.php`
```
→ Rechercher articles Wikipedia par titre/mots-clés
→ Récupérer extraits et résumés d'articles
→ Déduplication et filtrage des ressources
→ Scoring de pertinence
```

#### `CourseService.php` (Orchestration)
```
→ Créer cours avec génération IA automatique
→ Générer résumé/keywords sur demande
→ Récupérer ressources complémentaires
→ Recherche avancée avec pagination
→ Gestion des vues, likes et accès
```

#### `CourseRepository.php` (Data Access)
```
→ findActiveCourses() - Cours actifs
→ searchCoursesAdvanced() - Recherche paginée
→ findMostPopular() - Cours populaires
→ findLatest() - Cours récents
→ Critères de tri multiples (date, popularité, vues, prix)
```

### 2. **Contrôleurs**

#### `CourseController.php`
```
Routes publiques (HTML):
  GET  /cours/                    → Liste des cours
  GET  /cours/{id}                → Detail avec ressources
  GET  /cours/{id}/chapitre/{chId} → Détail chapitre
  POST /cours/{id}/message         → Envoyer message prof
  
API REST (JSON):
  GET  /cours/api/search                    → Recherche avancée paginée
  GET  /cours/api/{id}                      → Détail cours + ressources
  POST /cours/api/{id}/generate-summary     → Générer résumé IA
  POST /cours/api/{id}/generate-keywords    → Générer mots-clés IA
  GET  /cours/api/{id}/resources            → Ressources Wikipedia
  POST /cours/api/{id}/like                 → Incrémenter likes
  GET  /cours/api/popular                   → Cours populaires
  GET  /cours/api/latest                    → Cours récents
```

### 3. **Entité Course.php**
```php
Champs existants:
  - id, titre, description, category
  - image_url, pdf_file, price, status
  - created_at, teacher

Nouveaux champs IA:
  - wikipedia_summary (TEXT) : Résumé IA Generé
  - keywords (VARCHAR 500) : Mots-clés séparés par virgule
  - views (INT) : Nombre de vues
  - likes (INT) : Nombre de likes
  - popularity_score (DECIMAL) : Score de popularité
  - last_accessed (DATETIME) : Dernier accès
```

### 4. **DTOs**
```
CourseAISummaryDTO
  → courseId, success, summary, error

CourseKeywordsDTO
  → courseId, success, keywords, keywordsList, error

WikipediaResourceDTO
  → title, summary, url, source, relevanceScore

ComplementaryResourcesDTO
  → success, articles[], count, error

CourseSearchResponseDTO
  → items[], pagination{total, page, limit, pages}
```

---

## 🔌 Configuration API

### `.env` - Variables d'environnement

```dotenv
# OpenAI (déjà présent)
OPENAI_API_KEY=sk-proj-xxxxxx

# Wikipedia (publique, aucune clé requise)
WIKIPEDIA_API_URL=https://fr.wikipedia.org/w/api.php

# Configuration des services
COURSE_AI_SUMMARY_ENABLED=true
WIKIPEDIA_RESOURCES_ENABLED=true
```

### Fichier `config/services.yaml`

```yaml
services:
  App\Service\OpenAICourseService:
    arguments:
      - '@Symfony\Contracts\HttpClient\HttpClientInterface'
      - '@logger'
      - '%env(OPENAI_API_KEY)%'

  App\Service\WikipediaResourceService:
    arguments:
      - '@Symfony\Contracts\HttpClient\HttpClientInterface'
      - '@logger'

  App\Service\CourseService:
    arguments:
      - '@App\Repository\CourseRepository'
      - '@doctrine.orm.entity_manager'
      - '@App\Service\OpenAICourseService'
      - '@App\Service\WikipediaResourceService'
      - '@logger'
```

---

## 📡 Exemples API - Requêtes & Réponses JSON

### **1️⃣ Recherche Avancée Paginée**

**Requête:**
```bash
GET /cours/api/search?title=JavaScript&category=Développement&keywords=web,backend&sort=popularity&page=1&limit=12
```

**Réponse (200 OK):**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "titre": "JavaScript Avancé",
        "description": "Maîtrisez ES6+ et les concepts avancés",
        "category": "Développement",
        "price": "29.99",
        "image_url": "https://...",
        "summary": "Ce cours couvre les concepts avancés de JavaScript...",
        "keywords": "javascript,es6,async,promises,web",
        "views": 1250,
        "likes": 85,
        "popularity_score": 8.5
      }
    ],
    "pagination": {
      "total": 42,
      "page": 1,
      "limit": 12,
      "pages": 4
    }
  }
}
```

---

### **2️⃣ Détail Cours + Ressources Wikipedia**

**Requête:**
```bash
GET /cours/api/1
```

**Réponse (200 OK):**
```json
{
  "success": true,
  "data": {
    "course": {
      "id": 1,
      "title": "JavaScript Avancé",
      "description": "Cours complet sur JavaScript ES6+...",
      "category": "Développement",
      "price": "29.99",
      "image_url": "https://...",
      "summary": "Apprenez les concepts avancés de JavaScript, les async/await, les promises et les patterns modernes.",
      "keywords": "javascript,async,promises,es6,web development",
      "views": 1250,
      "likes": 85
    },
    "complementary_resources": [
      {
        "title": "JavaScript",
        "summary": "JavaScript est un langage de programmation léger et interprété...",
        "url": "https://fr.wikipedia.org/wiki/JavaScript",
        "source": "Wikipedia",
        "relevance_score": 2.0
      },
      {
        "title": "Promise (programmation)",
        "summary": "En programmation, une promise (promesse) est...",
        "url": "https://fr.wikipedia.org/wiki/Promise_(programmation)",
        "source": "Wikipedia",
        "relevance_score": 1.8
      }
    ]
  }
}
```

---

### **3️⃣ Générer Résumé IA**

**Requête:**
```bash
POST /cours/api/1/generate-summary
Content-Type: application/json
```

**Réponse (200 OK):**
```json
{
  "success": true,
  "message": "Résumé IA généré avec succès",
  "data": {
    "course_id": 1,
    "success": true,
    "summary": "Ce cours couvre les concepts avancés de JavaScript incluant ES6+, async/await, promises, destructuring, et les patterns modernes. Vous apprendrez à écrire du code plus propre et efficace avec les fonctionnalités les plus utilisées des versions récentes de JavaScript. Idéal pour les développeurs intermédiaires cherchant à maîtriser les concepts essentiels.",
    "error": null
  }
}
```

**Réponse d'erreur (400 Bad Request):**
```json
{
  "success": false,
  "error": "OpenAI API non configurée. Définissez OPENAI_API_KEY dans .env"
}
```

---

### **4️⃣ Générer Mots-clés IA**

**Requête:**
```bash
POST /cours/api/1/generate-keywords
Content-Type: application/json
```

**Réponse (200 OK):**
```json
{
  "success": true,
  "message": "Mots-clés générés avec succès",
  "data": {
    "course_id": 1,
    "success": true,
    "keywords": "javascript,es6+,async await,promises,destructuring,développement web,react,node.js,programming,backend",
    "keywords_list": [
      "javascript",
      "es6+",
      "async await",
      "promises",
      "destructuring",
      "développement web",
      "react",
      "node.js",
      "programming",
      "backend"
    ],
    "error": null
  }
}
```

---

### **5️⃣ Récupérer Ressources Wikipedia**

**Requête:**
```bash
GET /cours/api/1/resources
```

**Réponse (200 OK):**
```json
{
  "success": true,
  "data": {
    "success": true,
    "count": 3,
    "articles": [
      {
        "title": "JavaScript",
        "summary": "JavaScript est un langage de programmation léger et interprété, initialement utilisé dans les navigateurs web.",
        "url": "https://fr.wikipedia.org/wiki/JavaScript",
        "source": "Wikipedia",
        "relevance_score": 2.0
      },
      {
        "title": "Asynchronisme",
        "summary": "L'asynchronisme en programmation permet l'exécution non-bloquante d'opérations longues.",
        "url": "https://fr.wikipedia.org/wiki/Asynchronisme",
        "source": "Wikipedia",
        "relevance_score": 1.7
      },
      {
        "title": "Langage de programmation",
        "summary": "Un langage de programmation est un système de communication permettant à un humain de dialoguer avec un ordinateur.",
        "url": "https://fr.wikipedia.org/wiki/Langage_de_programmation",
        "source": "Wikipedia",
        "relevance_score": 1.5
      }
    ],
    "error": null
  }
}
```

---

### **6️⃣ Incrémenter les Likes**

**Requête:**
```bash
POST /cours/api/1/like
Content-Type: application/json
```

**Réponse (200 OK):**
```json
{
  "success": true,
  "message": "Cours liké avec succès"
}
```

---

### **7️⃣ Récupérer Cours Populaires**

**Requête:**
```bash
GET /cours/api/popular?limit=6
```

**Réponse (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "JavaScript Avancé",
      "category": "Développement",
      "popularity_score": "8.5",
      "views": 1250
    },
    {
      "id": 3,
      "title": "React from Scratch",
      "category": "Développement",
      "popularity_score": "8.2",
      "views": 980
    }
  ]
}
```

---

### **8️⃣ Récupérer Cours Récents**

**Requête:**
```bash
GET /cours/api/latest?limit=6
```

**Réponse (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 42,
      "title": "Rust - Programmation Système",
      "category": "Développement",
      "image_url": "https://...",
      "created_at": "2026-02-23 10:30:00"
    },
    {
      "id": 41,
      "title": "Python Machine Learning",
      "category": "Science",
      "image_url": "https://...",
      "created_at": "2026-02-22 15:45:00"
    }
  ]
}
```

---

## 🔍 Critères de Tri Disponibles

| Paramètre | Description |
|-----------|-------------|
| `date` | Récents en premier (défaut) |
| `popularity` | Par score de popularité |
| `views` | Par nombre de vues |
| `price_asc` | Prix croissant |
| `price_desc` | Prix décroissant |

---

## ⚠️ Gestion des Erreurs

Tous les endpoints API retournent un `success: bool` et peuvent inclure une clé `error`:

**Code HTTP:**
- `200 OK` : Requête réussie
- `400 Bad Request` : Données invalides ou API non configurée
- `404 Not Found` : Ressource non trouvée
- `500 Internal Server Error` : Erreur serveur

**Exemple d'erreur:**
```json
{
  "success": false,
  "error": "Cours non trouvé"
}
```

---

## 🚀 Utilisation des Services

### Dans un Contrôleur

```php
// Dans un contrôleur quelconque
public function __construct(
    private CourseService $courseService
) {}

// Créer un cours avec IA
$result = $this->courseService->createCourseWithAI(
    'JavaScript Avancé',
    'Apprenez ES6+...',
    'Développement'
);

if ($result['course']) {
    // Cours créé
    echo "Résumé: " . $result['summary'];
    echo "Mots-clés: " . $result['keywords'];
}

// Recherche avancée
$results = $this->courseService->searchCourses(
    title: 'JavaScript',
    category: 'Développement',
    sortBy: 'popularity',
    page: 1,
    limit: 12
);
```

---

## 🔐 Sécurité

1. **Clés API** : Toujours définit dans `.env.local`, jamais en Git
2. **Rate Limiting** : Implémenter si besoin (OpenAI applique limites)
3. **Validation** : Données validées côté serveur/Doctrine
4. **CORS** : À configurer si API consommée par frontend externe

---

## 🐛 Troubleshooting

### Les résumés IA ne sont pas générés

1. Vérifier `OPENAI_API_KEY` dans `.env`
2. Vérifier quota OpenAI
3. Consulter les logs: `var/log/dev.log`

### Ressources Wikipedia manquantes

1. Wikipedia API est publique, pas de clé requise
2. Vérifier que `keywords` du cours ne sont pas vides
3. Vérifier la langue (API français par défaut)

### Erreurs de pagination

```php
// Pagination valide
$page = max(1, $page);        // Min 1
$limit = min(100, $limit);    // Max 100
```

---

## 📋 Checklist Implémentation

- ✅ Services OpenAI et Wikipedia créés
- ✅ CourseService (orchestration métier)
- ✅ CourseController avec endpoints API
- ✅ CourseRepository avec pagination
- ✅ DTOs pour réponses structurées
- ✅ Gestion erreurs complète
- ✅ Logs détaillés
- ✅ Documentation API complète
- ⚠️ Tests unitaires (À faire)
- ⚠️ Frontend (À adapter/créer)

