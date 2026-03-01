# 🚀 Module Courses - Guide d'Installation & Configuration

## 📋 Prérequis

- **Symfony 6.x** installéinclus
- **PHP 8.1+**
- **Doctrine ORM**
- **HttpClient Symfony** (pour les appels API)
- **Clé OpenAI** (optionnelle mais recommandée)

---

## 🔧 Installation Complète

### 1. **Vérifier les dépendances installées**

```bash
# Vérifier symfony/http-client
composer show | grep http-client

# Si absent, installer:
composer require symfony/http-client
```

### 2. **Copier les fichiers créés**

Les fichiers suivants ont été créés/modifiés:

```
src/
  ├── Service/
  │   ├── OpenAICourseService.php        (NEW)
  │   ├── WikipediaResourceService.php   (NEW)
  │   └── CourseService.php              (NEW)
  ├── Controller/Front/
  │   └── CourseController.php           (MODIFIED)
  ├── Repository/
  │   └── CourseRepository.php           (MODIFIED)
  ├── Entity/
  │   └── Course.php                     (Unchanged - fields exist)
  └── DTO/
      ├── CourseAISummaryDTO.php         (NEW)
      ├── CourseKeywordsDTO.php          (NEW)
      ├── WikipediaResourceDTO.php       (NEW)
      ├── ComplementaryResourcesDTO.php  (NEW)
      └── CourseSearchResponseDTO.php    (NEW)
```

### 3. **Configuration `.env.local`**

```dotenv
###> openai-api ###
# Obtenir une clé sur: https://platform.openai.com/api-keys
OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxxxxxxxxxx
###< openai-api ###

###> course-module ###
# Wikipedia (publique - aucune clé requise)
WIKIPEDIA_API_ENABLED=true
COURSE_AI_ENABLED=true
COURSE_PAGINATION_LIMIT=12
###< course-module ###
```

### 4. **Vérifier la configuration des services**

Symfony charge automatiquement les services via l'autowiring. Vérifier les dépendances dans le Contrôleur:

```php
use App\Service\CourseService;

public function __construct(
    private CourseService $courseService
) {}
```

### 5. **Créer/Mettre à jour la base de données**

```bash
# Générer Migration (si champs manquants)
php bin/console make:migration

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Ou charger les fixtures
php bin/console doctrine:fixtures:load
```

---

## ✅ Vérification de l'Installation

### **1. Vérifier les services**

```bash
# Lister les services disponibles
php bin/console debug:container Course

# Output attendu:
# App\Service\CourseService
# App\Service\OpenAICourseService
# App\Service\WikipediaResourceService
```

### **2. Vérifier les routes API**

```bash
# Afficher toutes les routes "/cours/api"
php bin/console debug:router | grep "api"

# Routes attendues:
# api_courses_search
# api_course_show
# api_course_generate_summary
# api_course_generate_keywords
# api_course_resources
# api_course_like
# api_courses_popular
# api_courses_latest
```

### **3. Tester une requête API simple**

```bash
curl -X GET "http://localhost:8000/cours/api/popular?limit=3" \
  -H "Accept: application/json"
```

Réponse attendue:
```json
{
  "success": true,
  "data": [...]
}
```

---

## 🔑 Configuration des Clés API

### **OpenAI API**

1. **Créer un compte**: https://platform.openai.com/signup
2. **Générer une clé**:
   - Aller à: https://platform.openai.com/api-keys
   - Click "Create new secret key"
   - Copier la clé
3. **Ajouter à `.env.local`**:
   ```dotenv
   OPENAI_API_KEY=sk-proj-xxxxx
   ```

### **Wikipedia API** ✅

Aucune clé requise ! Wikipedia API est publique.

---

## 📊 Champs de la Table Course

Assurez-vous que la table `course` possède ces colonnes:

```sql
ALTER TABLE course ADD COLUMN IF NOT EXISTS wikipedia_summary LONGTEXT NULL;
ALTER TABLE course ADD COLUMN IF NOT EXISTS keywords VARCHAR(500) NULL;
ALTER TABLE course ADD COLUMN IF NOT EXISTS views INT DEFAULT 0;
ALTER TABLE course ADD COLUMN IF NOT EXISTS likes INT DEFAULT 0;
ALTER TABLE course ADD COLUMN IF NOT EXISTS popularity_score DECIMAL(10,2) DEFAULT 0;
ALTER TABLE course ADD COLUMN IF NOT EXISTS last_accessed DATETIME NULL;
```

