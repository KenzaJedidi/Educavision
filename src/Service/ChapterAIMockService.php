<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

/**
 * Service Mock pour développement (quand API Gemini/OpenAI indisponible)
 * Retourne des données réalistes sans appeler une API externe
 */
class ChapterAIMockService
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    /**
     * Enrichit le contenu simulé (Mock)
     */
    public function enrichChapterContent(string $title, string $originalContent, string $category = ''): ?string
    {
        sleep(2); // Simule le temps d'appel API
        
        $enriched = "# " . htmlspecialchars($title) . " - Contenu Enrichi\n\n";
        $enriched .= "## 📚 Introduction\n";
        $enriched .= htmlspecialchars($originalContent) . "\n\n";
        
        $enriched .= "## 🔑 Concepts Clés\n";
        $enriched .= "- **Fondamental**: Les bases à comprendre\n";
        $enriched .= "- **Pratique**: Application dans des cas réels\n";
        $enriched .= "- **Avancé**: Approfondissements possibles\n\n";
        
        $enriched .= "## 💡 Exemples Concrets\n";
        $enriched .= "1. Exemple 1: Cas d'application simple\n";
        $enriched .= "2. Exemple 2: Cas plus complexe\n";
        $enriched .= "3. Exemple 3: Cas avancé\n\n";
        
        $enriched .= "[Important: Consultez la documentation officielle pour plus de détails]\n\n";
        
        $enriched .= "## 📝 Points à Retenir\n";
        $enriched .= "- Point principal 1\n";
        $enriched .= "- Point principal 2\n";
        $enriched .= "- Point principal 3\n";

        $this->logger->info('📊 (MOCK) Chapitre enrichi', [
            'title' => $title,
            'length' => strlen($enriched),
        ]);

        return $enriched;
    }

    /**
     * Détecte le niveau de difficulté simulé
     */
    public function detectDifficultyLevel(string $title, string $content): ?string
    {
        sleep(1); // Simule le temps d'appel API
        
        // Algorithme simple basé sur la longueur du contenu
        $length = strlen($content);
        
        if ($length < 100) {
            $level = 'débutant';
        } elseif ($length < 300) {
            $level = 'intermédiaire';
        } else {
            $level = 'avancé';
        }

        $this->logger->info('📊 (MOCK) Niveau détecté', [
            'title' => $title,
            'level' => $level,
        ]);

        return $level;
    }

    /**
     * Génère un plan structuré simulé
     */
    public function generateStructuredOutline(string $title, string $content): ?string
    {
        sleep(2); // Simule le temps d'appel API
        
        $outline = "# 📋 Plan Structuré: " . htmlspecialchars($title) . "\n\n";
        
        $outline .= "## Section 1: Introduction et Contexte\n";
        $outline .= "- Définition du sujet\n";
        $outline .= "- Importance et applications\n";
        $outline .= "  - Cas d'usage 1.1\n";
        $outline .= "  - Cas d'usage 1.2\n\n";
        
        $outline .= "## Section 2: Concepts Fondamentaux\n";
        $outline .= "- Concept principal\n";
        $outline .= "- Concepts secondaires\n";
        $outline .= "  - Sous-concept 2.1\n";
        $outline .= "  - Sous-concept 2.2\n";
        $outline .= "  - Sous-concept 2.3\n\n";
        
        $outline .= "## Section 3: Approfondissements Pratiques\n";
        $outline .= "- Exemple pratique 1\n";
        $outline .= "- Exemple pratique 2\n";
        $outline .= "- Exercices et cas d'application\n\n";
        
        $outline .= "## Section 4: Points Avancés et Extensions\n";
        $outline .= "- Développements possibles\n";
        $outline .= "- Ressources complémentaires\n";
        $outline .= "- Liens avec d'autres domaines\n\n";
        
        $outline .= "## Section 5: Conclusion et Perspectives\n";
        $outline .= "- Récapitulatif\n";
        $outline .= "- Points clés à retenir\n";
        $outline .= "- Prochaines étapes d'apprentissage\n";

        $this->logger->info('📊 (MOCK) Plan généré', [
            'title' => $title,
            'sections' => 5,
        ]);

        return $outline;
    }
}
