<?php
require_once __DIR__ . '/../models/UserModel.php';

class ProfileController {
    
    public function edit() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user'])) { header('Location: index.php'); exit; }

        $userModel = new UserModel();
        $user = $userModel->getUserById($_SESSION['user']['id']);
        $message = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'];
            $email = $_POST['email'];
            $password = !empty($_POST['password']) ? $_POST['password'] : null;

            if ($userModel->updateUser($user['id'], $nom, $email, $password)) {
                $message = "Profil mis à jour avec succès !";
                // Mise à jour de la session pour l'affichage immédiat
                $_SESSION['user']['nom'] = $nom;
                $_SESSION['user']['email'] = $email;
                $user = $userModel->getUserById($user['id']); // Rafraîchir les données
            } else {
                $message = "Erreur lors de la mise à jour.";
            }
        }

        require_once __DIR__ . '/../views/auth/profile.php';
    }
}
?>