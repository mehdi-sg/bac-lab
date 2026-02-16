/* ============================================================
   seed_bac_tn.sql  — BacLab (Tunisie) seed data
   Compatible: MySQL / MariaDB
   Assumes schema already created via Doctrine migrations.
   ============================================================ */

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

/* --------------------------
   CLEAN (TRUNCATE)
   -------------------------- */
TRUNCATE TABLE notifications;
TRUNCATE TABLE message;
TRUNCATE TABLE membre_groupe;
TRUNCATE TABLE groupe;

TRUNCATE TABLE fiche_favoris;
TRUNCATE TABLE fiche_join_requests;
TRUNCATE TABLE fiche_moderateurs;
TRUNCATE TABLE fiche_version;
TRUNCATE TABLE fiche;

TRUNCATE TABLE evaluation_ressource;
TRUNCATE TABLE ressource;

TRUNCATE TABLE choix;
TRUNCATE TABLE question;
TRUNCATE TABLE quiz;
TRUNCATE TABLE chapitre;
TRUNCATE TABLE matiere;
TRUNCATE TABLE filiere;

TRUNCATE TABLE profil;
TRUNCATE TABLE utilisateur;

/* --------------------------
   USERS + PROFILES
   -------------------------- */
/*
  Replace password hashes with:
  php bin/console security:hash-password 'Password123!'
*/
INSERT INTO utilisateur (id, email, roles, password, is_active, created_at) VALUES
(1, 'admin@baclab.tn',      '["ROLE_ADMIN"]',     '$2y$13$REPLACE_WITH_HASH_ADMIN.........................', 1, NOW()),
(2, 'moderateur@baclab.tn', '["ROLE_MODERATOR"]', '$2y$13$REPLACE_WITH_HASH_MOD...........................', 1, NOW()),
(3, 'eleve.science@baclab.tn','["ROLE_USER"]',    '$2y$13$REPLACE_WITH_HASH_USER..........................', 1, NOW()),
(4, 'eleve.math@baclab.tn', '["ROLE_USER"]',      '$2y$13$REPLACE_WITH_HASH_USER2.........................', 1, NOW());

INSERT INTO profil (id, nom, prenom, niveau, gouvernorat, date_naissance, filiere_id, utilisateur_id) VALUES
(1, 'Ben Salem', 'Amine',  '4ème année', 'Tunis',    '2007-03-14', NULL, 1),
(2, 'Kchaou',    'Sara',   'M1',         'Sfax',     '2002-10-02', NULL, 2),
(3, 'Trabelsi',  'Yassine','4ème année', 'Ariana',   '2007-07-21', 1,    3),
(4, 'Gharbi',    'Rim',    '4ème année', 'Sousse',   '2007-12-05', 2,    4);

/* --------------------------
   FILIERES (Bac TN)
   -------------------------- */
INSERT INTO filiere (id, nom, niveau, actif, created_at, updated_at) VALUES
(1, 'Sciences Expérimentales', 'Bac', 1, NOW(), NULL),
(2, 'Mathématiques',           'Bac', 1, NOW(), NULL),
(3, 'Sciences de l’Informatique','Bac',1, NOW(), NULL),
(4, 'Économie & Gestion',      'Bac', 1, NOW(), NULL),
(5, 'Lettres',                 'Bac', 1, NOW(), NULL),
(6, 'Technique',               'Bac', 1, NOW(), NULL),
(7, 'Sport',                   'Bac', 1, NOW(), NULL);

UPDATE profil SET filiere_id = 1 WHERE id = 3;
UPDATE profil SET filiere_id = 2 WHERE id = 4;

/* --------------------------
   MATIERES (subset realistic)
   -------------------------- */
INSERT INTO matiere (id, nom, filiere_id, actif, created_at, updated_at) VALUES
(1,  'Mathématiques',            1, 1, NOW(), NULL),
(2,  'Sciences de la Vie et de la Terre (SVT)', 1, 1, NOW(), NULL),
(3,  'Physique-Chimie',          1, 1, NOW(), NULL),

(4,  'Mathématiques',            2, 1, NOW(), NULL),
(5,  'Physique',                 2, 1, NOW(), NULL),
(6,  'Informatique',             2, 1, NOW(), NULL),

