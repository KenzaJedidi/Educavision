# 📚 MODULE CHAPITRE - DOCUMENTATION COMPLÈTE

## 🎯 Fonctionnalités Implémentées

### ✅ 1. Enrichissement Automatique par IA (OpenAI)
- **Enrichissement du contenu** - Améliore et clarifie le texte saisi
- **Détection du niveau** - Classe automatiquement en débutant / intermédiaire / avancé
- **Génération de plan** - Crée une structure hiérarchisée pour le chapitre

### ✅ 2. Traduction Automatique (LibreTranslate)
- **Traduction multi-langues** - EN, ES, DE, IT, PT, RU, JA, ZH, AR
- **Stockage persistant** - Sauvegarde les traductions en JSON
- **Traduction complète** - Titre + description + contenu enrichi

### ✅ 3. Gestion des Chapitres
- **Statuts** - Draft (brouillon) / Published (publié)
- **Ordre dynamique** - Champ `position` pour drag & drop
- **Historique** - Champs `created_at` et `updated_at`
- **CRUD complet** - Créer, lire, mettre à jour, supprimer

---

## 🏗️ ARCHITECTURE

### Entité Chapter
```php
class Chapter {
    - id: int
    - titre: string
    - description: text
    - enriched_content: text (IA)
    - difficulty_level: string (débutant|intermédiaire|avancé)
    - structured_outline: text (plan IA)
    - status: string (draft|published)
    - position: int (pour drag & drop)
    - translations: json ({'en': {...}, 'es': {...}})
    - image_url: string
    - teacher_name: string
    - teacher_email: string
    - created_at: datetime
    - updated_at: datetime
    - course: ManyToOne Course
}
```

### Services
```
ChapterAIService
├── enrichChapterContent()      // Améliore le contenu
├── detectDifficultyLevel()     // Détecte le niveau
└── generateStructuredOutline() // Génère le plan

ChapterTranslationService
├── translateContent()          // Traduit le contenu
├── translateTitle()            // Traduit le titre
└── translateToMultiple()       // Traductions multiples

ChapterService (Orchestrateur)
├── createChapter()
├── enrichChapter()
├── detectDifficultyLevel()
├── generateOutline()
├── translateChapter()
├── reorderChapters()           // Drag & drop
├── publishChapter()
├── saveDraft()
├── deleteChapter()
└── updateChapter()
```

### Repository
```
ChapterRepository
├── findByCourseOrdered()       // Triés par position
├── findMaxPositionByCourse()
├── findByCourseAndStatus()     // Filtrer par statut
├── findWithEnrichedContent()
└── countByStatus()
```

---

## 🔌 API REST

### Base URL
```
/api/chapters
```

### 1. Liste des chapitres d'un cours
```
GET /api/chapters/course/{courseId}

Response:
{
    "success": true,
    "data": [
        {
            "id": 1,
            "titre": "Introduction",
            "description": "...",
            "status": "published",
            "position": 1,
            "difficultyLevel": "débutant",
            "hasEnrichedContent": true,
            "hasOutline": true,
            "availableTranslations": ["en", "es"]
        }
    ],
    "count": 3
}
```

### 2. Récupère un chapitre
```
GET /api/chapters/{id}

Response:
{
    "success": true,
    "data": {
        "id": 1,
        "titre": "Introduction",
        "description": "...",
        "enrichedContent": "...",
        "structuredOutline": "## Section 1\n- Point 1",
        "difficultyLevel": "débutant",
        "status": "published",
        "translations": {
            "en": {
                "titre": "Introduction",
                "description": "..."
            }
        }
    }
}
```

### 3. Enrichit un chapitre avec IA
```
POST /api/chapters/{id}/enrich

Les 3 opérations:
1. Enrichissement du contenu (max 2000 tokens)
2. Détection du niveau de difficulté
3. Génération du plan structuré

Response:
{
    "success": true,
    "data": {
        "chapterId": 1,
        "enrichedContent": "Contenu amélioré...",
        "difficultyLevel": "intermédiaire",
        "structuredOutline": "## Section 1\n- Point 1\n  - Sous-point"
    }
}
```

### 4. Traduit un chapitre
```
POST /api/chapters/{id}/translate

Request:
{
    "targetLanguage": "en"
}

Response:
{
    "success": true,
    "data": {
        "chapterId": 1,
        "language": "en",
        "translations": {
            "titre": "Introduction",
            "description": "Translated description"
        }
    }
}
```

### 5. Réorganise les chapitres (Drag & Drop)
```
POST /api/chapters/reorder

Request:
{
    "courseId": 1,
    "chapterPositions": [
        { "chapterId": 3, "position": 1 },
        { "chapterId": 1, "position": 2 },
        { "chapterId": 2, "position": 3 }
    ]
}

Response:
{
    "success": true,
    "message": "Chapitres réorganisés avec succès"
}
```

### 6. Publie un chapitre
```
POST /api/chapters/{id}/publish

Response:
{
    "success": true,
    "data": {
        "id": 1,
        "status": "published",
        "message": "Chapitre publié avec succès"
    }
}
```

### 7. Sauvegarde un brouillon
```
POST /api/chapters/{id}/draft

Request (optionnel):
{
    "titre": "Nouveau titre",
    "description": "Nouvelle description",
    "imageUrl": "url.jpg"
}

Response:
{
    "success": true,
    "data": {
        "id": 1,
        "status": "draft",
        "message": "Brouillon sauvegardé avec succès"
    }
}
```

### 8. Supprime un chapitre
```
DELETE /api/chapters/{id}

Response:
{
    "success": true,
    "message": "Chapitre supprimé avec succès"
}
```

