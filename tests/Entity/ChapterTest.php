<?php

namespace App\Tests\Entity;

use App\Entity\Chapter;
use App\Entity\Course;
use PHPUnit\Framework\TestCase;

class ChapterTest extends TestCase
{
    private Chapter $chapter;

    protected function setUp(): void
    {
        $this->chapter = new Chapter();
    }

    public function testCreateChapitre(): void
    {
        // Test de création d'un chapitre avec des données valides
        $this->chapter->setTitre('Test Chapter');
        $this->chapter->setDescription('Test chapter description');
        $this->chapter->setOrdre(1);
        $this->chapter->setStatus('published');
        $this->chapter->setDifficultyLevel('débutant');

        $this->assertEquals('Test Chapter', $this->chapter->getTitre());
        $this->assertEquals('Test chapter description', $this->chapter->getDescription());
        $this->assertEquals(1, $this->chapter->getOrdre());
        $this->assertEquals('published', $this->chapter->getStatus());
        $this->assertEquals('débutant', $this->chapter->getDifficultyLevel());
        $this->assertNotNull($this->chapter->getCreatedAt());
    }

    public function testUpdateChapitre(): void
    {
        // Initialisation
        $this->chapter->setTitre('Original Chapter');
        $this->chapter->setDescription('Original description');
        $this->chapter->setOrdre(1);
        $this->chapter->setStatus('draft');
        $this->chapter->setDifficultyLevel('débutant');

        // Mise à jour
        $this->chapter->setTitre('Updated Chapter');
        $this->chapter->setDescription('Updated description');
        $this->chapter->setOrdre(2);
        $this->chapter->setStatus('published');
        $this->chapter->setDifficultyLevel('avancé');

        // Vérification des mises à jour
        $this->assertEquals('Updated Chapter', $this->chapter->getTitre());
        $this->assertEquals('Updated description', $this->chapter->getDescription());
        $this->assertEquals(2, $this->chapter->getOrdre());
        $this->assertEquals('published', $this->chapter->getStatus());
        $this->assertEquals('avancé', $this->chapter->getDifficultyLevel());
    }

    public function testChapitreTitreNotEmpty(): void
    {
        // Test avec un titre valide
        $this->chapter->setTitre('Valid Title');
        $this->assertEquals('Valid Title', $this->chapter->getTitre());
        $this->assertNotEmpty($this->chapter->getTitre());

        // Test avec un titre vide
        $this->chapter->setTitre('');
        $this->assertEquals('', $this->chapter->getTitre());
        $this->assertEmpty($this->chapter->getTitre());
    }

    public function testChapitreCourseRelation(): void
    {
        // Test de la relation avec le cours
        $course = new Course();
        $course->setTitre('Test Course');

        $this->chapter->setCourse($course);

        // Vérification
        $this->assertEquals($course, $this->chapter->getCourse());
        
        // Pour tester la relation bidirectionnelle, on doit ajouter le chapitre au cours
        $course->addChapter($this->chapter);
        $this->assertTrue($course->getChapters()->contains($this->chapter));
    }

    public function testChapitreStatusValidation(): void
    {
        // Test statuts valides
        $this->chapter->setStatus('draft');
        $this->assertEquals('draft', $this->chapter->getStatus());

        $this->chapter->setStatus('published');
        $this->assertEquals('published', $this->chapter->getStatus());

        // Test statut invalide
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid status: invalid');
        $this->chapter->setStatus('invalid');
    }

    public function testChapitreDifficultyLevelValidation(): void
    {
        // Test niveaux de difficulté valides
        $this->chapter->setDifficultyLevel('débutant');
        $this->assertEquals('débutant', $this->chapter->getDifficultyLevel());

        $this->chapter->setDifficultyLevel('intermédiaire');
        $this->assertEquals('intermédiaire', $this->chapter->getDifficultyLevel());

        $this->chapter->setDifficultyLevel('avancé');
        $this->assertEquals('avancé', $this->chapter->getDifficultyLevel());

        // Test niveau invalide
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid difficulty level: invalid');
        $this->chapter->setDifficultyLevel('invalid');
    }

    public function testChapitreTranslations(): void
    {
        // Test des traductions
        $translations = [
            'en' => 'Chapter title in English',
            'es' => 'Título del capítulo en español',
            'de' => 'Kapiteltitel auf Deutsch'
        ];

        $this->chapter->setTranslations($translations);

        // Vérification des traductions
        $this->assertEquals($translations, $this->chapter->getTranslations());
        $this->assertEquals('Chapter title in English', $this->chapter->getTranslation('en'));
        $this->assertEquals('Título del capítulo en español', $this->chapter->getTranslation('es'));
        $this->assertEquals('Kapiteltitel auf Deutsch', $this->chapter->getTranslation('de'));
        $this->assertNull($this->chapter->getTranslation('fr')); // Traduction inexistante

        // Test ajout de traduction
        $this->chapter->addTranslation('fr', 'Titre du chapitre en français');
        $this->assertEquals('Titre du chapitre en français', $this->chapter->getTranslation('fr'));
    }

    public function testChapitrePosition(): void
    {
        // Test de la position
        $this->chapter->setPosition(1);
        $this->assertEquals(1, $this->chapter->getPosition());

        $this->chapter->setPosition(5);
        $this->assertEquals(5, $this->chapter->getPosition());

        // Test position null
        $this->chapter->setPosition(null);
        $this->assertNull($this->chapter->getPosition());
    }

    public function testChapitreEnrichedContent(): void
    {
        // Test du contenu enrichi
        $enrichedContent = 'This is enriched content generated by AI...';
        $this->chapter->setEnrichedContent($enrichedContent);
        $this->assertEquals($enrichedContent, $this->chapter->getEnrichedContent());

        // Test contenu enrichi null
        $this->chapter->setEnrichedContent(null);
        $this->assertNull($this->chapter->getEnrichedContent());
    }

    public function testChapitreStructuredOutline(): void
    {
        // Test du plan structuré
        $outline = '1. Introduction\n2. Main Content\n3. Conclusion';
        $this->chapter->setStructuredOutline($outline);
        $this->assertEquals($outline, $this->chapter->getStructuredOutline());

        // Test plan structuré null
        $this->chapter->setStructuredOutline(null);
        $this->assertNull($this->chapter->getStructuredOutline());
    }

    public function testChapitreUpdatedAt(): void
    {
        // Test de la date de mise à jour
        $updatedAt = new \DateTime('2023-12-25 10:00:00');
        $this->chapter->setUpdatedAt($updatedAt);
        $this->assertEquals($updatedAt, $this->chapter->getUpdatedAt());

        // Test date de mise à jour null
        $this->chapter->setUpdatedAt(null);
        $this->assertNull($this->chapter->getUpdatedAt());
    }

    public function testChapitreTeacherInfo(): void
    {
        // Test informations de l'enseignant
        $this->chapter->setTeacherName('John Doe');
        $this->chapter->setTeacherEmail('john.doe@example.com');

        $this->assertEquals('John Doe', $this->chapter->getTeacherName());
        $this->assertEquals('john.doe@example.com', $this->chapter->getTeacherEmail());

        // Test null
        $this->chapter->setTeacherName(null);
        $this->chapter->setTeacherEmail(null);
        $this->assertNull($this->chapter->getTeacherName());
        $this->assertNull($this->chapter->getTeacherEmail());
    }
}