(7,  'Algorithmique',            3, 1, NOW(), NULL),
(8,  'Bases de données',         3, 1, NOW(), NULL),
(9,  'Mathématiques',            3, 1, NOW(), NULL),

(10, 'Économie',                 4, 1, NOW(), NULL),
(11, 'Gestion',                  4, 1, NOW(), NULL),

(12, 'Arabe',                    5, 1, NOW(), NULL),
(13, 'Français',                 5, 1, NOW(), NULL),
(14, 'Philosophie',              5, 1, NOW(), NULL);

/* --------------------------
   CHAPITRES
   -------------------------- */
INSERT INTO chapitre (id, titre, contenu, actif, ordre, created_at, updated_at, matiere_id) VALUES
-- Sciences Exp
(1, 'Fonctions et dérivation', 'Rappels + dérivées + étude de fonctions (niveau Bac).', 1, 1, NOW(), NULL, 1),
(2, 'Probabilités', 'Variables aléatoires, lois usuelles, espérance, exercices type Bac.', 1, 2, NOW(), NULL, 1),
(3, 'Génétique', 'Transmission des caractères, ADN, exercices type Bac Sciences.', 1, 1, NOW(), NULL, 2),
(4, 'Immunologie', 'Réponse immunitaire, vaccination, schémas et QCM.', 1, 2, NOW(), NULL, 2),
(5, 'Électricité (RC/RL)', 'Régimes transitoires, méthodes, applications.', 1, 1, NOW(), NULL, 3),

-- Maths
(6, 'Suites', 'Suites arithmétiques/géométriques, récurrence, limites.', 1, 1, NOW(), NULL, 4),
(7, 'Intégration', 'Primitives, intégrales, aires, techniques.', 1, 2, NOW(), NULL, 4),
(8, 'Mécanique', 'Travail-énergie, dynamique, exercices type Bac Maths.', 1, 1, NOW(), NULL, 5),
(9, 'Bases de l’algorithmique', 'Boucles, tableaux, complexité intuitive.', 1, 1, NOW(), NULL, 6),

-- Info
(10,'Structures conditionnelles', 'If/else, cas, tests, exercices.', 1, 1, NOW(), NULL, 7),
(11,'SQL de base', 'SELECT, WHERE, JOIN (intro), exercices Bac Info.', 1, 1, NOW(), NULL, 8),
(12,'Matrices', 'Opérations, déterminant (selon programme), applications.', 1, 1, NOW(), NULL, 9),

-- Eco/Gestion
(13,'Offre & demande', 'Équilibre, élasticité (intro), exercices.', 1, 1, NOW(), NULL, 10),
(14,'Comptabilité', 'Bilan, compte de résultat, notions clés.', 1, 1, NOW(), NULL, 11),

-- Lettres
(15,'Expression écrite', 'Méthodes, connecteurs, sujets Bac (exemples).', 1, 1, NOW(), NULL, 13),
(16,'Notions de philosophie', 'Liberté, justice, vérité — plans types.', 1, 1, NOW(), NULL, 14);

/* --------------------------
   QUIZ
   (table quiz has id_quiz + join columns id_chapitre / id_matiere)
   -------------------------- */
INSERT INTO quiz (id_quiz, titre, description, niveau, duree, nb_questions, date_creation, etat, id_chapitre, id_matiere) VALUES
(1, 'QCM — Dérivation (Bac Sciences)', 'Quiz rapide sur dérivées et étude de fonctions.', 'Bac', 15, 6, NOW(), 1, 1, 1),
(2, 'QCM — Probabilités', 'Lois, arbres, calculs classiques Bac.', 'Bac', 20, 6, NOW(), 1, 2, 1),
(3, 'QCM — Génétique', 'Hérédité, ADN, QCM + vrai/faux.', 'Bac', 18, 6, NOW(), 1, 3, 2),
(4, 'QCM — SQL', 'SELECT / WHERE / JOIN (intro) Bac Info.', 'Bac', 18, 6, NOW(), 1, 11, 8),
(5, 'QCM — Philosophie (notions)', 'Définitions, exemples, repères.', 'Bac', 12, 6, NOW(), 1, 16, 14);

/* --------------------------
   QUESTIONS + CHOIX
   (question.id_question, choix.id_choix)
   -------------------------- */
