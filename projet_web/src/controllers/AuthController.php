<?php
require_once __DIR__ . '/../config/Database.php';

class AuthController {
    
    public function login() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (isset($_SESSION['user'])) {
            header('Location: index.php?route=dashboard');
            exit;
        }

        $error = "";
        $success = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pdo = Database::getConnection();
            $action = $_POST['action'] ?? '';

            // --- CONNEXION ---
            if ($action === 'login') {
                $email = $_POST['email'] ?? '';
                $pass = $_POST['password'] ?? '';

                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($pass, $user['password'])) {
                    if ($user['is_active'] == 0) {
                        $error = "Ce compte a été désactivé.";
                    } else {
                        $_SESSION['user'] = $user;
                        header('Location: index.php?route=dashboard');
                        exit;
                    }
                } else {
                    $error = "Email ou mot de passe incorrect.";
                }
            } 
            
            // --- INSCRIPTION (AVEC RECAPTCHA) ---
            elseif ($action === 'register') {
                
                // 1. Vérification Google
                $recaptchaSecret = '6Le5Rx8sAAAAAECjeGOxt0XCbPiFgdn8kmHfhWzm';
                $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

                // Appel à l'API Google
                $verifyUrl = "https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$recaptchaResponse}";
                $verifyContent = @file_get_contents($verifyUrl);
                $json = json_decode($verifyContent);

                // Si le Captcha est invalide
                if (!$json || !$json->success) {
                    $error = "Veuillez cocher la case 'Je ne suis pas un robot'.";
                } 
                else {
                    // 2. Inscription normale
                    $nom = $_POST['nom'] ?? '';
                    $email = $_POST['email'] ?? '';
                    $pass = $_POST['password'] ?? '';
                    $role = $_POST['role'] ?? 'user';

                    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                    $stmt->execute([$email]);
                    
                    if ($stmt->rowCount() > 0) {
                        $error = "Cet email est déjà utilisé.";
                    } else {
                        $hash = password_hash($pass, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("INSERT INTO users (nom, email, password, role, is_active) VALUES (?, ?, ?, ?, 1)");
                        
                        if ($stmt->execute([$nom, $email, $hash, $role])) {
                            $success = "Compte créé ! Connectez-vous maintenant.";
                        } else {
                            $error = "Erreur lors de l'inscription.";
                        }
                    }
                }
            }
        }

        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: index.php');
        exit;
    }
}
?>