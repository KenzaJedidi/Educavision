<?php
namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReclamationAnalysisControllerTest extends WebTestCase
{
    public function testAnalyzeEndpointReturnsJson()
    {
        $client = static::createClient();
        $client->request('POST', '/api/reclamation/analyze', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['text' => 'Ceci est un test rapide et simple.']));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('summary', $data);
        $this->assertArrayHasKey('sentiment', $data);
        $this->assertArrayHasKey('predictedHours', $data);
    }
}