INSERT INTO question (id_question, enonce, type_question, score, id_quiz) VALUES
(1, 'Soit f(x)=x². f''(2)= ?', 'QCM', 1, 1),
(2, 'Une fonction dérivable est forcément continue.', 'VRAI_FAUX', 1, 1),
(3, 'La dérivée de sin(x) est :', 'QCM', 1, 1),
(4, 'L’équation f''(x)=0 aide à trouver :', 'QCM', 1, 1),
(5, 'La dérivée de ln(x) (x>0) est :', 'QCM', 1, 1),
(6, 'Dans un tirage sans remise, la probabilité change à chaque tirage.', 'VRAI_FAUX', 1, 2),
(7, 'P(A∩B)= ?', 'QCM', 1, 2),
(8, 'Une loi binomiale modélise :', 'QCM', 1, 2),
(9, 'E(X) pour X~B(n,p) vaut :', 'QCM', 1, 2),
(10,'P(A∪B)= ?', 'QCM', 1, 2),

(11,'L’ADN est constitué de :', 'QCM', 1, 3),
(12,'La méiose produit des gamètes haploïdes.', 'VRAI_FAUX', 1, 3),
(13,'Le croisement Aa x Aa donne un ratio génotypique :', 'QCM', 1, 3),
(14,'Une mutation peut être :', 'QCM', 1, 3),
(15,'Le code génétique est :', 'QCM', 1, 3),

(16,'Requête pour sélectionner tout depuis "ressource" :', 'QCM', 1, 4),
(17,'Clause pour filtrer :', 'QCM', 1, 4),
(18,'JOIN sert à :', 'QCM', 1, 4),
(19,'SELECT ... WHERE ...', 'VRAI_FAUX', 1, 4),
(20,'Une clé primaire est :', 'QCM', 1, 4),

(21,'La liberté est souvent opposée à :', 'QCM', 1, 5),
(22,'Dire vrai, c’est :', 'QCM', 1, 5),
(23,'La justice concerne :', 'QCM', 1, 5),
(24,'Un argument est :', 'QCM', 1, 5),
(25,'Philosophie: on peut discuter rationnellement.', 'VRAI_FAUX', 1, 5);

INSERT INTO choix (id_choix, libelle, est_correct, id_question) VALUES
-- Quiz 1
(1, '2', 0, 1), (2, '4', 1, 1), (3, '8', 0, 1), (4, '16', 0, 1),
(5, 'Vrai', 1, 2), (6, 'Faux', 0, 2),
(7, 'cos(x)', 1, 3), (8, '-cos(x)', 0, 3), (9, 'sin(x)', 0, 3), (10, '-sin(x)', 0, 3),
(11, 'Les zéros de la fonction', 0, 4), (12, 'Les extremums', 1, 4), (13, 'Les asymptotes', 0, 4), (14, 'Les racines carrées', 0, 4),
(15, '1/x', 1, 5), (16, 'ln(x)', 0, 5), (17, 'x', 0, 5), (18, 'e^x', 0, 5),

-- Quiz 2
(19, 'Vrai', 1, 6), (20, 'Faux', 0, 6),
(21, 'P(A)×P(B)', 0, 7), (22, 'P(A) + P(B)', 0, 7), (23, 'P(B)×P(A|B)', 1, 7), (24, '1 - P(A)', 0, 7),
(25, 'Une suite géométrique', 0, 8), (26, 'n essais indépendants avec 2 issues', 1, 8), (27, 'Une loi continue', 0, 8), (28, 'Une permutation', 0, 8),
(29, 'n+p', 0, 9), (30, 'n×p', 1, 9), (31, 'p/n', 0, 9), (32, 'n²', 0, 9),
(33, 'P(A)+P(B)', 0,10), (34, 'P(A)+P(B)-P(A∩B)', 1,10), (35, 'P(A)×P(B)', 0,10), (36, '1-P(A∩B)', 0,10),

-- Quiz 3
(37, 'Acides aminés', 0,11), (38, 'Nucléotides', 1,11), (39, 'Lipides', 0,11), (40, 'Glucides', 0,11),
(41, 'Vrai', 1,12), (42, 'Faux', 0,12),
(43, '1:2:1', 1,13), (44, '3:1', 0,13), (45, '1:1', 0,13), (46, '2:1', 0,13),
(47, 'Toujours bénéfique', 0,14), (48, 'Neutre, bénéfique ou délétère', 1,14), (49, 'Impossible', 0,14), (50, 'Seulement délétère', 0,14),
(51, 'Universel et dégénéré', 1,15), (52, 'Unique et non dégénéré', 0,15), (53, 'Aléatoire', 0,15), (54, 'Inexistant', 0,15),

