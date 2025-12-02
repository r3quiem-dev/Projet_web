<?php
require_once __DIR__ . '/../models/QuizModel.php';

class PlayerController {

    // --- 1. DASHBOARD : Liste des quiz ---
    public function index() {
        $this->checkAuth();
        $model = new QuizModel();
        
        // Récupérer les quiz lancés
        $quizzes = $model->getAvailableQuizzes();
        
        // Récupérer l'historique des résultats du joueur
        $myResults = $model->getPlayerResults($_SESSION['user']['id']);
        
        require_once __DIR__ . '/../views/player/dashboard.php';
    }

    // --- 2. JOUER : Afficher le quiz ---
    public function play() {
        $this->checkAuth();
        $quizId = $_GET['id'] ?? 0;
        $userId = $_SESSION['user']['id'];
        
        $model = new QuizModel();

        // SÉCURITÉ : Si déjà joué, on bloque !
        if ($model->hasUserPlayed($userId, $quizId)) {
            header('Location: index.php?route=dashboard&error=already_played');
            exit;
        }

        // Récupérer le quiz
        $quiz = $model->getQuizForPlayer($quizId);
        if (!$quiz) die("Quiz introuvable ou non disponible.");

        require_once __DIR__ . '/../views/player/play.php';
    }

    // --- 3. CORRECTION : Calculer le score ---
    public function submit() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $quizId = $_POST['quiz_id'];
            $reponsesUtilisateur = $_POST['reponses'] ?? []; // Tableau [question_id => [choix_id, ...]]

            $model = new QuizModel();
            
            // SÉCURITÉ : On vérifie encore si déjà joué (double protection)
            if ($model->hasUserPlayed($_SESSION['user']['id'], $quizId)) {
                header('Location: index.php?route=dashboard');
                exit;
            }

            $quiz = $model->getQuizForPlayer($quizId);
            
            $score = 0;
            $total = count($quiz['questions']);

            // Algorithme de correction (QCM Multiple)
            foreach ($quiz['questions'] as $q) {
                
                // A. On récupère les IDs des VRAIES bonnes réponses en BDD
                $bonnesReponsesIds = [];
                foreach ($q['choix'] as $c) {
                    if ($c['est_correct'] == 1) {
                        $bonnesReponsesIds[] = $c['id'];
                    }
                }

                // B. On récupère les IDs cochés par l'élève
                $choixUserIds = $reponsesUtilisateur[$q['id']] ?? [];
                
                // Si c'est une réponse unique (radio), ce n'est pas un tableau, on le transforme
                if (!is_array($choixUserIds)) {
                    $choixUserIds = [$choixUserIds];
                }

                // C. Comparaison stricte (Il faut tout bon et rien de faux)
                sort($bonnesReponsesIds); // On trie pour comparer facilement
                sort($choixUserIds);

                if ($bonnesReponsesIds == $choixUserIds) {
                    $score++;
                }
            }

            // Sauvegarde
            $model->saveScore($_SESSION['user']['id'], $quizId, $score, $total);

            // Redirection
            header("Location: index.php?route=dashboard&score=$score&total=$total");
            exit;
        }
    }

    // Helper de sécurité
    private function checkAuth() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user'])) { header('Location: index.php'); exit; }
    }
}
?>