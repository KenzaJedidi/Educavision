<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

// Initialiser la base de données pour les tests
if ($_SERVER['APP_ENV'] === 'test') {
    // Créer le schéma de la base de données pour les tests
    $kernel = new \App\Kernel('test', true);
    $kernel->boot();
    
    $entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');
    
    try {
        // Créer le schéma
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->createSchema($metadata);
    } catch (\Exception $e) {
        // Le schéma existe déjà, ignorer l'erreur
    }
    
    $kernel->shutdown();
}