-- Quiz 4
(55, 'GET * FROM ressource;', 0,16), (56, 'SELECT * FROM ressource;', 1,16), (57, 'PULL ressource;', 0,16), (58, 'SHOW ressource;', 0,16),
(59, 'ORDER BY', 0,17), (60, 'WHERE', 1,17), (61, 'GROUP BY', 0,17), (62, 'LIMIT', 0,17),
(63, 'Créer une table', 0,18), (64, 'Relier des tables', 1,18), (65, 'Supprimer une base', 0,18), (66, 'Compiler du code', 0,18),
(67, 'Vrai', 1,19), (68, 'Faux', 0,19),
(69, 'Un index obligatoire', 0,20), (70, 'Un identifiant unique d’une ligne', 1,20), (71, 'Un champ texte', 0,20), (72, 'Un lien web', 0,20),

-- Quiz 5
(73, 'La contrainte', 1,21), (74, 'La météo', 0,21), (75, 'La géométrie', 0,21), (76, 'La chimie', 0,21),
(77, 'Dire ce qui plaît', 0,22), (78, 'Accorder jugement et réalité', 1,22), (79, 'Mentir utilement', 0,22), (80, 'Répéter', 0,22),
(81, 'Les règles du sport', 0,23), (82, 'Le juste et le droit', 1,23), (83, 'Les planètes', 0,23), (84, 'La biologie', 0,23),
(85, 'Une opinion sans preuve', 0,24), (86, 'Un raisonnement justifié', 1,24), (87, 'Une image', 0,24), (88, 'Un slogan', 0,24),
(89, 'Vrai', 1,25), (90, 'Faux', 0,25);

/* --------------------------
   RESSOURCES (catalog realistic)
   -------------------------- */
INSERT INTO ressource
(id, titre, description, auteur, url_fichier, type_fichier, image_couverture, tags, categorie, taille_fichier,
 nombre_vues, nombre_telechargements, note_moyenne, statut, date_ajout, est_active)
VALUES
(1, 'Bac Maths — Séries d’exercices (Dérivation)', 'Série d’exercices corrigés sur la dérivation et étude de fonctions.', 'Prof. A. Ben Abdallah', 'https://example.tn/derivation.pdf', 'PDF', NULL, 'bac,maths,derivation', 'Maths', 2200, 54, 18, '4.20', 'VALIDEE', NOW(), 1),
(2, 'Bac Sciences — Génétique (Résumé + QCM)', 'Résumé clair + QCM type Bac (génétique).', 'Mme. K. Triki', 'https://example.tn/genetique.pdf', 'PDF', NULL, 'bac,svt,genetique', 'SVT', 1800, 62, 25, '4.50', 'VALIDEE', NOW(), 1),
(3, 'Bac Info — SQL (cours + exercices)', 'Cours SQL: SELECT/WHERE/JOIN + exercices.', 'M. H. Bouslama', 'https://example.tn/sql.pdf', 'PDF', NULL, 'bac,info,sql', 'Informatique', 1400, 71, 31, '4.10', 'VALIDEE', NOW(), 1),
(4, 'Bac Eco — Offre et Demande', 'Fiche de révision: équilibre, déplacements des courbes, exercices.', 'Prof. S. Jebali', 'https://example.tn/offre_demande.pdf', 'PDF', NULL, 'bac,eco,offre,demande', 'Economie', 1100, 40, 12, '3.90', 'VALIDEE', NOW(), 1),
(5, 'Méthodo — Expression écrite (Français)', 'Plans types, connecteurs logiques, sujets Bac.', 'Mme. N. Chouchane', 'https://example.tn/expression.pdf', 'PDF', NULL, 'bac,francais,expression', 'Français', 900, 33, 9, '4.00', 'VALIDEE', NOW(), 1),
(6, 'Vidéo — Probabilités (Bac)', 'Explication simple + exercices (probabilités).', 'BacLab', 'https://example.tn/video-proba', 'VIDEO', NULL, 'bac,maths,proba', 'Maths', NULL, 95, 44, '4.30', 'VALIDEE', NOW(), 1),
(7, 'Lien — Banque d’examens Bac (Tunisie)', 'Liens vers sujets + corrigés (diverses filières).', 'BacLab', 'https://example.tn/examens', 'LIEN', NULL, 'bac,sujets,corriges', 'Général', NULL, 120, 0, '0.00', 'VALIDEE', NOW(), 1),
(8, 'Bac Sciences — Électricité RC', 'Régime transitoire RC: cours + exercices corrigés.', 'Prof. M. Khelifi', 'https://example.tn/rc.pdf', 'PDF', NULL, 'bac,physique,rc', 'Physique', 2000, 49, 17, '4.05', 'VALIDEE', NOW(), 1);

