<?php
namespace App\Service;

class CategoryAutoService
{
    private array $map = [];

    public function __construct()
    {
        // simple keyword to category mapping (French)
        $this->map = [
            'note' => 'Note/Évaluation',
            'note\b' => 'Note/Évaluation',
            'bulletin' => 'Note/Évaluation',
            'contact' => 'Contact',
            'professeur' => 'Contact',
            'inscription' => 'Inscription',
            'paiement' => 'Paiement',
            'facture' => 'Paiement',
            'erreur' => 'Technique',
            'bug' => 'Technique',
            'panne' => 'Technique',
            'connexion' => 'Technique',
            'absence' => 'Absence',
            'retard' => 'Absence',
            'diplome' => 'Diplôme',
            'stage' => 'Stage',
            'rendez-vous' => 'Rendez-vous',
        ];
    }

    public function categorize(string $text): string
    {
        $t = mb_strtolower($text);
        foreach ($this->map as $k => $cat) {
            // use word boundary or simple strpos
            if (preg_match('/'.preg_quote($k, '/').'/u', $t)) {
                return $cat;
            }
        }
        return 'Autre';
    }
}
