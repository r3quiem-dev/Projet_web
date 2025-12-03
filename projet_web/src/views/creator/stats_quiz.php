<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques du Quiz</title>
    <style>
        /* CHARTE GRAPHIQUE */
        :root {
            --input-focus: #2d8cf0;
            --font-color: #323232;
            --bg-color: #fff;
            --main-color: #323232;
            --shadow: 4px 4px var(--main-color);
            --accent-blue: #cce5ff;
            --accent-green: #d4edda;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f0f0;
            color: var(--font-color);
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        .container {
            max-width: 900px;
            width: 100%;
            background: var(--bg-color);
            padding: 30px;
            border-radius: 8px;
            border: 2px solid var(--main-color);
            box-shadow: var(--shadow);
        }

        /* HEADER */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px dashed #ccc;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        h1 { margin: 0; font-size: 1.8em; }
        .subtitle { color: #666; margin-top: 5px; }

        /* BOUTON RETOUR */
        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-weight: 600;
            text-decoration: none;
            color: var(--font-color);
            background: #fff;
            border: 2px solid var(--main-color);
            border-radius: 5px;
            box-shadow: var(--shadow);
            transition: 0.1s;
        }
        .btn:active { box-shadow: 0 0 0; transform: translate(2px, 2px); }

        /* CARD INFO PARTICIPANTS */
        .info-card {
            background: var(--accent-blue);
            border: 2px solid var(--main-color);
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 30px;
            box-shadow: 2px 2px 0 var(--main-color);
        }

        /* QUESTION BLOCK */
        .question-box {
            background: #fff;
            border: 2px solid var(--main-color);
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 2px 2px 0 #ddd;
        }

        .question-title {
            font-size: 1.1em;
            font-weight: 700;
            margin-bottom: 15px;
            border-left: 5px solid var(--input-focus);
            padding-left: 10px;
        }

        /* BARRES DE PROGRESSION */
        .answer-row { margin-bottom: 15px; }
        
        .answer-label {
            display: flex;
            justify-content: space-between;
            font-size: 0.95em;
            margin-bottom: 5px;
        }

        .progress-track {
            width: 100%;
            height: 20px;
            background: #f0f0f0;
            border: 2px solid var(--main-color);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: #28a745; /* Vert */
            border-right: 2px solid var(--main-color); /* Petite bordure à la fin de la barre */
            transition: width 0.5s ease-in-out;
        }

        .text-libre {
            font-style: italic;
            color: #777;
            background: #f9f9f9;
            padding: 10px;
            border: 1px dashed #aaa;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    
    <div class="header">
        <div>
            <h1>Statistiques</h1>
            <div class="subtitle">Quiz : <strong><?= htmlspecialchars($quiz['titre']) ?></strong></div>
        </div>
        <a href="index.php?route=dashboard" class="btn">Retour</a>
    </div>

    <div class="info-card">
        Total Participants : <?= $statsData['total'] ?>
    </div>

    <?php foreach($quiz['questions'] as $index => $q): ?>
        <div class="question-box">
            <div class="question-title">
                Question <?= $index + 1 ?> : <?= htmlspecialchars($q['texte']) ?>
            </div>
            
            <?php if ($q['type'] === 'libre'): ?>
                
                <div class="text-libre">
                    <strong>Réponses des participants :</strong>
                    <ul style="margin-top: 10px; padding-left: 20px;">
                        <?php 
                        $reponsesTextuelles = $statsData['text'][$q['id']] ?? [];
                        
                        if (empty($reponsesTextuelles)): ?>
                            <li style="color: #888;">Aucune réponse textuelle enregistrée.</li>
                        <?php else: ?>
                            <?php foreach($reponsesTextuelles as $text): ?>
                                <li style="margin-bottom: 5px;">"<?= $text ?>"</li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>

            <?php else: ?>
                <?php foreach($q['choix'] as $c): ?>
                    <?php 
                        $count = $statsData['data'][$q['id']][$c['id']] ?? 0;
                        // Eviter division par zéro
                        $percent = ($statsData['total'] > 0) ? round(($count / $statsData['total']) * 100) : 0;
                    ?>
                    <div class="answer-row">
                        <div class="answer-label">
                            <span><?= htmlspecialchars($c['texte']) ?></span>
                            <strong><?= $percent ?>% <small style="font-weight:normal; color:#666;">(<?= $count ?> votes)</small></strong>
                        </div>
                        
                        <div class="progress-track">
                            <div class="progress-fill" style="width: <?= $percent ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

</div>

</body>
</html>