### 9. Langues disponibles
```
GET /api/chapters/languages/available

Response:
{
    "success": true,
    "data": {
        "en": "English",
        "es": "Español",
        "de": "Deutsch",
        "it": "Italiano",
        "pt": "Português",
        "ru": "Русский",
        "ja": "Japanese",
        "zh": "中文",
        "ar": "العربية"
    }
}
```

---

## 🔐 CONFIGURATION

### Variables d'Environnement (.env)
```env
# Clé API OpenAI (requis pour enrichissement IA)
OPENAI_API_KEY=sk-...

# LibreTranslate (gratuit, pas de clé requise)
# https://api.libretranslate.de est l'endpoint public
```

### Configuration Symfony (services.yaml)
```yaml
App\Service\ChapterAIService:
    public: true
    arguments:
        $openaiApiKey: '%env(default::OPENAI_API_KEY)%'

App\Service\ChapterTranslationService:
    public: true

App\Service\ChapterService:
    public: true
```

---

## 🚀 UTILISATION

### 1. En PHP (Service)
```php
// Injecter le service
public function __construct(private ChapterService $chapterService) {}

// Créer un chapitre
$chapter = $this->chapterService->createChapter(
    $course,
    'Titre du chapitre',
    'Description...',
    'image.jpg',
    'Jean Dupont',
    'jean@example.com'
);

// Enrichir le chapitre
$chapter = $this->chapterService->enrichChapter($chapter);

// Traduire
$chapter = $this->chapterService->translateChapter($chapter, 'en');

// Réorganiser
$this->chapterService->reorderChapters($course, [
    1 => 2,  // Le chapitre 1 passe à la position 2
    2 => 1,  // Le chapitre 2 passe à la position 1
]);

// Publier
$chapter = $this->chapterService->publishChapter($chapter);
```

### 2. En JavaScript (Client API)
```javascript
const api = new ChapterAPIClient();

// Récupérer les chapitres
const response = await api.getChaptersByCourse(1);
console.log(response.data);

// Enrichir un chapitre
const enriched = await api.enrichChapter(5);
console.log('Nouveau contenu:', enriched.data.enrichedContent);
console.log('Niveau:', enriched.data.difficultyLevel);

// Traduire
const translated = await api.translateChapter(5, 'en');
console.log('Traduction anglaise:', translated.data.translations);

// Réorganiser (drag & drop)
const newOrder = [
    { chapterId: 3, position: 1 },
    { chapterId: 1, position: 2 },
];
await api.reorderChapters(1, newOrder);

// Publier
await api.publishChapter(5);
```

---

## 📊 CAS D'UTILISATION

### Workflow Type 1: Créer et Publier un Chapitre
```
1. Créer le chapitre (titre + description basique)
2. Cliquer "Enrichir avec IA" (améliore + détecte niveau + génère plan)
3. Vérifier le contenu enrichi
4. Ajouter traductions (optionnel)
5. Cliquer "Publier"
```

### Workflow Type 2: Modifier l'Ordre des Chapitres
```
1. Afficher la liste des chapitres
2. Activer le drag & drop
3. Réorganiser les chapitres
4. L'ordre est mis à jour automatiquement via API
```

### Workflow Type 3: Ajouter des Traductions
```
1. Ouvrir un chapitre
2. Cliquer "Traduire en"
3. Sélectionner la langue (EN, ES, DE, etc)
4. Traduction automatique (titre + description + contenu enrichi)
5. Résultats stockés en JSON dans le chapitre
```

---

## ✅ VALIDATION ET GESTION D'ERREURS

### Validation Validée
```
- Status: 'draft' ou 'published' uniquement
- DifficultyLevel: 'débutant'|'intermédiaire'|'avancé' uniquement
- Position: nombre positif
- Language codes: ISO 639-1 (en, es, fr, etc)
```

### Gestion d'Erreurs
```
- API invalide (response 400) → Message d'erreur du service
- Chapitre non trouvé (404) → "Chapitre non trouvé"
- Clé API manquante → Logs avec détails
- Timeout réseau → Message générique
```

---

## 🔄 MIGRATIONS

Exécuter la migration:
```bash
php bin/console doctrine:migrations:migrate
```

Champs ajoutés:
- `status` VARCHAR(50) - Draft ou Published
- `enriched_content` LONGTEXT - Contenu enrichi IA
- `difficulty_level` VARCHAR(50) - Niveau détecté
- `translations` JSON - Traductions stockées
- `position` INT - Ordre drag & drop
- `structured_outline` LONGTEXT - Plan IA
- `updated_at` DATETIME - Historique

Indexes créés:
- `idx_chapter_status`
- `idx_chapter_position`
- `idx_chapter_course_position`

---

## 🧪 TESTS QUICK

```bash
# Enrichir un chapitre
curl -X POST http://localhost/api/chapters/1/enrich

# Traduire en anglais
curl -X POST http://localhost/api/chapters/1/translate \
  -H "Content-Type: application/json" \
  -d '{"targetLanguage":"en"}'

# Réorganiser
curl -X POST http://localhost/api/chapters/reorder \
  -H "Content-Type: application/json" \
  -d '{
    "courseId": 1,
    "chapterPositions": [
      {"chapterId":1,"position":2},
      {"chapterId":2,"position":1}
    ]
  }'
```

---

## 📝 NOTES

- ✅ Code fonctionnel testé et vérifié
- ✅ Design existant respecté (ajoute des champs DB)
- ✅ MVC strict maintenu (Service + Repository + Controller)
- ✅ Gestion d'exceptions complète
- ✅ Logs détaillés pour debugging
- ✅ Validation des paramètres
- ✅ DTOs pour structure des réponses
- ✅ Client JavaScript integrated ready (Sortable.js needed)
- ✅ API REST RESTful complète
- ✅ Support des langues multiples

---

**Module CHAPITRE v1.0 - Prêt pour production** ✅
