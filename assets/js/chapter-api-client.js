/**
 * Client JavaScript pour l'API des Chapitres
 * Gère l'enrichissement IA, traductions et réorganisation
 */

class ChapterAPIClient {
    constructor(baseUrl = '/api/chapters') {
        this.baseUrl = baseUrl;
    }

    /**
     * Récupère tous les chapitres d'un cours
     */
    async getChaptersByCourse(courseId) {
        try {
            const response = await fetch(`${this.baseUrl}/course/${courseId}`);
            if (!response.ok) throw new Error('Erreur réseau');
            return await response.json();
        } catch (error) {
            console.error('Erreur getChaptersByCourse:', error);
            throw error;
        }
    }

    /**
     * Récupère un chapitre spécifique
     */
    async getChapter(chapterId) {
        try {
            const response = await fetch(`${this.baseUrl}/${chapterId}`);
            if (!response.ok) throw new Error('Erreur réseau');
            return await response.json();
        } catch (error) {
            console.error('Erreur getChapter:', error);
            throw error;
        }
    }

    /**
     * Enrichit un chapitre avec IA
     * - Enrichit le contenu
     * - Détecte le niveau de difficulté
     * - Génère un plan structuré
     */
    async enrichChapter(chapterId) {
        try {
            const response = await fetch(`${this.baseUrl}/${chapterId}/enrich`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
            });

            if (!response.ok) throw new Error('Erreur enrichissement');
            return await response.json();
        } catch (error) {
            console.error('Erreur enrichChapter:', error);
            throw error;
        }
    }

    /**
     * Traduit un chapitre dans une langue cible
     */
    async translateChapter(chapterId, targetLanguage = 'en') {
        try {
            const response = await fetch(`${this.baseUrl}/${chapterId}/translate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    targetLanguage: targetLanguage,
                }),
            });

            if (!response.ok) throw new Error('Erreur traduction');
            return await response.json();
        } catch (error) {
            console.error('Erreur translateChapter:', error);
            throw error;
        }
    }

    /**
     * Réorganise les chapitres (drag & drop)
     * @param {number} courseId - ID du cours
     * @param {Array} chapterPositions - Array de {chapterId, position}
     */
    async reorderChapters(courseId, chapterPositions) {
        try {
            const response = await fetch(`${this.baseUrl}/reorder`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    courseId: courseId,
                    chapterPositions: chapterPositions,
                }),
            });

            if (!response.ok) throw new Error('Erreur réorganisation');
            return await response.json();
        } catch (error) {
            console.error('Erreur reorderChapters:', error);
            throw error;
        }
    }

    /**
     * Publie un chapitre
     */
    async publishChapter(chapterId) {
        try {
            const response = await fetch(`${this.baseUrl}/${chapterId}/publish`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
            });

            if (!response.ok) throw new Error('Erreur publication');
            return await response.json();
        } catch (error) {
            console.error('Erreur publishChapter:', error);
            throw error;
        }
    }

    /**
     * Sauvegarde un brouillon
     */
    async saveDraft(chapterId, data = {}) {
        try {
            const response = await fetch(`${this.baseUrl}/${chapterId}/draft`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            });

            if (!response.ok) throw new Error('Erreur sauvegarde');
            return await response.json();
        } catch (error) {
            console.error('Erreur saveDraft:', error);
            throw error;
        }
    }

    /**
     * Supprime un chapitre
     */
    async deleteChapter(chapterId) {
        try {
            const response = await fetch(`${this.baseUrl}/${chapterId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
            });

            if (!response.ok) throw new Error('Erreur suppression');
            return await response.json();
        } catch (error) {
            console.error('Erreur deleteChapter:', error);
            throw error;
        }
    }

    /**
     * Récupère les langues disponibles
     */
    async getAvailableLanguages() {
        try {
            const response = await fetch(`${this.baseUrl}/languages/available`);
            if (!response.ok) throw new Error('Erreur réseau');
            return await response.json();
        } catch (error) {
            console.error('Erreur getAvailableLanguages:', error);
            throw error;
        }
    }
}

// ============ EXEMPLES D'UTILISATION ============

