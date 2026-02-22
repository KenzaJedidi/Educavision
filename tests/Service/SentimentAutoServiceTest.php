<?php
namespace App\Tests\Service;

use App\Service\SentimentAutoService;
use PHPUnit\Framework\TestCase;

class SentimentAutoServiceTest extends TestCase
{
    public function testAnalyzeDetailedPositive()
    {
        $s = new SentimentAutoService();
        $r = $s->analyzeDetailed('Merci, service excellent et rapide, très satisfait');
        $this->assertEquals('positif', $r['sentiment']);
        $this->assertGreaterThanOrEqual(0, $r['confidence']);
    }

    public function testAnalyzeDetailedNegative()
    {
        $s = new SentimentAutoService();
        $r = $s->analyzeDetailed('Problème, très mécontent, erreur persistante');
        $this->assertEquals('négatif', $r['sentiment']);
        $this->assertLessThanOrEqual(1, $r['confidence']);
    }
}
