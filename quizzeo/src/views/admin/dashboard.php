<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

    <div class="navbar">
        <div>
            <img src="img/projet_web_logo.png" alt="Quizzeo" style="height: 50px; vertical-align: middle;">
        </div>
        <div class="user-info">
            <a href="index.php?route=profile" class="btn-profile">Mon Profil</a>
            <a href="index.php?route=logout" class="btn-logout">Déconnexion</a>
        </div>
    </div>

    <div class="container">
        <div class="box accordion">
            <div class="accordion-header">
                <h2>Utilisateurs</h2>
                <span class="arrow">▼</span>
            </div>
            <div class="accordion-content">
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
                                       <?= $u['is_active'] ? 'ACTIF' : 'INACTIF' ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="box accordion">
            <div class="accordion-header">
                <h2>Quiz</h2>
                <span class="arrow">▼</span>
            </div>
            <div class="accordion-content">
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
    </div>

    <script>
        document.querySelectorAll('.accordion-header').forEach(header => {
            header.addEventListener('click', () => {
                // On remonte au parent (.box) pour lui ajouter/enlever la classe "open"
                const box = header.parentElement;
                box.classList.toggle('open');
            });
        });
    </script>

</body>
</html>