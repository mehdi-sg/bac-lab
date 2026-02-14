<?php

namespace App\DataFixtures;

use App\Entity\Chapitre;
use App\Entity\Matiere;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ChapitreFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Récupérer la matière avec id=1
        $matiere = $manager->find(Matiere::class, 1);
        
        if (!$matiere) {
            throw new \LogicException('La matière avec id=1 n\'existe pas dans la base de données.');
        }

        // Chapitres pour la matière Mathématiques (id=1)
        $chapitres = [
            [
                'titre' => 'Introduction aux Nombres',
                'contenu' => 'Ce chapitre couvre les bases des nombres naturels, entiers, rationnels et réels. Vous apprendrez les propriétés fondamentales des nombres et les opérations de base.',
                'ordre' => 1,
            ],
            [
                'titre' => 'Les Opérations Arithmétiques',
                'contenu' => 'Ce chapitre traite des quatre opérations fondamentales: addition, soustraction, multiplication et division. Includes les propriétés commutatives, associatives et distributives.',
                'ordre' => 2,
            ],
            [
                'titre' => 'Les Fractions',
                'contenu' => 'Apprenez à manipuler les fractions: simplification, addition, soustraction, multiplication et division. Conversion entre fractions et nombres décimaux.',
                'ordre' => 3,
            ],
            [
                'titre' => 'Les Pourcentages',
                'contenu' => 'Comprendre le concept de pourcentage, calcul de pourcentages, augmentation et réduction pourcentuelle, et applications pratiques.',
                'ordre' => 4,
            ],
            [
                'titre' => 'Introduction à l\'Algèbre',
                'contenu' => 'Ce chapitre introduit les variables, expressions algébriques et équations simples. Résolution d\'équations du premier degré.',
                'ordre' => 5,
            ],
            [
                'titre' => 'Les Fonctions',
                'contenu' => 'Étude des fonctions mathématiques, représentation graphique, fonctions linéaires et affines, et applications.',
                'ordre' => 6,
            ],
            [
                'titre' => 'Géométrie Plane',
                'contenu' => 'Les figures géométriques planes: triangles, quadrilatères, cercles. Calcul de périmètres et d\'aires.',
                'ordre' => 7,
            ],
            [
                'titre' => 'Le Théorème de Pythagore',
                'contenu' => 'Démonstration et application du théorème de Pythagore dans les triangles rectangles. Applications pratiques.',
                'ordre' => 8,
            ],
            [
                'titre' => 'Les Probabilités',
                'contenu' => 'Introduction aux probabilités: expériences aléatoires, événements, calcul de probabilités simples.',
                'ordre' => 9,
            ],
            [
                'titre' => 'Statistiques Descriptives',
                'contenu' => 'Les mesures de tendance centrale: moyenne, médiane, mode. Représentations graphiques des données.',
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
            
            $manager->persist($chapitre);
        }

        $manager->flush();
    }
}
