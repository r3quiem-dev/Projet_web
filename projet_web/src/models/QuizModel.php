<?php
require_once __DIR__ . '/../config/Database.php';

class QuizModel {

    // --- GESTION CRÉATEUR ---

    // Lister les quiz d'un auteur (AVEC COMPTEUR DE RÉPONSES)
    public function getQuizzesByAuthor($authorId) {
        $pdo = Database::getConnection();
        // La sous-requête (SELECT COUNT...) compte les lignes dans la table resultats pour ce quiz
        $sql = "SELECT q.*, 
                (SELECT COUNT(*) FROM resultats r WHERE r.quiz_id = q.id) as nb_reponses 
                FROM quizzes q 
                WHERE auteur_id = ? 
                ORDER BY date_creation DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$authorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Créer un quiz complet
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

    // Récupérer un quiz par ID (pour modification)
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

    // Mettre à jour un quiz
    public function updateCompleteQuiz($id, $titre, $description, $authorId, $questions) {
        $pdo = Database::getConnection();
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("UPDATE quizzes SET titre = ?, description = ? WHERE id = ? AND auteur_id = ?");
            $stmt->execute([$titre, $description, $id, $authorId]);

            if (!empty($questions)) {
                $pdo->prepare("DELETE FROM questions WHERE quiz_id = ?")->execute([$id]);
                $this->insertQuestions($pdo, $id, $questions);
            }
            
            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    // Supprimer un quiz
    public function deleteQuiz($id, $authorId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM quizzes WHERE id = ? AND auteur_id = ?");
        return $stmt->execute([$id, $authorId]);
    }

    // Publier un quiz
    public function publishQuiz($id, $authorId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE quizzes SET status = 'lancé' WHERE id = ? AND auteur_id = ?");
        return $stmt->execute([$id, $authorId]);
    }

    // Helper privé pour insérer les questions
    private function insertQuestions($pdo, $quizId, $questionsData) {
        if (!empty($questionsData) && is_array($questionsData)) {
            $stmtQ = $pdo->prepare("INSERT INTO questions (quiz_id, texte, type) VALUES (?, ?, ?)");
            $stmtC = $pdo->prepare("INSERT INTO choix (question_id, texte, est_correct) VALUES (?, ?, ?)");

            foreach ($questionsData as $q) {
                $type = $q['type'] ?? 'qcm';
                $stmtQ->execute([$quizId, $q['texte'], $type]);
                $qId = $pdo->lastInsertId();

                if ($type === 'qcm' && isset($q['choix'])) {
                    foreach ($q['choix'] as $c) {
                        $correct = isset($c['correct']) ? 1 : 0;
                        $stmtC->execute([$qId, $c['texte'], $correct]);
                    }
                }
            }
        }
    }

    // --- PARTIE JOUEUR ---

    public function getAvailableQuizzes() {
        $pdo = Database::getConnection();
        return $pdo->query("SELECT q.*, u.nom as auteur FROM quizzes q JOIN users u ON q.auteur_id = u.id WHERE q.status = 'lancé' ORDER BY q.date_creation DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQuizForPlayer($quizId) {
        // Similaire à getQuizById mais sans vérification auteur (pour le joueur)
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
        $stmt->execute([$quizId]);
        $quiz = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$quiz) return null;

        $stmtQ = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
        $stmtQ->execute([$quizId]);
        $quiz['questions'] = $stmtQ->fetchAll(PDO::FETCH_ASSOC);

        foreach ($quiz['questions'] as &$q) {
            $stmtC = $pdo->prepare("SELECT id, texte, est_correct FROM choix WHERE question_id = ?");
            $stmtC->execute([$q['id']]);
            $q['choix'] = $stmtC->fetchAll(PDO::FETCH_ASSOC);
        }
        return $quiz;
    }

    public function hasUserPlayed($userId, $quizId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM resultats WHERE user_id = ? AND quiz_id = ?");
        $stmt->execute([$userId, $quizId]);
        return $stmt->fetchColumn() > 0;
    }

    public function getPlayerResults($userId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT quiz_id, score, total_questions FROM resultats WHERE user_id = ?");
        $stmt->execute([$userId]);
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) $results[$row['quiz_id']] = $row;
        return $results;
    }

    public function saveScore($userId, $quizId, $score, $total, $reponses) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO resultats (user_id, quiz_id, score, total_questions, reponses_json) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$userId, $quizId, $score, $total, json_encode($reponses)]);
    }

    // --- PARTIE RÉSULTATS / STATS ---

    public function getQuizResults($quizId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT r.*, u.nom, u.email FROM resultats r JOIN users u ON r.user_id = u.id WHERE r.quiz_id = ? ORDER BY r.score DESC");
        $stmt->execute([$quizId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQuizStats($quizId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM resultats WHERE quiz_id = ?");
        $stmt->execute([$quizId]);
        $total = $stmt->fetchColumn();

        if ($total == 0) return ['total' => 0, 'data' => [], 'text' => []];

        $stmt = $pdo->prepare("SELECT reponses_json FROM resultats WHERE quiz_id = ?");
        $stmt->execute([$quizId]);
        $allReponses = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $statsData = [];
        $statsText = [];

        foreach ($allReponses as $json) {
            if (empty($json)) continue;
            $reponses = json_decode($json, true);
            if (!is_array($reponses)) continue;

            foreach ($reponses as $qId => $val) {
                if (is_array($val)) {
                    foreach ($val as $cId) {
                        if (!isset($statsData[$qId][$cId])) $statsData[$qId][$cId] = 0;
                        $statsData[$qId][$cId]++;
                    }
                } else {
                    if (is_numeric($val)) {
                        if (!isset($statsData[$qId][$val])) $statsData[$qId][$val] = 0;
                        $statsData[$qId][$val]++;
                    } else {
                        if (!empty($val)) $statsText[$qId][] = htmlspecialchars($val);
                    }
                }
            }
        }
        return ['total' => $total, 'data' => $statsData, 'text' => $statsText];
    }
}
?>