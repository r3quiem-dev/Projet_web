<?php
require_once __DIR__ . '/../config/Database.php';

class AdminModel {
    // Tous les utilisateurs
    public function getAllUsers() {
        $pdo = Database::getConnection();
        return $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tous les quiz
    public function getAllQuizzes() {
        $pdo = Database::getConnection();
        return $pdo->query("SELECT q.*, u.nom as auteur FROM quizzes q JOIN users u ON q.auteur_id = u.id ORDER BY date_creation DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    // Activer/Désactiver n'importe quoi (User ou Quiz)
    public function toggleStatus($table, $id) {
        $pdo = Database::getConnection();
        // Sécurité : on n'accepte que les tables 'users' ou 'quizzes'
        if (!in_array($table, ['users', 'quizzes'])) return false;
        
        // Colonne statut différente selon la table
        $col = ($table === 'users') ? 'is_active' : 'status';
        
        // On récupère l'état actuel
        $stmt = $pdo->prepare("SELECT $col FROM $table WHERE id = ?");
        $stmt->execute([$id]);
        $val = $stmt->fetchColumn();

        // On inverse
        if ($table === 'users') {
            $newVal = ($val == 1) ? 0 : 1;
        } else {
            // Pour les quiz, on bascule entre 'lancé' et 'terminé' (ou on ajoute un statut 'désactivé')
            // Pour faire simple selon le cahier des charges (modération) :
            // Si c'est 'lancé', on passe à 'terminé' (bloqué). Si 'terminé', on relance.
            $newVal = ($val === 'lancé') ? 'terminé' : 'lancé';
        }

        $update = $pdo->prepare("UPDATE $table SET $col = ? WHERE id = ?");
        return $update->execute([$newVal, $id]);
    }
}
?>