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
            
            // 1. Récupérer les deux types de réponses
            $reponsesQCM = $_POST['reponses'] ?? [];
            $reponsesText = $_POST['reponses_text'] ?? [];
            
            // 2. Fusionner (l'index est l'ID de la question, donc pas de conflit)
            $reponsesUtilisateur = $reponsesQCM + $reponsesText;

            $model = new QuizModel();
            
            // SÉCURITÉ
            if ($model->hasUserPlayed($_SESSION['user']['id'], $quizId)) {
                header('Location: index.php?route=dashboard');
                exit;
            }

            $quiz = $model->getQuizForPlayer($quizId);
            
            $score = 0;
            $total = count($quiz['questions']);

            // Algorithme de correction (Uniquement pour les QCM)
            foreach ($quiz['questions'] as $q) {
                if ($q['type'] === 'qcm') {
                    // Logique QCM existante
                    $bonnes = [];
                    foreach ($q['choix'] as $c) if ($c['est_correct']) $bonnes[] = $c['id'];
                    
                    $choixUser = $reponsesUtilisateur[$q['id']] ?? [];
                    if (!is_array($choixUser)) $choixUser = [$choixUser];
                    
                    sort($bonnes); sort($choixUser);
                    if ($bonnes == $choixUser) $score++;
                } 
                else {
                    // Question Libre : Pas de correction auto (0 point par défaut ou 1 point offert, ici on laisse 0)
                    // Le texte est bien dans $reponsesUtilisateur, donc il sera sauvegardé
                }
            }

            // Sauvegarde
            $model->saveScore($_SESSION['user']['id'], $quizId, $score, $total, $reponsesUtilisateur);

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