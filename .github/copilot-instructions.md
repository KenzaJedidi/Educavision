# AI Copilot Instructions for EducaVision Symfony Project

## Project Overview
EducaVision is a Symfony 6.4 educational management system for managing academic **filieres** (study programs), **formations** (courses), **metiers** (careers), **prerequis** (prerequisites), and **simulation** (career simulations). Built with Doctrine ORM, Twig templating, and the Stimulus JavaScript framework for interactivity.

## Architecture Overview

### Entity Model & Relationships
- **Core Entities** (in [src/Entity/](src/Entity/)):
  - `Filiere` → has many `Metier` (one-to-many, orphanRemoval=true)
  - `Formation` → related to `Filiere`
  - `Metier` → career paths within a filiere
  - `Prerequis` → prerequisites for formations
  - `Simulation` → career simulation data
- **Key Pattern**: All entities use Doctrine attributes (`#[ORM\...]`) for mapping; getter/setter methods follow PSR naming conventions
- **Timestamps**: Entities typically have `dateCreation` fields set to `new \DateTime()` on create

### MVC Structure
- **Controllers** ([src/Controller/Admin/](src/Controller/Admin/)):
  - Route attributes: `#[Route('/admin/{resource}')]` at class level; individual actions use `#[Route('/{action}', name: 'admin_{resource}_{action}', methods: [...])]`
  - Pattern: `index`, `new`, `show`, `edit`, `delete` - standard CRUD
  - Dependency injection: Controllers receive `FiliereRepository`, `EntityManagerInterface`, `Request` as constructor/method params
  - Form handling: `$form->handleRequest()` then `if ($form->isSubmitted() && $form->isValid())`
- **Forms** ([src/Form/](src/Form/)):
  - `buildForm()` adds fields matching entity properties
  - `configureOptions()` sets `data_class` to the entity
  - Auto-wiring in controllers: `$this->createForm(FiliereType::class, $entity)`
- **Templates** ([templates/admin/](templates/admin/)):
  - Extend `admin/layout.html.twig` (dashboard layout with breadcrumbs, sidebar)
  - Use blocks: `{% block title %}`, `{% block breadcrumb_title %}`, `{% block body %}`
  - Asset path helper: `{{ asset('uploads/filieres/' ~ filiere.image) }}` for user uploads

### Database & Migrations
- **Driver**: Doctrine ORM with Symfony 6.4 migrations
- **Config** [config/packages/doctrine.yaml](config/packages/doctrine.yaml):
  - Naming strategy: `doctrine.orm.naming_strategy.underscore_number_aware`
  - Auto-mapping: Entities in `src/Entity` with `App\Entity` namespace
  - Lazy ghost objects enabled
- **Migrations**: [migrations/](migrations/) directory contains version files; execute with `bin/console doctrine:migrations:migrate`
- **Test Database**: Tests run against separate DB (suffix via `TEST_TOKEN` env var)

### Routing & Security
- **Routes** [config/routes.yaml](config/routes.yaml):
  - Auto-load controllers from `src/Controller/` via attribute routing
  - Admin routes prefixed `/admin/{resource}`
- **Security** [config/packages/security.yaml](config/packages/security.yaml):
  - Basic in-memory provider (dev default); no user authentication configured for production yet
  - `/admin` routes are NOT currently role-protected (see commented access_control)

## Development Workflows

### Setup & Database
```bash
# Install dependencies
composer install

# Create database and run migrations
bin/console doctrine:database:create
bin/console doctrine:migrations:migrate

# Run development server
symfony serve  # or: php -S localhost:8000 -t public/
```

### Testing
```bash
# Run PHPUnit tests
bin/console phpunit
# OR directly:
./bin/phpunit

# Test configuration: phpunit.dist.xml sets APP_ENV=test, failOnDeprecation=true
```

### Asset Management
- **Asset Mapper** (Symfony 6.4): Uses `importmap.php`
- Assets compiled to `public/assets/`
- Post-install: `importmap:install` runs automatically via Composer scripts
- Frontend: Bootstrap 5 + Font Awesome in [assets/vendors/](assets/vendors/)

### Code Patterns & Conventions

#### Controller Action Pattern
```php
#[Route('/{id}/edit', name: 'admin_resource_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Resource $resource, EntityManagerInterface $em): Response
{
    $form = $this->createForm(ResourceType::class, $resource);
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();  // No persist needed if entity already managed
        $this->addFlash('success', 'Message de succès !');
        return $this->redirectToRoute('admin_resource_index', [], Response::HTTP_SEE_OTHER);
    }
    
    return $this->render('admin/resource/edit.html.twig', [
        'resource' => $resource,
        'form' => $form,
    ]);
}
```

#### Entity Getters/Setters
- Use typed properties with `?int $id = null` or `?string $nom = null`
- Return `static` from setters for method chaining
- Relations use `Collection` type from `Doctrine\Common\Collections\ArrayCollection`

#### Template Inheritance
```twig
{% extends 'admin/layout.html.twig' %}

{% block title %}Titre de la page{% endblock %}
{% block breadcrumb_title %}Titre du breadcrumb{% endblock %}
{% block body %}
  {# Content here #}
{% endblock %}
```

#### French Localization
- UI strings in French (e.g., "La filière a été créée avec succès !")
- Date format: `{{ date|date('d/m/Y') }}`
- Translation config: [config/packages/translation.yaml](config/packages/translation.yaml)

## Common Tasks

### Add a New Admin Resource
1. Create Entity in [src/Entity/ResourceName.php](src/Entity/) with Doctrine attributes
2. Create Form in [src/Form/ResourceNameType.php](src/Form/) using `AbstractType`
3. Create Repository: Symfony Maker auto-generates or extend `ServiceEntityRepository`
4. Create Controller in [src/Controller/Admin/ResourceNameController.php](src/Controller/Admin/) extending `AbstractController`
5. Create Twig templates in [templates/admin/resourcename/](templates/admin/) extending `admin/layout.html.twig`
6. Run migrations if new database fields: `bin/console make:migration && bin/console doctrine:migrations:migrate`

### Add Database Column to Existing Entity
1. Add typed property + ORM attribute to Entity class
2. Regenerate migration: `bin/console make:migration`
3. Review generated migration file in [migrations/](migrations/)
4. Execute: `bin/console doctrine:migrations:migrate`
5. Update corresponding Form class to include new field

### Troubleshooting
- **Entity not found in route**: Check parameter type hint in controller method matches entity class name
- **Form rendering errors**: Ensure Form class `configureOptions()` sets correct `data_class`
- **Database sync issues**: Run `bin/console doctrine:schema:validate` to check ORM mapping

## File Organization Quick Reference
- Controllers: [src/Controller/Admin/](src/Controller/Admin/)
- Entities & Repositories: [src/Entity/](src/Entity/), [src/Repository/](src/Repository/)
- Forms: [src/Form/](src/Form/)
- Admin Templates: [templates/admin/](templates/admin/)
- Config: [config/packages/](config/packages/) (Doctrine, Security, Framework, etc.)
- Public Assets: [public/assets/](public/assets/)
- Frontend Assets (compiled): [assets/](assets/) with [importmap.php](importmap.php)
