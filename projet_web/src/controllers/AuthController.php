<?php
require_once __DIR__ . '/../config/Database.php';

class AuthController {
    
    public function login() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Si déjà connecté, on redirige
        if (isset($_SESSION['user'])) {
            header('Location: index.php?route=dashboard');
            exit;
        }

        $error = "";
        $success = "";

        // TRAITEMENT DU FORMULAIRE
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pdo = Database::getConnection();
            $action = $_POST['action'] ?? '';

            // --- CAS 1 : CONNEXION ---
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
            
            // --- CAS 2 : INSCRIPTION ---
            elseif ($action === 'register') {
                $nom = $_POST['nom'] ?? '';
                $email = $_POST['email'] ?? '';
                $pass = $_POST['password'] ?? '';
                $role = $_POST['role'] ?? 'user';

                // Vérifier si l'email existe déjà
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                
                if ($stmt->rowCount() > 0) {
                    $error = "Cet email est déjà utilisé.";
                } else {
                    // Création du compte
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

        // On charge la vue avec les messages
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