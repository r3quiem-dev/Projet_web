<?php
require_once __DIR__ . '/../models/QuizModel.php';
// require_once __DIR__ . '/../Model/UserModel.php'; // Pour l'admin plus tard

class DashboardController {

    public function index() {
        // 1. Sécurité
        if (!isset($_SESSION['user'])) {
            header('Location: index.php');
            exit;
        }

        $user = $_SESSION['user'];
        $role = $user['role'];

        // 2. Aiguillage
        if ($role === 'admin') {
            // TODO: Charger les données admin
            // require_once __DIR__ . '/../View/Admin/dashboard.php';
            echo "<h1>Espace Admin (Vue à créer dans /view/Admin/dashboard.php)</h1>";
        } 
        elseif ($role === 'ecole' || $role === 'entreprise') {
            // C'est ici que ça se passe !
            $quizModel = new QuizModel();
            
            // On récupère les données
            $myQuizzes = $quizModel->getQuizzesByAuthor($user['id']);
            
            // On charge la Vue qu'on vient de créer
            require_once __DIR__ . '/../view/Creator/dashboard.php';
        } 
        else {
            // Utilisateur simple
            echo "<h1>Espace Joueur (Vue à créer dans /view/Player/dashboard.php)</h1>";
        }
    }
}
?>