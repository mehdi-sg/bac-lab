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

