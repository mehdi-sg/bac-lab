<?php
// test_env.php - Placez à la racine du projet (C:\Users\Msi\bac-lab\test_env.php)

require 'vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

echo "=== TEST VARIABLES D'ENVIRONNEMENT ===\n\n";

// Forcer le chargement explicite des fichiers .env
$dotenv = new Dotenv();
try {
    $dotenv->loadEnv('.env');
    echo "✅ Fichier .env chargé avec loadEnv()\n";
} catch (\Exception $e) {
    echo "❌ Erreur chargement .env: " . $e->getMessage() . "\n";
}

// Essayer aussi .env.local explicitement
if (file_exists('.env.local')) {
    try {
        $dotenv->load('.env.local');
        echo "✅ Fichier .env.local chargé avec load()\n";
    } catch (\Exception $e) {
        echo "❌ Erreur chargement .env.local: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Fichier .env.local n'existe pas à: " . getcwd() . "/.env.local\n";
}

echo "\n--- Après chargement ---\n";
echo "1. GEMINI_API_KEY dans \$_ENV : " . ($_ENV['GEMINI_API_KEY'] ?? 'NON TROUVÉ') . "\n";
echo "2. APP_ENV : " . ($_ENV['APP_ENV'] ?? 'NON TROUVÉ') . "\n";

echo "\n5. Fichiers .env présents dans " . getcwd() . " :\n";
$envFiles = ['.env', '.env.local', '.env.dev', '.env.prod'];
foreach ($envFiles as $file) {
    $path = getcwd() . '/' . $file;
    $exists = file_exists($path) ? '✅ EXISTE' : '❌ ABSENT';
    $size = file_exists($path) ? filesize($path) . ' octets' : '';
    echo "   - $file : $exists $size\n";
}

echo "\n6. Contenu de .env.local :\n";
if (file_exists('.env.local')) {
    $content = file_get_contents('.env.local');
    echo "   (Taille: " . strlen($content) . " caractères)\n";
    $lines = explode("\n", $content);
    foreach ($lines as $num => $line) {
        if (trim($line) !== '' && trim($line)[0] !== '#') {
            echo "   Ligne " . ($num+1) . ": " . substr($line, 0, 50) . (strlen($line) > 50 ? '...' : '') . "\n";
        }
    }
}

echo "\n=== FIN DU TEST ===\n";