/* --------------------------
   EVALUATIONS (ratings + comments + favorites)
   Unique constraint: (ressource_id, utilisateur_id)
   -------------------------- */
INSERT INTO evaluation_ressource
(id, note, commentaire, est_favori, est_signale, date_evaluation, date_commentaire, date_favori, ressource_id, utilisateur_id)
VALUES
(1, 5, 'Très clair, les exercices ressemblent vraiment aux sujets Bac.', 1, 0, NOW(), NOW(), NOW(), 1, 3),
(2, 4, 'Bon contenu, j’aurais aimé plus d’exemples détaillés.',        0, 0, NOW(), NOW(), NULL, 1, 4),
(3, 5, 'Résumé génétique ممتاز، QCM مفيد برشا.',                        1, 0, NOW(), NOW(), NOW(), 2, 3),
(4, 4, 'Cours SQL bien structuré et accessible.',                      1, 0, NOW(), NOW(), NOW(), 3, 4),
(5, 4, 'Méthodo utile, surtout les connecteurs.',                      0, 0, NOW(), NOW(), NULL, 5, 3),
(6, 5, NULL,                                                         1, 0, NOW(), NULL, NOW(), 6, 3),
(7, 3, 'Lien pratique mais بعض الروابط مش تخدم.',                        0, 0, NOW(), NOW(), NULL, 7, 4),
(8, 4, 'RC bien expliqué, exercices corrects.',                        0, 0, NOW(), NOW(), NULL, 8, 3);

/* --------------------------
   FICHES (public/private + versions)
   -------------------------- */
INSERT INTO fiche (id, title, content, created_at, updated_at, is_public, utilisateur_id, filiere_id) VALUES
(1, 'Dérivation — Règles essentielles', '>> Règles: somme, produit, quotient\n== Astuce: factoriser avant dériver\n!! Attention aux domaines (ln, racine)\n\nExemples Bac + exercices.', NOW(), NULL, 1, 3, 1),
(2, 'Probabilités — Formules à connaître', '📘 P(A∪B)=P(A)+P(B)-P(A∩B)\n📘 B(n,p): E(X)=np, V(X)=np(1-p)\n\nExercices type Bac.', NOW(), NULL, 1, 4, 2),
(3, 'SQL — Mini mémo', 'SELECT ... FROM ... WHERE ...\nJOIN ... ON ...\nGROUP BY / ORDER BY\n\nExemples sur tables baclab.', NOW(), NULL, 1, 4, 3),
(4, 'Philo — Justice (plan type)', 'Définition, problématique, thèses, exemples.\n\nPlan: I/ II/ III + transition.', NOW(), NULL, 1, 2, 5);

INSERT INTO fiche_version (id, content, edited_at, editor_name, fiche_id) VALUES
(1, '>> Règles de dérivation + exemples (version 1).', NOW(), 'Yassine Trabelsi', 1),
(2, '>> Ajout exercices Bac 2020/2021 (version 2).', NOW(), 'Yassine Trabelsi', 1),
(3, '📘 Formules + arbres (version 1).', NOW(), 'Rim Gharbi', 2),
(4, '📘 Ajout loi binomiale + correction (version 2).', NOW(), 'Rim Gharbi', 2),
(5, 'SQL mémo (v1) + exemples JOIN.', NOW(), 'Rim Gharbi', 3);

/* Moderators (owner/admin) */
INSERT INTO fiche_moderateurs (id, fiche_id, utilisateur_id, created_at, is_owner) VALUES
(1, 1, 3, NOW(), 1),
(2, 2, 4, NOW(), 1),
(3, 4, 2, NOW(), 1),
(4, 1, 2, NOW(), 0);

