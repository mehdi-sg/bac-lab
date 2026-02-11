# Fiche Filter Implementation TODO

## Steps to Complete:

### 1. Create FicheFavori Entity
- [x] Create `src/Entity/FicheFavori.php` for many-to-many relationship between User and Fiche
- [x] Create `src/Repository/FicheFavoriRepository.php`
- [x] Update `src/Entity/Fiche.php` to add favoris relationship

### 2. Update FicheRepository
- [x] Add `findByFilters()` method for combined filtering
- [x] Add `findByFiliere()` method
- [x] Add `findByOwner()` method  
- [x] Add `findFavoritesByUser()` method

### 3. Update FicheController
- [x] Modify `index()` action to accept filter parameters
- [x] Add `toggleFavorite()` action for adding/removing favorites
- [x] Update `show()` action to check favorite status
- [x] Pass filieres list to template
- [x] Pass current filter state to template

### 4. Update Templates
- [x] Add filter UI section with tabs/buttons in `index.html.twig`
- [x] Add filiere dropdown selector
- [x] Add favorite buttons to cards
- [x] Add favorite badge and button in `show.html.twig`

### 5. Update CSS
- [x] Add filter section styling in `fiche.css`
- [x] Add favorite button styles
- [x] Add favorite badge styles

### 6. Create Migration
- [x] Generate migration for fiche_favoris table (run: `php bin/console make:migration`)
- [x] Run migration to create database table (run: `php bin/console doctrine:migrations:migrate`)


## Summary of Changes:

### New Files Created:
1. `src/Entity/FicheFavori.php` - Entity for favorite fiches
2. `src/Repository/FicheFavoriRepository.php` - Repository for favorites

### Files Modified:
1. `src/Entity/Fiche.php` - Added favoris relationship
2. `src/Repository/FicheRepository.php` - Added filter methods
3. `src/Controller/FicheController.php` - Added filter logic and toggleFavorite action
4. `templates/fiche/index.html.twig` - Added filter UI and favorite buttons
5. `templates/fiche/show.html.twig` - Added favorite button and badge
6. `public/front/css/fiche.css` - Added filter and favorite styles

## Filter Features Implemented:
1. **Filter by Type:**
   - "Toutes" (All) - Shows all fiches
   - "Mes fiches" (Own) - Shows fiches where user is owner
   - "Favorites" - Shows user's favorite fiches

2. **Filter by Filiere:**
   - Dropdown selector to filter by field of study
   - Works in combination with type filter

3. **Favorite Functionality:**
   - Heart button on each fiche card to add/remove favorite
   - Favorite button on fiche show page
# TODO - Question Form Validation Sans JavaScript

## Objectif
Modifier `question_form.html.twig` pour effectuer le contrôle de saisie sans JavaScript et sans attribut `required`.

## Étapes

- [x] 1. Supprimer le bloc JavaScript (`{% block js %}`)
- [x] 2. Supprimer les attributs `required` des champs de formulaire
- [x] 3. Remplacer les boutons JavaScript par des boutons submit avec `name="action" value="add_choice"`
- [x] 4. Générer les choix dynamiquement via Twig (basé sur `choiceCount`)
- [x] 5. Charger les chapitres via Twig (basé sur `chapitres` variable)
- [x] 6. Afficher les erreurs de validation (boucle sur `errors`)
- [x] 7. Repopuler les champs avec les données de `old`
- [x] 8. Ajouter un indicateur visuel pour les champs erronés (style CSS)

## Notes
- Le contrôleur est déjà prêt à gérer les actions sans JS
- Les variables disponibles: `choiceCount`, `chapitres`, `errors`, `old`, `matieres`

## Modifications effectuées

### Template (`templates/Quiz/question_form.html.twig`):
- ✅ Suppression complète du bloc `{% block js %}` (sauf petit JS pour highlight VRAI/FAUX)
- ✅ Suppression des attributs `required` sur tous les champs
- ✅ Bouton "Ajouter un choix" → `<button type="submit" name="action" value="add_choice">`
- ✅ Bouton "Rafraîchir les chapitres" → `<button type="submit" name="action" value="refresh_chapitres">`
- ✅ Génération des choix via boucle Twig `{% for i in 0..(count - 1) %}`
- ✅ Affichage des erreurs dans une alerte rouge `{% if errors is not empty %}`
- ✅ Repopulation des champs avec `{{ old.enonce ?? ... }}`
- ✅ Style CSS ajouté pour les champs avec erreur (bordure rouge)
- ✅ Support VRAI_FAUX avec boutons radio
- ✅ Support QCM avec checkboxes et ajout/suppression de choix

### Contrôleur (`src/Controller/QuestionAdminController.php`):
- ✅ Route `admin_question_new` pour créer une question
- ✅ Route `admin_question_edit` pour modifier une question existante
- ✅ Route `admin_question_delete` pour supprimer une question
- ✅ Validation serveur sans JavaScript
- ✅ Gestion des erreurs et affichage des messages

### Template (`templates/Quiz/questions.html.twig`):
- ✅ Bouton "Modifier" ajouté à chaque question
- ✅ Lien vers `admin_question_edit`

