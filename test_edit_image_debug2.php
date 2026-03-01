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

// Recherche de la section "Image actuelle"
echo "=== Recherche de la section Image actuelle ===\n";
if (preg_match('/<!-- Image actuelle -->(.*?)<!-- \/Image actuelle -->/s', $html, $matches)) {
    echo "Section Image actuelle trouvée:\n";
    echo $matches[1] . "\n";
    
    // Recherche de l'image dans cette section
    if (preg_match('/<img[^>]*src="([^"]*)"/', $matches[1], $imgMatches)) {
        echo "Image trouvée dans la section: " . $imgMatches[1] . "\n";
    } else {
        echo "Image non trouvée dans la section\n";
    }
} else {
    echo "Section Image actuelle non trouvée\n";
}

// Affichage des 50 premiers caractères avant et après la section Image
$pos = strpos($html, '<!-- Image actuelle -->');
if ($pos !== false) {
    echo "HTML avant la section Image actuelle:\n";
    echo substr($html, max(0, $pos - 50), 50) . "...\n";
    echo "HTML après la section Image actuelle:\n";
    echo substr($html, $pos, 50) . "\n";
} else {
    echo "Balise <!-- Image actuelle --> non trouvée\n";
}
?>