/* Favoris */
INSERT INTO fiche_favoris (id, utilisateur_id, fiche_id, created_at) VALUES
(1, 4, 1, NOW()),
(2, 3, 2, NOW()),
(3, 3, 3, NOW());

/* Join Requests */
INSERT INTO fiche_join_requests
(id, fiche_id, utilisateur_id, message, status, processed_by_id, created_at, processed_at)
VALUES
(1, 1, 4, 'Je veux participer et ajouter des exercices corrigés.', 'approved', 2, NOW(), NOW()),
(2, 3, 3, 'Je peux améliorer les exemples JOIN.', 'pending', NULL, NOW(), NULL);

/* --------------------------
   GROUPES + MEMBRES + MESSAGES (chat)
   -------------------------- */
INSERT INTO groupe (id, nom, description, is_public, filiere_id, createur_id) VALUES
(1, 'Bac Sciences — Révision', 'Groupe de révision: SVT/Physique/Maths (Bac Sciences).', 1, 1, 2),
(2, 'Bac Maths — Exercices',   'Partage d’exercices, astuces, sujets Bac Maths.',       1, 2, 2),
(3, 'Bac Info — SQL & Algo',   'Aide SQL/Algo + mini-challenges.',                      1, 3, 2);

INSERT INTO membre_groupe (id, utilisateur_id, groupe_id, role_membre, statut, date_joint) VALUES
(1, 2, 1, 'ADMIN',      'ACCEPTED', NOW()),
(2, 3, 1, 'MEMBRE',     'ACCEPTED', NOW()),
(3, 4, 2, 'MEMBRE',     'ACCEPTED', NOW()),
(4, 2, 2, 'MODERATEUR', 'ACCEPTED', NOW()),
(5, 4, 3, 'MEMBRE',     'ACCEPTED', NOW()),
(6, 2, 3, 'ADMIN',      'ACCEPTED', NOW());

INSERT INTO message
(id, contenu, type_message, created_at, parent_message_id, deleted_at, file_path, file_name, fiche_id, expediteur_id, groupe_id)
VALUES
(1, 'Salut! Qui a des exercices sur dérivation?', 'TEXTE', NOW(), NULL, NULL, NULL, NULL, 1, 3, 2),
(2, 'Je peux partager une série corrigée (PDF).', 'TEXTE', NOW(), 1, NULL, NULL, NULL, NULL, 2, 2),
(3, 'Voici un mémo: P(A∪B)=P(A)+P(B)-P(A∩B).', 'TEXTE', NOW(), NULL, NULL, NULL, NULL, 2, 4, 2),
(4, 'Lien utile: https://example.tn/examens', 'TEXTE', NOW(), NULL, NULL, NULL, NULL, NULL, 2, 1),
(5, 'SQL JOIN: INNER JOIN relie les tables via une clé.', 'TEXTE', NOW(), NULL, NULL, NULL, NULL, 3, 4, 3),
(6, 'Merci! je vais réviser avec la fiche SQL.', 'TEXTE', NOW(), 5, NULL, NULL, NULL, 3, 3, 3);

/* --------------------------
   NOTIFICATIONS
   -------------------------- */
INSERT INTO notifications
(id, utilisateur_id, type, title, message, link, is_read, created_at, is_seen, membre_id, fiche_join_request_id)
VALUES
(1, 4, 'info', 'Demande acceptée', 'Votre demande de rejoindre la fiche "Dérivation" a été acceptée.', '/fiche/1', 0, NOW(), 0, NULL, 1),
(2, 2, 'info', 'Nouvelle demande', 'Nouvelle demande en attente pour la fiche SQL.',              '/admin/fiche/join-requests', 0, NOW(), 0, NULL, 2),
(3, 3, 'success', 'Ressource favorite', 'Vous avez ajouté une ressource en favori.',               '/ressource/2', 1, NOW(), 1, NULL, NULL),
(4, 3, 'info', 'Nouveau message', 'Nouveau message dans "Bac Info — SQL & Algo".',                '/groupe/3/chat', 0, NOW(), 0, 5, NULL);

SET FOREIGN_KEY_CHECKS = 1;

/* ============================================================
   END
   ============================================================ */
