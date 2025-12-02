<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Créateur</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f4f4; padding: 0; margin: 0; }
        .navbar { background: #333; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        .container { max-width: 1000px; margin: 30px auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        
        /* LE BOUTON CRÉER */
        .btn-create {
            background-color: #28a745; color: white; padding: 10px 20px; 
            text-decoration: none; border-radius: 5px; font-weight: bold;
            display: inline-block;
        }
        .btn-create:hover { background-color: #218838; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        .badge { padding: 5px 10px; border-radius: 12px; color: white; font-size: 0.8em; font-weight: bold;}
        .status-writing { background: #ffc107; color: #333; }
        .status-launched { background: #28a745; }
        .status-finished { background: #dc3545; }
    </style>
</head>
<body>

    <div class="navbar">
        <div style="font-weight: bold; font-size: 1.2em;">Quizzeo | Espace <?= ucfirst($user['role']) ?></div>
        <div>
            <span>Bonjour, <?= htmlspecialchars($user['nom']) ?></span>
            <a href="logout.php" style="color: #ff6b6b; margin-left: 15px; text-decoration: none;">Déconnexion</a>
        </div>
    </div>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Mes Quiz</h2>
            <a href="create_quiz.php" class="btn-create">+ Créer un nouveau quiz</a>
        </div>

        <?php if (empty($myQuizzes)): ?>
            <div style="text-align: center; padding: 40px; color: #666;">
                <p>Vous n'avez pas encore créé de quiz.</p>
                <p>Cliquez sur le bouton vert pour commencer !</p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Statut</th>
                        <th>Date de création</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($myQuizzes as $quiz): ?>
                    <tr>
                        <td><?= htmlspecialchars($quiz['titre']) ?></td>
                        <td>
                            <?php 
                                $statusClass = 'status-writing';
                                if ($quiz['status'] === 'lancé') $statusClass = 'status-launched';
                                if ($quiz['status'] === 'terminé') $statusClass = 'status-finished';
                            ?>
                            <span class="badge <?= $statusClass ?>"><?= $quiz['status'] ?></span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($quiz['date_creation'])) ?></td>
                        <td>
                            <a href="edit_quiz.php?id=<?= $quiz['id'] ?>" style="color: #007bff; text-decoration: none; font-weight: bold;">Modifier</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</body>
</html>