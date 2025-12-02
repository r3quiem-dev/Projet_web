--  TABLE : roles
CREATE TABLE roles (
    id_role INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50) NOT NULL UNIQUE
);

--  TABLE : utilisateurs
CREATE TABLE utilisateurs (
    id_utilisateur INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    id_role INT DEFAULT 4,
    actif BOOLEAN DEFAULT TRUE,

    FOREIGN KEY(id_role) REFERENCES roles(id_role)
);

-- Table: Quizzes
CREATE TABLE quizzes (
    id_quiz INT PRIMARY KEY AUTO_INCREMENT,
    id_utilisateur_createur INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur_createur) REFERENCES utilisateurs(id_utilisateur)
);

-- Table: Questions
CREATE TABLE questions (
    id_question INT PRIMARY KEY AUTO_INCREMENT,
    id_quiz INT NOT NULL,
    texte_question TEXT NOT NULL,
    FOREIGN KEY (id_quiz) REFERENCES quizzes(id_quiz) ON DELETE CASCADE
);

-- Table 3: Choix/Options de Réponse
CREATE TABLE choix (
    id_choix INT PRIMARY KEY AUTO_INCREMENT,
    id_question INT NOT NULL,
    texte_choix TEXT NOT NULL,
    est_correct BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (id_question) REFERENCES questions(id_question) ON DELETE CASCADE
);