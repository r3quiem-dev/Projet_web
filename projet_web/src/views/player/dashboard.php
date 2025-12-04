<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Élève</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

    <div class="navbar">
        <div>
            <img src="img/projet_web_logo.png" alt="Quizzeo" style="height: 50px; vertical-align: middle;">
        </div>
        <div>
            <span style="margin-right: 15px; font-weight: bold;"><?= htmlspecialchars($_SESSION['user']['nom']) ?></span>
            
            <a href="index.php?route=profile" class="btn-profile">Mon Profil</a>
            
            <a href="index.php?route=logout" class="btn-logout">Déconnexion</a>
        </div>
    </div>

    <?php if (isset($_GET['score'])): ?>
        <div class="alert-score">
            Score : <?= $_GET['score'] ?> / <?= $_GET['total'] ?>
        </div>
    <?php endif; ?>

    <div class="grid">
        <?php if(empty($quizzes)): ?>
            <p>Aucun quiz disponible.</p>
        <?php else: ?>
            <?php foreach($quizzes as $q): ?>
                <div class="card">
                    <div>
                        <h3><?= htmlspecialchars($q['titre']) ?></h3>
                        <p><strong>Auteur :</strong> <?= htmlspecialchars($q['auteur']) ?></p>
                        <p><?= htmlspecialchars($q['description']) ?></p>
                    </div>
                    
                    <?php if (isset($myResults[$q['id']])): ?>
                        <?php 
                            $res = $myResults[$q['id']]; 
                            $colorNote = ($res['score'] >= $res['total_questions'] / 2) ? '#28a745' : '#dc3545';
                        ?>
                        <div class="card-footer" style="background: #f0f0f0;">
                            <span>Terminé</span>
                            <span style="font-size: 1.2em; font-weight: 900; color: <?= $colorNote ?>;">
                                <?= $res['score'] ?> / <?= $res['total_questions'] ?>
                            </span>
                        </div>
                    <?php else: ?>
                        <a href="index.php?route=play_quiz&id=<?= $q['id'] ?>" class="btn-play">Faire le quiz</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php require_once __DIR__ . '/../footer.php'; ?>

</body>
</html>