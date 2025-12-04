# Documentation du Projet : Quizzeo

**Quizzeo** est une application web de gestion de quiz développée avec une architecture **MVC (Modèle-Vue-Contrôleur)** "faite maison" en PHP natif. Elle permet la création, la gestion, la publication et la participation à des quiz interactifs.

---

## Architecture & Stack Technique

* **Langage :** PHP.
* **Base de données :** MySQL (PDO).
* **Frontend :** HTML, CSS, Javascript.
* **Sécurité :** Hashage des mots de passe, requêtes préparées, Google reCAPTCHA.

---

## Rôles Utilisateurs

L'application gère 4 types de profils définis en base de données :
1.  **Admin :** Modération globale (bannissement utilisateurs, activation/désactivation quiz).
2.  **École :** Création de quiz notés, accès aux tableaux de résultats des élèves.
3.  **Entreprise :** Création de sondages/quiz, accès aux statistiques graphiques.
4.  **Utilisateur :** Participation aux quiz lancés, visualisation des scores.

---

## Structure des Fichiers

## 📂 Structure du Projet

L'application principale suit une architecture **MVC (Modèle-Vue-Contrôleur)** située dans le dossier `projet_web/`.

```text
projet_web/
    ├── lib/
    │   ├──readme.md
    │   └── schema.sql
    │
    ├── public/
    │   ├── css/
    │   ├── img/
    │   └── index.php
    │
    └── src/
        ├── config/
        │   └── Database.php
        │
        ├── controllers/
        │   ├── AuthController.php
        │   ├── DashboardController.php
        │   ├── PlayerController.php
        │   ├── ProfileController.php
        │   └── QuizController.php
        │
        ├── models/
        │   ├── AdminModel.php
        │   ├── QuizModel.php
        │   └── UserModel.php
        │
        └── views/
            ├── admin/
            │   └── dashboard.php
            ├── auth/
            │   ├── login.php
            │   └── profile.php
            ├── creator/
            │   ├── create_quiz.php
            │   ├── dashboard.php
            │   ├── edit_quiz.php
            │   ├── results_quiz.php
            │   └── stats_quiz.php
            ├── player/
            │   ├── dashboard.php
            │   └── play.php
            └── footer.php


### 1. Le Cœur (Configuration & Routage)

| Fichier | Description |
| :--- | :--- |
| `public/index.php` | **Le Routeur (Point d'entrée).** Analyse l'URL et appelle le bon contrôleur. |
| `src/config/Database.php` | **Connexion BDD.** Singleton gérant la connexion MySQL via PDO. |
| `lib/schema.sql` | **Structure SQL.** Script de création des tables (`users`, `quizzes`, `questions`, etc.). |

### 2. Les Contrôleurs (`src/controllers/`)

Ils font le lien entre la base de données et l'affichage.

* **`AuthController.php`** : Gère la connexion, la déconnexion et l'inscription (avec vérification reCAPTCHA).
* **`DashboardController.php`** : Redirige vers la vue adaptée selon le rôle (Admin, Créateur ou Joueur).
* **`QuizController.php`** : Gère le CRUD complet des quiz (Créer, Modifier, Supprimer, Publier) et l'affichage des résultats côté créateur.
* **`PlayerController.php`** : Gère l'expérience du joueur (liste des quiz disponibles, jeu, soumission des réponses).
* **`ProfileController.php`** : Gestion de la modification du profil utilisateur.

### 3. Les Modèles (`src/models/`)

Ils contiennent les requêtes SQL.

* **`QuizModel.php`** : Gère les données complexes des quiz (questions, choix multiples, statistiques de réponses).
* **`UserModel.php`** : Opérations sur les utilisateurs (récupération par ID, mise à jour).
* **`AdminModel.php`** : Fonctions d'administration (listing global, bascule de statut actif/inactif).

### 4. Les Vues (`src/views/`)

L'interface utilisateur (HTML/PHP).

#### Authentification (`views/auth/`)
* `login.php` : Page de connexion/inscription.
* `profile.php` : Formulaire de modification du profil.

#### Créateur & Admin (`views/creator/` & `views/admin/`)
* `create_quiz.php` / `edit_quiz.php` : Formulaires dynamiques JS pour ajouter/retirer des questions.
* `dashboard.php` (Creator) : Liste des quiz créés et gestion des statuts.
* `dashboard.php` (Admin) : Accordéons pour modérer les utilisateurs et les quiz.
* `results_quiz.php` : Tableau des notes (Vue École).
* `stats_quiz.php` : Visualisation graphique des réponses (Vue Entreprise).

#### Utilisateur (`views/player/`)
* `dashboard.php` : Grille des cartes de quiz disponibles.
* `play.php` : Interface de jeu (QCM et champs texte).

### 5. Styles & Assets (`public/`)

* `css/global.css` : Charte graphique principale (variables couleurs, style "Neubrutalism").
* `css/auth.css` : Styles spécifiques à l'animation de login.
* `css/dashboard.css` : Styles des tableaux et grilles de cartes.