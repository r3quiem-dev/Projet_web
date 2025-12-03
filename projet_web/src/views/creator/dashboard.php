<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Quizzeo</title>
    <style>
        :root {
            --input-focus: #2d8cf0;
            --font-color: #323232;
            --font-color-sub: #666;
            --bg-color: #fff;
            --main-color: #323232;
            --shadow: 4px 4px var(--main-color);
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f0f0;
            color: var(--font-color);
            margin: 0;
            padding: 20px;
        }

        /* NAVBAR */
        .navbar {
            background: var(--bg-color);
            border: 2px solid var(--main-color);
            box-shadow: var(--shadow);
            border-radius: 8px;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .navbar-brand {
            font-weight: 900;
            font-size: 1.5em;
            letter-spacing: 1px;
            color: var(--main-color);
            text-transform: uppercase;
        }

        .user-info {
            display: flex;
            align-items: center;
            font-weight: 600;
        }

        /* BOUTONS ACTIONS NAVBAR */
        .btn-profile {
            background: #ffffff;
            color: var(--main-color);
            padding: 10px 15px;
            border: 2px solid var(--main-color);
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 2px 2px 0 var(--main-color);
            transition: all 0.1s;
            margin-right: 10px;
        }
        .btn-profile:hover { background: #f9f9f9; }
        .btn-profile:active { box-shadow: 0 0 0; transform: translate(2px, 2px); }

        .btn-logout {
            background: #ff4d4d;
            color: white;
            padding: 10px 15px;
            border: 2px solid var(--main-color);
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 2px 2px 0 var(--main-color);
            transition: all 0.1s;
        }
        .btn-logout:active { box-shadow: 0 0 0; transform: translate(2px, 2px); }
        .btn-logout:hover { background: #ff3333; }

        /* CONTAINER */
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: var(--bg-color);
            padding: 30px;
            border-radius: 8px;
            border: 2px solid var(--main-color);
            box-shadow: var(--shadow);
        }

        h2 { border-bottom: 2px dashed #ddd; padding-bottom: 10px; margin-top: 0; }

        /* BOUTON CRÉER */
        .btn-green {
            display: inline-block;
            padding: 10px 20px;
            font-weight: 600;
            text-decoration: none;
            color: var(--font-color);
            background: #ccffcc;
            border: 2px solid var(--main-color);
            border-radius: 5px;
            box-shadow: var(--shadow);
            cursor: pointer;
            transition: all 0.1s;
        }
        .btn-green:active { box-shadow: 0 0 var(--main-color); transform: translate(2px, 2px); }

        /* TABLEAU */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 20px;
            border: 2px solid var(--main-color);
            border-radius: 5px;
            overflow: hidden;
        }

        th {
            background: var(--main-color);
            color: white;
            padding: 15px;
            text-align: left;
        }

        td { padding: 15px; border-bottom: 2px solid var(--main-color); }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background-color: #f9f9f9; }

        /* PETITS BOUTONS ACTIONS TABLEAU */
        .btn-action { font-size: 0.9em; padding: 5px 10px; border: 2px solid var(--main-color); border-radius: 5px; text-decoration: none; color: black; font-weight: bold; box-shadow: 2px 2px 0 var(--main-color); display: inline-block; margin-left: 5px; }
        .btn-action:active { box-shadow: 0 0 0; transform: translate(2px, 2px); }
        .bg-red { background: #ffcccc; }
        .bg-yellow { background: #ffffcc; }
        .bg-orange { background: #ffe5b4; }
        .bg-grey { background: #e2e6ea; }

        /* BADGES STATUT */
        .badge {
            padding: 5px 10px; border-radius: 5px; border: 2px solid var(--main-color);
            font-weight: bold; font-size: 0.8em; display: inline-block;
            box-shadow: 2px 2px 0 rgba(0,0,0,0.1);
        }
        .status-writing { background: #fff3cd; }
        .status-launched { background: #d4edda; }
        .status-finished { background: #f8d7da; }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="navbar-brand">QUIZZEO</div>
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
            <a href="index.php?route=create_quiz" class="btn-green">+ Créer un nouveau quiz</a>
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
                            👥 <?= $quiz['nb_reponses'] ?>
                        </td>

                        <td><?= date('d/m/Y', strtotime($quiz['date_creation'])) ?></td>
                        
                        <td style="text-align: right;">
                            <?php 
                                // Si entreprise -> Stats, sinon -> Résultats
                                $routeResults = ($user['role'] === 'entreprise') ? 'results_quiz' : 'results_quiz'; 
                                // (Le contrôleur fera la distinction d'affichage)
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