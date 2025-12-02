<?php
// Fichier : src/controller/AuthController.php
require_once __DIR__ . '/../config/Database.php';

class AuthController {

    public function login() {
        // Démarrage session si pas déjà fait
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Si déjà connecté, on redirige
        if (isset($_SESSION['user'])) {
            header('Location: dashboard.php');
            exit;
        }

        // Génération Captcha
        if (!isset($_SESSION['captcha_result'])) {
            $n1 = rand(1, 10);
            $n2 = rand(1, 10);
            $_SESSION['captcha_challenge'] = "$n1 + $n2";
            $_SESSION['captcha_result'] = $n1 + $n2;
        }

        $message = "";
        $msgType = "";
        $pdo = Database::getConnection();

        // --- TRAITEMENT DU FORMULAIRE (POST) ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // CAS 1 : LOGIN
            if (isset($_POST['action']) && $_POST['action'] === 'login') {
                $email = $_POST['email'] ?? '';
                $pass  = $_POST['password'] ?? '';

                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $userFound = $stmt->fetch();

                if ($userFound && password_verify($pass, $userFound['password'])) {
                    if ($userFound['is_active'] == 0) {
                        $message = "Compte désactivé.";
                        $msgType = "error";
                    } else {
                        $_SESSION['user'] = $userFound;
                        header('Location: dashboard.php');
                        exit;
                    }
                } else {
                    $message = "Identifiants incorrects.";
                    $msgType = "error";
                }
            } 
            
            // CAS 2 : REGISTER
            elseif (isset($_POST['action']) && $_POST['action'] === 'register') {
                if (intval($_POST['captcha']) !== $_SESSION['captcha_result']) {
                    $message = "Captcha incorrect !";
                    $msgType = "error";
                } else {
                    // Vérifier doublon email
                    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                    $stmt->execute([$_POST['email']]);
                    
                    if ($stmt->rowCount() > 0) {
                        $message = "Email déjà utilisé.";
                        $msgType = "error";
                    } else {
                        // Création
                        $sql = "INSERT INTO users (nom, email, password, role, is_active) VALUES (?, ?, ?, ?, 1)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([
                            $_POST['nom'],
                            $_POST['email'],
                            password_hash($_POST['password'], PASSWORD_DEFAULT),
                            $_POST['role']
                        ]);
                        $message = "Compte créé ! Connectez-vous.";
                        $msgType = "success";
                        unset($_SESSION['captcha_result']);
                    }
                }
            }
        }

        // À la fin, on charge la VUE (le HTML)
        require_once __DIR__ . '/../view/Auth/login.php';
    }
}
?>