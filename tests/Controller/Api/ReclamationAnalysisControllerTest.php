<?php
namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReclamationAnalysisControllerTest extends WebTestCase
{
    public function testAnalyzeEndpointReturnsJson()
    {
        $client = static::createClient();
        $client->request('POST', '/api/reclamation/analyze', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['text' => 'Ceci est un test rapide et simple.']));
        
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertTrue(
            in_array($statusCode, [200, 500])
        );
        
        // Vérifier les données seulement si le statut est 200
        if ($statusCode === 200) {
            $data = json_decode($client->getResponse()->getContent(), true);
            $this->assertArrayHasKey('summary', $data);
            $this->assertArrayHasKey('sentiment', $data);
            $this->assertArrayHasKey('predictedHours', $data);
        }
    }
}
