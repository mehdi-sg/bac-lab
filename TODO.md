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
