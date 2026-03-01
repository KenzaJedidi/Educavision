<?php

// Simulation de connexion et récupération du formulaire
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'email=admin&password=admin&login=Login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
$response = curl_exec($ch);
curl_close($ch);

// Extraction du cookie de session
preg_match('/PHPSESSID=([^;]+)/', $response, $matches);
$sessionId = $matches[1] ?? '';

// Accès à la page d'édition avec le cookie
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/admin/course/15/edit');
curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID=$sessionId");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$html = curl_exec($ch);
curl_close($ch);

// Recherche de l'image dans le HTML
echo "=== Recherche de l'image ===\n";
if (preg_match('/<img[^>]*src="([^"]*)"[^>]*alt="cyber"/', $html, $matches)) {
    echo "Image trouvée: " . $matches[1] . "\n";
} else {
    echo "Image non trouvée avec la première regex\n";
}

// Recherche avec une regex plus large
if (preg_match('/<img[^>]*src="([^"]*)"/', $html, $matches)) {
    echo "Image trouvée (regex large): " . $matches[1] . "\n";
} else {
    echo "Image non trouvée avec la regex large\n";
}

// Recherche de asset() avec imageUrl
if (preg_match('/asset\(.*?[^)]*imageUrl[^)]*\)/', $html, $matches)) {
    echo "Asset(imageUrl) trouvé: " . $matches[1] . "\n";
} else {
    echo "Asset(imageUrl) non trouvé\n";
}

// Affichage du contenu HTML autour de l'image
if (preg_match('/(<img[^>]*src="[^"]*"[^>]*alt="cyber"[^>]*>)/', $html, $matches)) {
    echo "HTML autour de l'image:\n";
    echo $matches[1] . "\n";
}
?>
