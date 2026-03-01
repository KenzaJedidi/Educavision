<?php

// Connexion à la base de données
$pdo = new PDO('mysql:host=127.0.0.1;dbname=educavision', 'root', '');

// Récupération du cours cyber (ID: 15)
$stmt = $pdo->prepare("SELECT id, titre, image_url, status FROM course WHERE id = 15");
$stmt->execute();
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if ($course) {
    echo "Cours trouvé:\n";
    echo "ID: " . $course['id'] . "\n";
    echo "Titre: " . $course['titre'] . "\n";
    echo "Image URL: " . $course['image_url'] . "\n";
    echo "Status: " . $course['status'] . "\n";
    
    // Vérification si le champ est vide ou null
    if (empty($course['image_url']) || is_null($course['image_url'])) {
        echo "Le champ image_url est vide ou null\n";
    } else {
        echo "Le champ image_url a une valeur: " . $course['image_url'] . "\n";
    }
} else {
    echo "Cours non trouvé\n";
}
?>
