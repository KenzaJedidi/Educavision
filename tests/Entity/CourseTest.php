<?php

namespace App\Tests\Entity;

use App\Entity\Course;
use App\Entity\Chapter;
use PHPUnit\Framework\TestCase;

class CourseTest extends TestCase
{
    private Course $course;

    protected function setUp(): void
    {
        $this->course = new Course();
    }

    public function testCreateCours(): void
    {
        // Test de création d'un cours avec des données valides
        $this->course->setTitre('Test Course');
        $this->course->setDescription('Test description');
        $this->course->setCategory('Développement');
        $this->course->setStatus(1);
        $this->course->setPrice('99.99');

        $this->assertEquals('Test Course', $this->course->getTitre());
        $this->assertEquals('Test description', $this->course->getDescription());
        $this->assertEquals('Développement', $this->course->getCategory());
        $this->assertEquals(1, $this->course->getStatus());
        $this->assertEquals('99.99', $this->course->getPrice());
        $this->assertNotNull($this->course->getCreatedAt());
    }

    public function testUpdateCours(): void
    {
        // Initialisation
        $this->course->setTitre('Original Title');
        $this->course->setDescription('Original description');
        $this->course->setCategory('Design');
        $this->course->setStatus(0);
        $this->course->setPrice('49.99');

        // Mise à jour
        $this->course->setTitre('Updated Title');
        $this->course->setDescription('Updated description');
        $this->course->setCategory('Marketing');
        $this->course->setStatus(1);
        $this->course->setPrice('149.99');

        // Vérification des mises à jour
        $this->assertEquals('Updated Title', $this->course->getTitre());
        $this->assertEquals('Updated description', $this->course->getDescription());
        $this->assertEquals('Marketing', $this->course->getCategory());
        $this->assertEquals(1, $this->course->getStatus());
        $this->assertEquals('149.99', $this->course->getPrice());
    }

    public function testCoursTitreNotEmpty(): void
    {
        // Test avec un titre valide
        $this->course->setTitre('Valid Title');
        $this->assertEquals('Valid Title', $this->course->getTitre());
        $this->assertNotEmpty($this->course->getTitre());

        // Test avec un titre vide
        $this->course->setTitre('');
        $this->assertEquals('', $this->course->getTitre());
        $this->assertEmpty($this->course->getTitre());
    }

    public function testCoursChaptersRelation(): void
    {
        // Test de la relation avec les chapitres
        $chapter1 = new Chapter();
        $chapter1->setTitre('Chapter 1');
        
        $chapter2 = new Chapter();
        $chapter2->setTitre('Chapter 2');

        // Ajout de chapitres
        $this->course->addChapter($chapter1);
        $this->course->addChapter($chapter2);

        // Vérification
        $this->assertCount(2, $this->course->getChapters());
        $this->assertTrue($this->course->getChapters()->contains($chapter1));
        $this->assertTrue($this->course->getChapters()->contains($chapter2));
        $this->assertEquals($this->course, $chapter1->getCourse());
        $this->assertEquals($this->course, $chapter2->getCourse());

        // Suppression d'un chapitre
        $this->course->removeChapter($chapter1);
        $this->assertCount(1, $this->course->getChapters());
        $this->assertFalse($this->course->getChapters()->contains($chapter1));
        $this->assertNull($chapter1->getCourse());
    }

    public function testCoursPopularityScore(): void
    {
        // Test du score de popularité
        $this->course->setViews(100);
        $this->course->setLikes(25);
        $this->course->setCommentsCount(10);

        $score = $this->course->calculatePopularityScore();
        $expectedScore = (100 * 0.5) + (25 * 2) + (10 * 1.5); // 50 + 50 + 15 = 115
        $this->assertEquals((string)$expectedScore, $score);
        $this->assertEquals((string)$expectedScore, $this->course->getPopularityScore());
    }

    public function testCoursFormattedPrice(): void
    {
        // Test du prix formaté
        $this->course->setPrice('99.99');
        $this->assertEquals('99,99 €', $this->course->getFormattedPrice());

        // Test prix gratuit
        $this->course->setPrice('0');
        $this->assertEquals('Gratuit', $this->course->getFormattedPrice());

        // Test prix null
        $this->course->setPrice(null);
        $this->assertEquals('Gratuit', $this->course->getFormattedPrice());
    }

    public function testCoursFormattedStatus(): void
    {
        // Test statut actif
        $this->course->setStatus(1);
        $this->assertEquals('Actif', $this->course->getFormattedStatus());

        // Test statut inactif
        $this->course->setStatus(0);
        $this->assertEquals('Inactif', $this->course->getFormattedStatus());
    }

    public function testCoursKeywordsGeneration(): void
    {
        // Test de génération de mots-clés
        $this->course->setTitre('PHP Development Course');
        $this->course->setDescription('Learn PHP programming and web development');

        $keywords = $this->course->generateKeywords();
        $this->assertIsString($keywords);
        $this->assertNotEmpty($keywords);
        
        // Vérifie que les mots-clés contiennent des termes pertinents
        $this->assertStringContainsStringIgnoringCase('php', $keywords);
        $this->assertStringContainsStringIgnoringCase('development', $keywords);
    }

    public function testCoursTrending(): void
    {
        // Test cours trending (créé il y a moins de 7 jours avec score élevé)
        $this->course->setPopularityScore('75'); // Score > 50
        $this->course->setCreatedAt(new \DateTime('-3 days')); // Créé il y a 3 jours
        
        $this->assertTrue($this->course->isTrending());

        // Test cours non trending (score bas)
        $this->course->setPopularityScore('25'); // Score < 50
        $this->assertFalse($this->course->isTrending());

        // Test cours non trending (trop ancien)
        $this->course->setPopularityScore('75'); // Score élevé
        $this->course->setCreatedAt(new \DateTime('-10 days')); // Créé il y a 10 jours
        $this->assertFalse($this->course->isTrending());
    }
}
