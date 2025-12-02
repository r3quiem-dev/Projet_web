<?php
require_once __DIR__ . '/../config/Database.php';

class QuizModel {

    // 1. Lister les quiz d'un auteur
    public function getQuizzesByAuthor($authorId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE auteur_id = ? ORDER BY date_creation DESC");
        $stmt->execute([$authorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Créer un quiz complet
    public function createCompleteQuiz($titre, $description, $authorId, $questionsData) {
        $pdo = Database::getConnection();
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("INSERT INTO quizzes (titre, description, auteur_id, status) VALUES (?, ?, ?, 'en cours d''écriture')");
            $stmt->execute([$titre, $description, $authorId]);
            $quizId = $pdo->lastInsertId();

            $this->insertQuestions($pdo, $quizId, $questionsData);

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    // 3. Récupérer un quiz par ID (pour l'édition)
    public function getQuizById($id, $authorId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND auteur_id = ?");
        $stmt->execute([$id, $authorId]);
        $quiz = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($quiz) {
            $stmtQ = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
            $stmtQ->execute([$id]);
            $quiz['questions'] = $stmtQ->fetchAll(PDO::FETCH_ASSOC);

            foreach ($quiz['questions'] as &$q) {
                $stmtC = $pdo->prepare("SELECT * FROM choix WHERE question_id = ?");
                $stmtC->execute([$q['id']]);
                $q['choix'] = $stmtC->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        return $quiz;
    }

    // 4. Mettre à jour un quiz (Supprime anciennes questions et remet les nouvelles)
    public function updateCompleteQuiz($id, $titre, $description, $authorId, $questions) {
        $pdo = Database::getConnection();
        try {
            $pdo->beginTransaction();
            
            // Update infos de base
            $stmt = $pdo->prepare("UPDATE quizzes SET titre = ?, description = ? WHERE id = ? AND auteur_id = ?");
            $stmt->execute([$titre, $description, $id, $authorId]);

            // Si on a des questions, on remplace tout (méthode radicale mais sûre)
            if (!empty($questions)) {
                // On supprime les anciennes
                $pdo->prepare("DELETE FROM questions WHERE quiz_id = ?")->execute([$id]);
                // On insère les nouvelles
                $this->insertQuestions($pdo, $id, $questions);
            }
            
            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    // 5. Supprimer un quiz
    public function deleteQuiz($id, $authorId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM quizzes WHERE id = ? AND auteur_id = ?");
        return $stmt->execute([$id, $authorId]);
    }

    // Helper pour insérer les questions (évite de dupliquer le code)
    private function insertQuestions($pdo, $quizId, $questionsData) {
        if (!empty($questionsData) && is_array($questionsData)) {
            $stmtQ = $pdo->prepare("INSERT INTO questions (quiz_id, texte) VALUES (?, ?)");
            $stmtC = $pdo->prepare("INSERT INTO choix (question_id, texte, est_correct) VALUES (?, ?, ?)");

            foreach ($questionsData as $q) {
                $stmtQ->execute([$quizId, $q['texte']]);
                $qId = $pdo->lastInsertId();

                if (isset($q['choix']) && is_array($q['choix'])) {
                    foreach ($q['choix'] as $c) {
                        $correct = isset($c['correct']) ? 1 : 0;
                        $stmtC->execute([$qId, $c['texte'], $correct]);
                    }
                }
            }
        }
    }

    // 1. Récupérer tous les quiz "lancés" (pour l'élève)
    public function getAvailableQuizzes() {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT q.*, u.nom as auteur FROM quizzes q 
                             JOIN users u ON q.auteur_id = u.id 
                             WHERE q.status = 'lancé' 
                             ORDER BY q.date_creation DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Récupérer un quiz complet avec questions et choix (pour jouer)
    public function getQuizForPlayer($quizId) {
        $pdo = Database::getConnection();
        
        // Infos du quiz
        $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
        $stmt->execute([$quizId]);
        $quiz = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$quiz) return null;

        // Questions
        $stmtQ = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
        $stmtQ->execute([$quizId]);
        $quiz['questions'] = $stmtQ->fetchAll(PDO::FETCH_ASSOC);

        // Choix
        foreach ($quiz['questions'] as &$q) {
            $stmtC = $pdo->prepare("SELECT id, texte, est_correct FROM choix WHERE question_id = ?");
            $stmtC->execute([$q['id']]);
            $q['choix'] = $stmtC->fetchAll(PDO::FETCH_ASSOC);
        }
        return $quiz;
    }

    // 3. Sauvegarder le score
    public function saveScore($userId, $quizId, $score, $total) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO resultats (user_id, quiz_id, score, total_questions) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$userId, $quizId, $score, $total]);
    }

    // Passer un quiz en statut 'lancé'
    public function publishQuiz($id, $authorId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE quizzes SET status = 'lancé' WHERE id = ? AND auteur_id = ?");
        return $stmt->execute([$id, $authorId]);
    }

    // Vérifie si un joueur a déjà fait ce quiz (Renvoie true ou false)
    public function hasUserPlayed($userId, $quizId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM resultats WHERE user_id = ? AND quiz_id = ?");
        $stmt->execute([$userId, $quizId]);
        return $stmt->fetchColumn() > 0;
    }

    // Récupère l'historique des résultats d'un joueur (Indexé par quiz_id)
    public function getPlayerResults($userId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT quiz_id, score, total_questions FROM resultats WHERE user_id = ?");
        $stmt->execute([$userId]);
        
        // On réorganise le tableau pour pouvoir chercher facilement par ID de quiz
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[$row['quiz_id']] = $row;
        }
        return $results;
    }

    public function getQuizResults($quizId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT r.*, u.nom, u.email 
            FROM resultats r 
            JOIN users u ON r.user_id = u.id 
            WHERE r.quiz_id = ? 
            ORDER BY r.score DESC
        ");
        $stmt->execute([$quizId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>