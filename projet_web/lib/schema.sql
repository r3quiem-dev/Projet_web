USE quizzeo;

-- 1. Utilisateurs
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'ecole', 'entreprise', 'user') NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. Quizzes
CREATE TABLE quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    auteur_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('en cours d''écriture', 'lancé', 'terminé') DEFAULT 'en cours d''écriture',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (auteur_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 3. Questions
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    texte VARCHAR(255) NOT NULL,
    type ENUM('qcm', 'libre') DEFAULT 'qcm', -- Nouveau champ Type
    points INT DEFAULT 1,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);

-- 4. Choix (Réponses QCM)
CREATE TABLE choix (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    texte VARCHAR(255) NOT NULL,
    est_correct TINYINT(1) DEFAULT 0,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

-- 5. Résultats (Notes + Détails JSON)
CREATE TABLE resultats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    quiz_id INT NOT NULL,
    score INT NOT NULL,
    total_questions INT NOT NULL,
    reponses_json JSON NULL, -- Stocke le détail (QCM + Texte)
    date_participation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);