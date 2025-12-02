<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultats du Quiz</title>
    <style>
        :root { --main-color: #323232; --bg-color: #fff; --shadow: 4px 4px var(--main-color); }
        body { font-family: 'Segoe UI', sans-serif; background-color: #f0f0f0; color: var(--main-color); padding: 20px; display: flex; justify-content: center; }
        
        .container { max-width: 1000px; width: 100%; background: var(--bg-color); padding: 30px; border-radius: 8px; border: 2px solid var(--main-color); box-shadow: var(--shadow); }
        
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px dashed #ddd; padding-bottom: 20px; margin-bottom: 20px; }
        .btn { padding: 8px 15px; background: #fff; border: 2px solid var(--main-color); text-decoration: none; color: var(--main-color); font-weight: bold; box-shadow: 2px 2px 0 var(--main-color); }
        .btn:active { box-shadow: 0 0 0; transform: translate(2px, 2px); }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: var(--main-color); color: white; padding: 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #ddd; }
        tr:hover { background: #f9f9f9; }

        .score-bad { color: #dc3545; font-weight: bold; }
        .score-good { color: #28a745; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div>
            <h1 style="margin: 0;">Résultats</h1>
            <p style="margin: 5px 0; color: #666;">Quiz : <strong><?= htmlspecialchars($quiz['titre']) ?></strong></p>
        </div>
        <a href="index.php?route=dashboard" class="btn">Retour</a>
    </div>

    <?php if (empty($results)): ?>
        <div style="text-align: center; padding: 40px; color: #888;">
            <h3>Aucun résultat pour le moment 📉</h3>
            <p>Attendez que des utilisateurs répondent à votre quiz.</p>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Nom de l'élève</th>
                    <th>Email</th>
                    <th>Score</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['nom']) ?></td>
                    <td><?= htmlspecialchars($r['email']) ?></td>
                    <td>
                        <?php 
                            $isGood = ($r['score'] >= $r['total_questions'] / 2);
                            $class = $isGood ? 'score-good' : 'score-bad';
                        ?>
                        <span class="<?= $class ?>"><?= $r['score'] ?> / <?= $r['total_questions'] ?></span>
                    </td>
                    <td><?= date('d/m/Y H:i', strtotime($r['date_participation'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>