Ou via Doctrine Migration:

```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

---

## 🧪 Tests

### **Test WebClient (HTTP Requests)**

```php
// tests/Controller/CourseControllerTest.php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CourseControllerTest extends WebTestCase
{
    public function testSearchCourses(): void
    {
        $client = static::createClient();
        $client->request('GET', '/cours/api/search?title=Java&page=1&limit=10');

        $this->assertResponseIsSuccessful();
        $this->assertJsonStringEqualsJsonString(
            json_encode(['success' => true]),
            $client->getResponse()->getContent()
        );
    }

    public function testGenerateSummary(): void
    {
        $client = static::createClient();
        $client->request('POST', '/cours/api/1/generate-summary');

        $this->assertResponseIsSuccessful();
    }
}
```

Lancer les tests:

```bash
php bin/phpunit tests/Controller/CourseControllerTest.php
```

---

## 🔍 Debugging

### **Vérifier connexion OpenAI**

```php
// Dans un contrôleur ou commande
public function __construct(
    private OpenAICourseService $openAIService
) {}

public function test(): void
{
    if ($this->openAIService->isConfigured()) {
        echo "✅ OpenAI API est configurée";
    } else {
        echo "❌ OpenAI API NOT configurée";
    }
}
```

### **Consulter les logs**

```bash
# Logs d'erreur
tail -f var/log/dev.log

# Filtrer par classe
tail -f var/log/dev.log | grep "CourseService"
```

### **Vérifier les appels API**

```php
// Debugger les appels HTTP
// Dans CourseController:
dd($this->courseService->searchCourses('test'));
```

---

## 🚨 Erreurs Courantes

| Erreur | Cause | Solution |
|--------|-------|----------|
| `Service not found: CourseService` | Autowiring échoué | Vérifier config services.yaml ou use statement |
| `OPENAI_API_KEY not defined` | Variable env manquante | Ajouter dans .env.local |
| `400 Bad Request: Invalid API key` | Clé API invalide | Vérifier clé sur platform.openai.com |
| `Rate limit exceeded` | Trop d'appels OpenAI | Implémenter pagination/cache |
| `No courses found` | Tous les cours ont status=0 | Vérifier status dans DB |

---

## 📦 Structure Résumée

```
CourseController (API + HTML)
  ├── Injecte CourseService
  └── CourseService (Orchestration métier)
      ├── Appelle OpenAICourseService (Résumés + Keywords)
      ├── Appelle WikipediaResourceService (Ressources)
      └── Appelle CourseRepository (Data Access)

DTOs
  ├── CourseAISummaryDTO
  ├── CourseKeywordsDTO
  ├── ComplementaryResourcesDTO
  ├── CourseSearchResponseDTO
  └── WikipediaResourceDTO
```

---

## 🔐 Bonnes Pratiques

1. ✅ **Ne jamais commiter la clé OpenAI** - Utiliser `.env.local`
2. ✅ **Valider les données** - Symfony Validator
3. ✅ **Logger les erreurs** - Logger interface
4. ✅ **Gérer les timeouts** - HTTP Client timeout
5. ✅ **Cacher les résumés** - Éviter appels répétés
6. ✅ **Limiter la pagination** - Max 100 par page
7. ✅ **Documenter l'API** - Fichiers .md fournis

---

## 🎉 Installation Réussie !

Une fois tous les fichiers copiés et `.env.local` configuré:

```bash
# ✅ Test final
curl -X GET "http://localhost:8000/cours/api/search?page=1" | jq .

# Vous devriez obtenir une réponse JSON
{
  "success": true,
  "data": {...}
}
```

---

## 📞 Support

Pour toute question ou problème:

1. Vérifier les logs: `var/log/dev.log`
2. Lancer les tests: `php bin/phpunit`
3. Debugger avec `dd()` ou le Symfony Profiler
4. Vérifier la doc: `COURSE_MODULE_DOCUMENTATION.md`

