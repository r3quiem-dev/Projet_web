<?php
require_once __DIR__ . '/../models/QuizModel.php';
require_once __DIR__ . '/../models/AdminModel.php';

class DashboardController {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user'])) { header('Location: index.php'); exit; }

        $user = $_SESSION['user'];
        $role = strtolower($user['role']);

        // ADMIN
        if ($role === 'admin') {
            $adminModel = new AdminModel();
            
            // gestion des actions
            if (isset($_GET['action']) && isset($_GET['id'])) {
                if ($_GET['action'] === 'toggle_user') {
                    $adminModel->toggleStatus('users', $_GET['id']);
                } elseif ($_GET['action'] === 'toggle_quiz') {
                    $adminModel->toggleStatus('quizzes', $_GET['id']);
                }
                // recharge de la page apr un action
                header('Location: index.php?route=dashboard');
                exit;
            }

            $users = $adminModel->getAllUsers();
            $quizzes = $adminModel->getAllQuizzes();
            
            require_once __DIR__ . '/../views/admin/dashboard.php';
        } 
        
        // ECOLE / ENTREPRISE
        elseif ($role === 'ecole' || $role === 'entreprise') {
            $model = new QuizModel();
            $myQuizzes = $model->getQuizzesByAuthor($user['id']);
            require_once __DIR__ . '/../views/creator/dashboard.php';
        } 
        
        // ELEVE
        else {
            require_once __DIR__ . '/../controllers/PlayerController.php';
            (new PlayerController())->index();
        }
    }
}
?>