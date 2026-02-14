<?php

use App\Entity\Chapitre;
use App\Entity\Matiere;
use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__.'/vendor/autoload.php';

(new Dotenv())->bootEnv(__DIR__.'/.env');

$kernel = new Kernel('dev', true);
$kernel->boot();

$container = $kernel->getContainer();
$entityManager = $container->get('doctrine')->getManager();

// Récupérer la matière avec id=1
$matiere = $entityManager->find(Matiere::class, 1);

if (!$matiere) {
    echo "Erreur: La matière avec id=1 n'existe pas dans la base de données.\n";
    exit(1);
}

echo "Matière trouvée: " . $matiere->getNom() . "\n";

// Chapitres pour la matière
$chapitres = [
    [
        'titre' => 'Introduction aux Nombres',
        'contenu' => 'Ce chapitre couvre les bases des nombres naturels, entiers, rationnels et réels. Vous apprendrez les propriétés fondamentales des nombres et les opérations de base.',
        'ordre' => 1,
    ],
    [
        'titre' => 'Les Opérations Arithmétiques',
        'contenu' => 'Ce chapitre traite des quatre opérations fondamentales: addition, soustraction, multiplication et division.',
        'ordre' => 2,
    ],
    [
        'titre' => 'Les Fractions',
        'contenu' => 'Apprenez à manipuler les fractions: simplification, addition, soustraction, multiplication et division.',
        'ordre' => 3,
    ],
    [
        'titre' => 'Les Pourcentages',
        'contenu' => 'Comprendre le concept de pourcentage, calcul de pourcentages, augmentation et réduction pourcentuelle.',
        'ordre' => 4,
    ],
    [
        'titre' => "Introduction à l'Algèbre",
        'contenu' => "Ce chapitre introduit les variables, expressions algébriques et équations simples.",
        'ordre' => 5,
    ],
    [
        'titre' => 'Les Fonctions',
        'contenu' => 'Étude des fonctions mathématiques, représentation graphique, fonctions linéaires et affines.',
        'ordre' => 6,
    ],
    [
        'titre' => 'Géométrie Plane',
        'contenu' => 'Les figures géométriques planes: triangles, quadrilatères, cercles.',
        'ordre' => 7,
    ],
    [
        'titre' => 'Le Théorème de Pythagore',
        'contenu' => 'Démonstration et application du théorème de Pythagore dans les triangles rectangles.',
        'ordre' => 8,
    ],
    [
        'titre' => 'Les Probabilités',
        'contenu' => 'Introduction aux probabilités: expériences aléatoires, événements.',
        'ordre' => 9,
    ],
    [
        'titre' => 'Statistiques Descriptives',
        'contenu' => 'Les mesures de tendance centrale: moyenne, médiane, mode.',
        'ordre' => 10,
    ],
];

foreach ($chapitres as $data) {
    $chapitre = new Chapitre();
    $chapitre->setTitre($data['titre']);
    $chapitre->setContenu($data['contenu']);
    $chapitre->setOrdre($data['ordre']);
    $chapitre->setActif(true);
    $chapitre->setMatiere($matiere);
    
    $entityManager->persist($chapitre);
}

$entityManager->flush();

echo "10 chapitres ont été créés avec succès pour la matière: " . $matiere->getNom() . "\n";
