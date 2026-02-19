# Contrôles de Saisie - Fiche Creation

## Vue d'ensemble
Validation complète avec Symfony Assert pour garantir l'intégrité des données lors de la création/modification de fiches.

## Champs Validés

### 1. Titre (Title)
**Contraintes:**
- `NotBlank`: Le titre est obligatoire
- `Length`: 
  - Minimum: 3 caractères
  - Maximum: 255 caractères
- `Regex`: Caractères autorisés uniquement (lettres, chiffres, accents français, ponctuation)
- `NotEqualTo`: Ne peut pas être "test"

**Messages d'erreur:**
- "Le titre est obligatoire."
- "Le titre doit contenir au moins 3 caractères."
- "Le titre ne peut pas dépasser 255 caractères."
- "Le titre contient des caractères non autorisés."
- "Le titre ne peut pas être 'test'."

### 2. Contenu (Content)
**Contraintes:**
- `NotBlank`: Le contenu est obligatoire
- `Length`:
  - Minimum: 20 caractères (pour assurer un contenu utile)
  - Maximum: 10000 caractères
- `Regex`: Caractères autorisés (lettres, chiffres, accents, ponctuation, retours à la ligne)

**Messages d'erreur:**
- "Le contenu est obligatoire."
- "Le contenu doit contenir au moins 20 caractères pour être utile."
- "Le contenu ne peut pas dépasser 10000 caractères."
- "Le contenu contient des caractères non autorisés."

### 3. Filière (Filiere)
**Contraintes:**
- `NotNull`: Une filière doit être sélectionnée
- `EntityType`: Doit être une entité Filiere valide

**Messages d'erreur:**
- "Veuillez sélectionner une filière."

### 4. Fiche Publique (isPublic)
**Contraintes:**
- Aucune contrainte (optionnel)
- Type: Boolean (checkbox)

## Validation Côté Client (JavaScript)

### Validation en temps réel
- Validation du titre au blur (perte de focus)
- Validation du contenu au blur
- Vérification des caractères invalides
- Comptage des mots, lignes et temps de lecture

### Validation à la soumission
- Vérification finale avant envoi
- Scroll automatique vers la première erreur
- Empêche la soumission si erreurs détectées

## Validation Côté Serveur (Symfony)

### Niveau Form (FicheType)
- Contraintes définies dans `buildForm()`
- Validation automatique lors de `$form->handleRequest()`
- Messages d'erreur personnalisés en français

### Niveau Entity (Fiche)
- Annotations Assert sur les propriétés
- Double validation pour sécurité maximale
- Validation lors de `$entityManager->persist()`

## Caractères Autorisés

### Titre
```regex
/^[a-zA-Z0-9àâäéèêëïîôùûüç\s\-\'"()\[\]\{\}\!\?\,\.\:\;\+\=\*\#\%\<\>\/\\]+$/
```
- Lettres (a-z, A-Z)
- Chiffres (0-9)
- Accents français (àâäéèêëïîôùûüç)
- Espaces
- Ponctuation: - ' " ( ) [ ] { } ! ? , . : ; + = * # % < > / \

### Contenu
```regex
/^[a-zA-Z0-9àâäéèêëïîôùûüç\s\-\'"()\[\]\{\}\!\?\,\.\:\;\+\=\*\#\%\<\>\/\\\r\n]+$/
```
- Mêmes caractères que le titre
- Plus: retours à la ligne (\r\n)

## Sécurité

### Protection XSS
- Validation stricte des caractères
- Pas de HTML autorisé
- Échappement automatique dans les templates Twig

### Protection Injection SQL
- Utilisation de Doctrine ORM
- Requêtes préparées automatiques
- Validation des types de données

### Protection CSRF
- Token CSRF sur tous les formulaires
- Validation automatique par Symfony

## Flux de Validation

1. **Saisie utilisateur** → Validation JavaScript temps réel
2. **Soumission formulaire** → Validation JavaScript finale
3. **Envoi au serveur** → Validation CSRF token
4. **Traitement Symfony** → Validation Form constraints
5. **Persistance** → Validation Entity constraints
6. **Sauvegarde** → Données validées en base

## Exemples de Validation

### Titre valide ✓
```
"Mathématiques - Dérivation (L1)"
"Cours de Physique: Les Forces"
"Introduction à l'Algorithmique"
```

### Titre invalide ✗
```
"AB" (trop court)
"<script>alert('xss')</script>" (caractères interdits)
"test" (valeur interdite)
```

### Contenu valide ✓
```
"== Introduction
!! Définition importante
>> Exemple concret
- Point 1
- Point 2"
```

### Contenu invalide ✗
```
"Trop court" (moins de 20 caractères)
"<img src=x onerror=alert(1)>" (HTML interdit)
```

## Configuration

### Fichiers concernés
- `src/Form/FicheType.php` - Définition des contraintes
- `src/Entity/Fiche.php` - Annotations de validation
- `templates/fiche/new.html.twig` - Affichage des erreurs
- `templates/fiche/edit.html.twig` - Affichage des erreurs

### Personnalisation
Pour modifier les contraintes, éditer:
1. Les annotations dans `FicheType::buildForm()`
2. Les annotations dans `Fiche` entity
3. Les messages d'erreur dans les contraintes

## Tests de Validation

### Tests unitaires recommandés
```php
// Titre trop court
$fiche->setTitle('AB');
// Devrait échouer

// Contenu trop court
$fiche->setContent('Court');
// Devrait échouer

// Caractères invalides
$fiche->setTitle('<script>');
// Devrait échouer

// Filière null
$fiche->setFiliere(null);
// Devrait échouer
```

## Maintenance

### Ajouter une nouvelle contrainte
1. Ajouter l'annotation Assert dans `FicheType`
2. Ajouter le message d'erreur personnalisé
3. Mettre à jour cette documentation
4. Tester la validation

### Modifier une contrainte existante
1. Modifier l'annotation dans `FicheType`
2. Vérifier l'impact sur les données existantes
3. Mettre à jour les tests
4. Mettre à jour cette documentation