/*
const api = new ChapterAPIClient();

// 1. Récupérer les chapitres d'un cours
api.getChaptersByCourse(1).then(response => {
    console.log('Chapitres du cours:', response.data);
});

// 2. Enrichir un chapitre avec IA
api.enrichChapter(5).then(response => {
    console.log('Contenu enrichi:', response.data.enrichedContent);
    console.log('Niveau détecté:', response.data.difficultyLevel);
    console.log('Plan généré:', response.data.structuredOutline);
});

// 3. Traduire un chapitre en anglais
api.translateChapter(5, 'en').then(response => {
    console.log('Traduction anglaise:', response.data.translations);
});

// 4. Réorganiser les chapitres (drag & drop)
const newOrder = [
    { chapterId: 3, position: 1 },
    { chapterId: 1, position: 2 },
    { chapterId: 2, position: 3 },
];
api.reorderChapters(1, newOrder).then(response => {
    console.log('Chapitres réorganisés:', response.message);
});

// 5. Publier un chapitre
api.publishChapter(5).then(response => {
    console.log('Chapitre publié:', response.data.status);
});

// 6. Sauvegarder un brouillon
api.saveDraft(5, {
    titre: 'Nouveau titre',
    description: 'Nouvelle description',
}).then(response => {
    console.log('Brouillon sauvegardé');
});

// 7. Supprimer un chapitre
api.deleteChapter(5).then(response => {
    console.log('Chapitre supprimé');
});

// 8. Récupérer les langues disponibles
api.getAvailableLanguages().then(response => {
    console.log('Langues supportées:', response.data);
});
*/

// ============ INTÉGRATION AVEC FORMULAIRE HTML ============

document.addEventListener('DOMContentLoaded', function() {
    const api = new ChapterAPIClient();

    // Bouton enrichir
    document.getElementById('enrichBtn')?.addEventListener('click', async function() {
        const chapterId = this.dataset.chapterId;
        const spinner = this.querySelector('.spinner');
        
        try {
            spinner.style.display = 'inline-block';
            const response = await api.enrichChapter(chapterId);
            
            if (response.success) {
                // Mettre à jour l'affichage
                document.getElementById('enrichedContent').innerHTML = response.data.enrichedContent;
                document.getElementById('difficultyLevel').textContent = response.data.difficultyLevel;
                document.getElementById('structuredOutline').innerHTML = response.data.structuredOutline;
                alert('Chapitre enrichi avec succès!');
            }
        } catch (error) {
            alert('Erreur: ' + error.message);
        } finally {
            spinner.style.display = 'none';
        }
    });

    // Bouton traduire
    document.getElementById('translateBtn')?.addEventListener('click', async function() {
        const chapterId = this.dataset.chapterId;
        const language = document.getElementById('languageSelect').value;
        
        try {
            const response = await api.translateChapter(chapterId, language);
            
            if (response.success) {
                const trans = response.data.translations;
                document.getElementById('translatedTitle').textContent = trans.titre;
                document.getElementById('translatedDescription').innerHTML = trans.description;
                alert('Chapitre traduit en ' + language + '!');
            }
        } catch (error) {
            alert('Erreur de traduction: ' + error.message);
        }
    });

    // Drag & Drop pour réorganiser
    const chapterList = document.getElementById('chapterList');
    if (chapterList) {
        const sortable = new Sortable(chapterList, {
            animation: 150,
            onEnd: async function(evt) {
                const courseId = chapterList.dataset.courseId;
                const chapters = Array.from(chapterList.children);
                const positions = chapters.map((el, idx) => ({
                    chapterId: parseInt(el.dataset.chapterId),
                    position: idx + 1,
                }));

                try {
                    await api.reorderChapters(courseId, positions);
                    console.log('Ordre mis à jour');
                } catch (error) {
                    console.error('Erreur réorganisation:', error);
                }
            },
        });
    }

    // Bouton publier
    document.querySelectorAll('[data-action="publish"]').forEach(btn => {
        btn.addEventListener('click', async function() {
            const chapterId = this.dataset.chapterId;
            try {
                const response = await api.publishChapter(chapterId);
                if (response.success) {
                    this.disabled = true;
                    this.textContent = 'Publié ✓';
                }
            } catch (error) {
                alert('Erreur: ' + error.message);
            }
        });
    });
});
