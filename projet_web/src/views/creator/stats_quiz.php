<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques du Quiz</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/quiz.css">
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