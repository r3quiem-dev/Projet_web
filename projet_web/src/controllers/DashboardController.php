<?php
require_once __DIR__ . '/../models/QuizModel.php';

class DashboardController {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user'])) { header('Location: index.php'); exit; }

        $user = $_SESSION['user'];
        $role = strtolower($user['role']); // Sécurité majuscule

        if ($role === 'ecole' || $role === 'entreprise') {
            $model = new QuizModel();
            $myQuizzes = $model->getQuizzesByAuthor($user['id']);
            require_once __DIR__ . '/../views/creator/dashboard.php';
        } elseif ($role === 'admin') {
            echo "Espace Admin";
        } else {
            // C'est un élève -> on charge le contrôleur Joueur
            require_once __DIR__ . '/../controllers/PlayerController.php';
            (new PlayerController())->index();
        }
    }
}
?>