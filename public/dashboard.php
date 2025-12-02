<?php
session_start();

// 1. SÉCURITÉ : Vérifier si connecté
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$currentUser = $_SESSION['user'];
$jsonFileUsers = __DIR__ . '/../database/users.json';
$users = json_decode(file_get_contents($jsonFileUsers), true) ?? [];

$message = "";

// 2. LOGIQUE ADMINISTRATEUR (Gestion des actions)
if ($currentUser['role'] === 'admin' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Action : Activer / Désactiver un utilisateur
    if (isset($_POST['action']) && $_POST['action'] === 'toggle_active') {
        $targetId = $_POST['user_id'];
        
        // On ne peut pas se désactiver soi-même !
        if ($targetId == $currentUser['id']) {
            $message = "Vous ne pouvez pas désactiver votre propre compte !";
        } else {
            // On cherche l'utilisateur et on inverse son statut
            foreach ($users as &$u) {
                if ($u['id'] == $targetId) {
                    $u['isActive'] = !$u['isActive']; // On inverse (true -> false ou false -> true)
                    break;
                }
            }
            unset($u);
            
            // On sauvegarde le fichier
            file_put_contents($jsonFileUsers, json_encode($users, JSON_PRETTY_PRINT));
            $message = "Statut de l'utilisateur mis à jour.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Quizzeo</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f4f4; margin: 0; }
        
        /* NAVBAR */
        .navbar { background-color: #333; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { margin: 0; font-size: 20px; }
        .logout-btn { background-color: #ff4d4d; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-weight: bold; }
        
        /* CONTENU */
        .container { max-width: 1000px; margin: 30px auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        
        /* TABLEAU ADMIN */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background-color: #f8f8f8; }
        .status-active { color: green; font-weight: bold; }
        .status-inactive { color: red; font-weight: bold; }
        
        .action-btn { cursor: pointer; padding: 5px 10px; border: none; border-radius: 4px; color: white; font-weight: bold; }
        .btn-disable { background-color: #ffc107; color: #333; }
        .btn-enable { background-color: #28a745; }

        .role-badge { padding: 3px 8px; border-radius: 10px; font-size: 0.8em; text-transform: uppercase; color: white; }
        .role-admin { background-color: #333; }
        .role-ecole { background-color: #007bff; }
        .role-entreprise { background-color: #6610f2; }
        .role-user { background-color: #28a745; }
    </style>
</head>
<body>

    <div class="navbar">
        <h1>Quizzeo | Espace <?= ucfirst($currentUser['role']) ?></h1>
        <div>
            <span>Bonjour, <?= htmlspecialchars($currentUser['nom']) ?></span>
            <a href="logout.php" class="logout-btn" style="margin-left: 15px;">Déconnexion</a>
        </div>
    </div>

    <div class="container">
        <?php if ($message): ?>
            <p style="background: #e3f2fd; padding: 10px; border-left: 4px solid #2196F3;"><?= $message ?></p>
        <?php endif; ?>

        <?php if ($currentUser['role'] === 'admin'): ?>
            <h2>Gestion des Utilisateurs</h2>
            <p>En tant qu'administrateur, vous pouvez activer ou désactiver les accès.</p>

            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['nom'] ?? 'Inconnu') ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <span class="role-badge role-<?= $u['role'] ?>"><?= $u['role'] ?></span>
                        </td>
                        <td>
                            <?php if ($u['isActive']): ?>
                                <span class="status-active">Actif</span>
                            <?php else: ?>
                                <span class="status-inactive">Désactivé</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($u['id'] !== $currentUser['id']): // Pas de bouton sur soi-même ?>
                                <form method="POST" style="margin:0;">
                                    <input type="hidden" name="action" value="toggle_active">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    
                                    <?php if ($u['isActive']): ?>
                                        <button type="submit" class="action-btn btn-disable">Désactiver</button>
                                    <?php else: ?>
                                        <button type="submit" class="action-btn btn-enable">Activer</button>
                                    <?php endif; ?>
                                </form>
                            <?php else: ?>
                                <small>(Vous)</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php else: ?>
            <h2>Bienvenue sur votre tableau de bord !</h2>
            <p>Contenu pour le rôle : <strong><?= $currentUser['role'] ?></strong> à venir...</p>
        <?php endif; ?>
    </div>

</body>
</html>