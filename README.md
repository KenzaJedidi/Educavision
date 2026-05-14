# 🚀 EducaVision Backend - API Symfony 6.4

[![Symfony](https://img.shields.io/badge/Symfony-6.4-black.svg)](https://symfony.com/)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-blue.svg)](https://www.mysql.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## 📖 À propos

Backend RESTful pour la plateforme **EducaVision** - Gestion complète des 6 modules pédagogiques avec API sécurisée.

---

## 🏗️ Architecture Backend
src/
├── Entity/ # Entités Doctrine (Base de données)
│ ├── Utilisateur.php # Utilisateurs (6 rôles)
│ ├── Course.php # Cours
│ ├── Chapter.php # Chapitres
│ ├── Quiz.php # Quiz/Tests
│ ├── Question.php # Questions
│ ├── Answer.php # Réponses
│ ├── Result.php # Résultats tests
│ ├── Formation.php # Formations
│ ├── Candidature.php # Inscriptions formations
│ ├── Filiere.php # Filières professionnelles
│ ├── Metier.php # Métiers
│ ├── Simulation.php # Simulations
│ ├── OffreStage.php # Offres stage/emploi
│ ├── Reclamation.php # Réclamations
│ ├── Message.php # Messages
│ ├── ConversationMessage.php # Conversations
│ └── Prerequis.php # Prérequis
│
├── Controller/ # Contrôleurs API
│ ├── AuthController.php # Authentification
│ ├── CourseController.php # Gestion cours
│ ├── QuizController.php # Gestion quiz
│ ├── FormationController.php # Formations
│ ├── FiliereController.php # Filières
│ ├── InternshipController.php # Offres stage
│ └── UserController.php # Profils utilisateurs
│
├── Service/ # Logique métier
│ ├── AuthService.php
│ ├── CourseService.php
│ ├── QuizService.php
│ ├── FormationService.php
│ └── ...
│
├── Repository/ # Requêtes BD
│ ├── CourseRepository.php
│ ├── QuizRepository.php
│ └── ...
│
├── DTO/ # Data Transfer Objects
├── Form/ # Formulaires Symfony
├── Command/ # Commandes CLI
├── Security/ # Authentification/Authorization
└── Kernel.php # Configuration Symfony

public/
├── index.php # Point d'entrée
├── uploads/ # Fichiers uploadés
└── assets/ # Ressources statiques
templates/
├── base.html.twig
└── ...

config/
├── services.yaml # Configuration services
├── routes.yaml # Routing API
└── security.yaml # Sécurité

migrations/ # Migrations Doctrine
tests/ # Tests unitaires/intégration
