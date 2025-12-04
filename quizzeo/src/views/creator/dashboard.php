<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Quizzeo</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

    <div class="navbar">
        <div>
            <img src="img/projet_web_logo.png" alt="Quizzeo" style="height: 50px; vertical-align: middle;">
        </div>
        <div class="user-info">
            <span style="margin-right: 20px;"><?= htmlspecialchars($user['nom']) ?> <small>(<?= ucfirst($user['role']) ?>)</small></span>
            <a href="index.php?route=profile" class="btn-profile">Mon Profil</a>
            <a href="index.php?route=logout" class="btn-logout">Déconnexion</a>
        </div>
    </div>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Mes Quiz</h2>
            <a href="index.php?route=create_quiz" class="btn btn-green">+ Créer un nouveau quiz</a>
        </div>

        <?php if (empty($myQuizzes)): ?>
            <div style="text-align: center; padding: 50px; color: #666;">
                <h3>C'est vide ici...</h3>
                <p>Créez votre quiz</p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Statut</th>
                        <th style="text-align: center;">Réponses</th> <th>Date</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($myQuizzes as $quiz): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($quiz['titre']) ?></strong></td>
                        <td>
                            <?php 
                                $s = $quiz['status'];
                                $cls = ($s === 'lancé') ? 'status-launched' : (($s === 'terminé') ? 'status-finished' : 'status-writing');
                            ?>
                            <span class="badge <?= $cls ?>"><?= ucfirst($s) ?></span>
                        </td>
                        
                        <td style="text-align: center; font-weight: bold; font-size: 1.1em;">
                            <?= $quiz['nb_reponses'] ?>
                        </td>

                        <td><?= date('d/m/Y', strtotime($quiz['date_creation'])) ?></td>
                        
                        <td style="text-align: right;">
                            <?php 
                                $routeResults = ($user['role'] === 'entreprise') ? 'results_quiz' : 'results_quiz'; 
                            ?>
                            <a href="index.php?route=results_quiz&id=<?= $quiz['id'] ?>" class="btn-action bg-grey" title="Voir les résultats">📊</a>

                            <?php if ($quiz['status'] !== 'lancé'): ?>
                                <a href="index.php?route=publish_quiz&id=<?= $quiz['id'] ?>" class="btn-action bg-orange" onclick="return confirm('Publier ?')">🚀</a>
                            <?php endif; ?>
                            
                            <a href="index.php?route=edit_quiz&id=<?= $quiz['id'] ?>" class="btn-action bg-yellow">✏️</a>
                            <a href="index.php?route=delete_quiz&id=<?= $quiz['id'] ?>" class="btn-action bg-red" onclick="return confirm('Supprimer ?')">🗑️</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <?php require_once __DIR__ . '/../footer.php'; ?>

</body>
</html>