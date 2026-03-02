<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as BaseKernelTestCase;
use Doctrine\ORM\Tools\SchemaTool;

abstract class KernelTestCase extends BaseKernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialiser la base de données pour les tests
        $kernel = self::bootKernel();
        
        $entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        
        try {
            // Créer le schéma
            $schemaTool = new SchemaTool($entityManager);
            $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
            $schemaTool->createSchema($metadata);
        } catch (\Exception $e) {
            // Le schéma existe déjà, ignorer l'erreur
        }
    }
}
