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
    echo "HTML complet autour de l'image:\n";
    echo substr($html, strpos($html, $matches[0]) - 50, 200) . "\n";
    echo "...\n";
} else {
    echo "Image non trouvée\n";
}

echo "=== Recherche de TEMPLATE RECRÉÉ ===\n";
if (preg_match('/TEMPLATE RECRÉÉ/', $html, $matches)) {
    echo "Template RECRÉÉ trouvé: " . $matches[1] . "\n";
} else {
    echo "Template RECRÉÉ non trouvé\n";
}
?>
