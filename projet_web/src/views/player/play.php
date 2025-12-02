<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Jeu en cours</title>
    <style>
        :root { --main-color: #323232; --shadow: 4px 4px var(--main-color); }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f0f0; padding: 20px; display: flex; justify-content: center; }
        
        .container { max-width: 800px; width: 100%; background: white; padding: 30px; border: 2px solid var(--main-color); box-shadow: var(--shadow); }
        
        .question-box { background: #f9f9f9; border: 2px solid var(--main-color); padding: 20px; margin-bottom: 20px; border-radius: 5px; }
        
        /* Style pour les choix */
        .choice-label { display: flex; align-items: center; gap: 10px; padding: 10px; border: 1px solid #ddd; margin-bottom: 5px; cursor: pointer; transition: 0.2s; }
        .choice-label:hover { background: #e2e6ea; border-color: var(--main-color); }
        
        /* Checkbox personnalisée */
        input[type="checkbox"] { width: 20px; height: 20px; accent-color: var(--main-color); cursor: pointer; }

        .btn-submit { width: 100%; padding: 15px; background: #28a745; color: white; font-size: 1.2em; font-weight: bold; border: 2px solid var(--main-color); box-shadow: var(--shadow); cursor: pointer; }
        .btn-submit:active { transform: translate(2px, 2px); box-shadow: 0 0 0; }
    </style>
</head>
<body>

    <div class="container">
        <h1><?= htmlspecialchars($quiz['titre']) ?></h1>
        <p><?= nl2br(htmlspecialchars($quiz['description'])) ?></p>
        <hr style="border: 1px solid var(--main-color); margin: 20px 0;">

        <form action="index.php?route=submit_quiz" method="POST">
            <input type="hidden" name="quiz_id" value="<?= $quiz['id'] ?>">

            <?php foreach ($quiz['questions'] as $index => $q): ?>
                <div class="question-box">
                    <h3>Question <?= $index + 1 ?></h3>
                    <p style="font-size: 1.1em; font-weight: 500;"><?= htmlspecialchars($q['texte']) ?></p>
                    
                    <?php foreach ($q['choix'] as $c): ?>
                        <label class="choice-label">
                            <input type="checkbox" name="reponses[<?= $q['id'] ?>][]" value="<?= $c['id'] ?>">
                            <span><?= htmlspecialchars($c['texte']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn-submit">Valider mes réponses 🚀</button>
        </form>
    </div>

</body>
</html>