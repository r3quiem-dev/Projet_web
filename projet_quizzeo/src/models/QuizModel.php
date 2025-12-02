<?php
require_once __DIR__ . '/../config/Database.php';

class QuizModel {
    
    // Récupérer tous les quiz créés par un utilisateur précis
    public function getQuizzesByAuthor($authorId) {
        $pdo = Database::getConnection();
        // On trie par date de création (le plus récent en haut)
        $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE auteur_id = ? ORDER BY date_creation DESC");
        $stmt->execute([$authorId]);
        return $stmt->fetchAll();
    }
}
?>