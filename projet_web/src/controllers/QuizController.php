<?php
require_once __DIR__ . '/../models/QuizModel.php';

class QuizController {

    // --- CRÉATION ---
    public function create() {
        $this->checkAuth();
        require_once __DIR__ . '/../views/creator/create_quiz.php';
    }

    public function store() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new QuizModel();
            $model->createCompleteQuiz(
                $_POST['titre'], 
                $_POST['description'], 
                $_SESSION['user']['id'], 
                $_POST['questions'] ?? []
            );
            header('Location: index.php?route=dashboard');
            exit;
        }
    }

    // --- ÉDITION ---
    public function edit() {
        $this->checkAuth();
        $quizId = $_GET['id'] ?? 0;
        
        $model = new QuizModel();
        $quiz = $model->getQuizById($quizId, $_SESSION['user']['id']);

        if (!$quiz) die("Quiz introuvable ou accès refusé.");

        require_once __DIR__ . '/../views/creator/edit_quiz.php';
    }

    public function update() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new QuizModel();
            $model->updateCompleteQuiz(
                $_POST['quiz_id'],
                $_POST['titre'],
                $_POST['description'],
                $_SESSION['user']['id'],
                $_POST['questions'] ?? []
            );
            header('Location: index.php?route=dashboard');
            exit;
        }
    }

    // --- SUPPRESSION ---
    public function delete() {
        $this->checkAuth();
        $quizId = $_GET['id'] ?? 0;
        
        $model = new QuizModel();
        $model->deleteQuiz($quizId, $_SESSION['user']['id']);
        
        header('Location: index.php?route=dashboard');
        exit;
    }

    // Helper sécurité session
    private function checkAuth() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user'])) { header('Location: index.php'); exit; }
    }

    public function publish() {
        $this->checkAuth(); // Sécurité
        $quizId = $_GET['id'] ?? 0;
        
        $model = new QuizModel();
        $model->publishQuiz($quizId, $_SESSION['user']['id']);
        
        // Retour au dashboard
        header('Location: index.php?route=dashboard');
        exit;
    }

    public function results() {
            $this->checkAuth();
            $quizId = $_GET['id'] ?? 0;
            $model = new QuizModel();
            $quiz = $model->getQuizById($quizId, $_SESSION['user']['id']);
            
            if (!$quiz) die("Accès refusé");

            // SI ENTREPRISE -> STATISTIQUES
            if ($_SESSION['user']['role'] === 'entreprise') {
                $statsData = $model->getQuizStats($quizId);
                require_once __DIR__ . '/../views/creator/stats_quiz.php';
            } 
            // SI ECOLE -> NOTES (Tableau classique)
            else {
                $results = $model->getQuizResults($quizId);
                require_once __DIR__ . '/../views/creator/results_quiz.php';
            }
        }
}
?>