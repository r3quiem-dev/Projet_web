<?php
require_once __DIR__ . '/../models/QuizModel.php';

class PlayerController {

    // liste des quiz
    public function index() {
        $this->checkAuth();
        $model = new QuizModel();
        
        // recup des quiz dispo
        $quizzes = $model->getAvailableQuizzes();
        
        // recup des resultats
        $myResults = $model->getPlayerResults($_SESSION['user']['id']);
        
        require_once __DIR__ . '/../views/player/dashboard.php';
    }

    // afficher le quiz
    public function play() {
        $this->checkAuth();
        $quizId = $_GET['id'] ?? 0;
        $userId = $_SESSION['user']['id'];
        
        $model = new QuizModel();

        // condition si deja fais le quiz
        if ($model->hasUserPlayed($userId, $quizId)) {
            header('Location: index.php?route=dashboard&error=already_played');
            exit;
        }

        // recup du quiz
        $quiz = $model->getQuizForPlayer($quizId);
        if (!$quiz) die("Quiz introuvable ou non disponible.");

        require_once __DIR__ . '/../views/player/play.php';
    }

    // calcul du score
    public function submit() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $quizId = $_POST['quiz_id'];
            
            // recup des types de reponses
            $reponsesQCM = $_POST['reponses'] ?? [];
            $reponsesText = $_POST['reponses_text'] ?? [];
            
            // fusion des reponses
            $reponsesUtilisateur = $reponsesQCM + $reponsesText;

            $model = new QuizModel();
            
            // verif si deja fait
            if ($model->hasUserPlayed($_SESSION['user']['id'], $quizId)) {
                header('Location: index.php?route=dashboard');
                exit;
            }

            $quiz = $model->getQuizForPlayer($quizId);
            
            $score = 0;
            $total = count($quiz['questions']);

            // algo de correction
            foreach ($quiz['questions'] as $q) {
                if ($q['type'] === 'qcm') {
                    // logique qcm
                    $bonnes = [];
                    foreach ($q['choix'] as $c) if ($c['est_correct']) $bonnes[] = $c['id'];
                    
                    $choixUser = $reponsesUtilisateur[$q['id']] ?? [];
                    if (!is_array($choixUser)) $choixUser = [$choixUser];
                    
                    sort($bonnes); sort($choixUser);
                    if ($bonnes == $choixUser) $score++;
                } 
                else {
                    // question libre
                }
            }

            // sauvegarde du score
            $model->saveScore($_SESSION['user']['id'], $quizId, $score, $total, $reponsesUtilisateur);

            header("Location: index.php?route=dashboard&score=$score&total=$total");
            exit;
        }
    }

    // check l'auth
    private function checkAuth() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user'])) { header('Location: index.php'); exit; }
    }
    
}
?>