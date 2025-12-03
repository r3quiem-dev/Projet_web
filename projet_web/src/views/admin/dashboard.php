<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        :root { --main-color: #323232; --bg-color: #fff; --shadow: 4px 4px var(--main-color); }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f0f0; color: var(--main-color); padding: 20px; }
        
        .navbar { background: var(--main-color); color: white; padding: 15px; display: flex; justify-content: space-between; margin-bottom: 30px; box-shadow: var(--shadow); }
        .navbar a { color: #ffcccc; text-decoration: none; font-weight: bold; }

        .container { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; max-width: 1200px; margin: 0 auto; }
        .box { background: white; border: 2px solid var(--main-color); padding: 20px; box-shadow: var(--shadow); }
        
        h2 { border-bottom: 2px dashed #ddd; padding-bottom: 10px; margin-top: 0; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; padding: 5px; border-bottom: 2px solid #333; }
        td { padding: 8px 5px; border-bottom: 1px solid #eee; }

        .btn-toggle { padding: 5px 10px; border: 1px solid #333; text-decoration: none; font-size: 0.8em; font-weight: bold; display: inline-block; }
        .active { background: #d4edda; color: #155724; }
        .inactive { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    <div class="navbar">
        <span>👑 ESPACE ADMIN</span>
        <a href="index.php?route=logout">Déconnexion</a>
        <a href="index.php?route=profile">Mon Profil</a>
    </div>

    <div class="container">
        <div class="box">
            <h2>Utilisateurs</h2>
            <table>
                <thead><tr><th>Nom</th><th>Rôle</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach($users as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['nom']) ?><br><small><?= htmlspecialchars($u['email']) ?></small></td>
                        <td><?= $u['role'] ?></td>
                        <td>
                            <?php if($u['role'] !== 'admin'): ?>
                                <a href="index.php?route=dashboard&action=toggle_user&id=<?= $u['id'] ?>" 
                                   class="btn-toggle <?= $u['is_active'] ? 'active' : 'inactive' ?>">
                                   <?= $u['is_active'] ? 'ACTIF' : 'BAN' ?>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="box">
            <h2>Quiz</h2>
            <table>
                <thead><tr><th>Titre</th><th>Statut</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach($quizzes as $q): ?>
                    <tr>
                        <td><?= htmlspecialchars($q['titre']) ?><br><small>Par: <?= htmlspecialchars($q['auteur']) ?></small></td>
                        <td><?= $q['status'] ?></td>
                        <td>
                            <a href="index.php?route=dashboard&action=toggle_quiz&id=<?= $q['id'] ?>" 
                               class="btn-toggle <?= ($q['status'] == 'lancé') ? 'active' : 'inactive' ?>">
                               <?= ($q['status'] == 'lancé') ? 'ON' : 'OFF' ?>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>