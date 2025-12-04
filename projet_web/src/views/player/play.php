<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Quiz</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/quiz.css">
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
                    <h3>Question <?= $index + 1 ?> <small style="font-weight:normal; font-size:0.8em;">(<?= strtoupper($q['type']) ?>)</small></h3>
                    <p style="font-size: 1.1em; font-weight: 500;"><?= htmlspecialchars($q['texte']) ?></p>
                    
                    <?php if ($q['type'] === 'qcm'): ?>
                        <?php foreach ($q['choix'] as $c): ?>
                            <label class="choice-label">
                                <input type="checkbox" name="reponses[<?= $q['id'] ?>][]" value="<?= $c['id'] ?>">
                                <span><?= htmlspecialchars($c['texte']) ?></span>
                            </label>
                        <?php endforeach; ?>

                    <?php else: ?>
                        <textarea name="reponses_text[<?= $q['id'] ?>]" rows="3" placeholder="Votre réponse ici..." style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;"></textarea>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn-submit">Valider mes réponses</button>
        </form>
    </div>

</body>
</html>