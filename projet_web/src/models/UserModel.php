<?php
require_once __DIR__ . '/../config/Database.php';

class UserModel {
    // Récupérer un user par ID
    public function getUserById($id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Mettre à jour les infos
    public function updateUser($id, $nom, $email, $password = null) {
        $pdo = Database::getConnection();
        
        if ($password) {
            // Si le mot de passe est fourni, on le met à jour
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET nom = ?, email = ?, password = ? WHERE id = ?");
            return $stmt->execute([$nom, $email, $hash, $id]);
        } else {
            // Sinon on garde l'ancien
            $stmt = $pdo->prepare("UPDATE users SET nom = ?, email = ? WHERE id = ?");
            return $stmt->execute([$nom, $email, $id]);
        }
    }
}
?>