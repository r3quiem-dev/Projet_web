<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Élève</title>
    <style>
        :root { --main-color: #323232; --bg-color: #fff; --shadow: 4px 4px var(--main-color); }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f0f0; color: var(--main-color); padding: 20px; margin: 0; }
        
        /* NAVBAR STYLÉE */
        .navbar {
            background: var(--bg-color);
            border: 2px solid var(--main-color);
            box-shadow: var(--shadow);
            border-radius: 8px;
            padding: 15px 25px;
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar-brand { font-weight: 900; font-size: 1.5em; letter-spacing: 1px; }
        
        .btn-logout {
            background: #ff4d4d; color: white; padding: 10px 15px;
            border: 2px solid var(--main-color); border-radius: 5px;
            text-decoration: none; font-weight: bold; box-shadow: 2px 2px 0 var(--main-color);
            transition: 0.1s;
        }
        .btn-logout:active { box-shadow: 0 0 0; transform: translate(2px, 2px); }

        /* GRID QUIZ */
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; max-width: 1000px; margin: 0 auto; }
        
        .card { 
            background: white; border: 2px solid var(--main-color); padding: 20px; 
            box-shadow: var(--shadow); border-radius: 8px; 
            transition: transform 0.1s; display: flex; flex-direction: column; justify-content: space-between; 
        }
        .card:hover { transform: translate(-2px, -2px); box-shadow: 6px 6px var(--main-color); }
        
        .card h3 { margin-top: 0; border-bottom: 2px dashed #ddd; padding-bottom: 10px; }

        .btn-play { 
            display: block; margin-top: 15px; background: #cce5ff; 
            border: 2px solid var(--main-color); padding: 10px; text-align: center; 
            text-decoration: none; color: black; font-weight: bold; box-shadow: 2px 2px 0 var(--main-color); 
        }
        .btn-play:active { box-shadow: 0 0 0; transform: translate(2px, 2px); }
        
        .alert-score { max-width: 1000px; margin: 0 auto 20px auto; background: #d4edda; border: 2px solid #155724; color: #155724; padding: 15px; font-weight: bold; box-shadow: var(--shadow); border-radius: 8px; }
        
        .card-footer { margin-top: 20px; padding-top: 15px; border-top: 2px solid var(--main-color); display: flex; justify-content: space-between; align-items: center; }
        
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
            margin-right: 10px; /* Espace avec le bouton déconnexion */
        }
        .btn-profile:hover { background: #f9f9f9; }
        .btn-profile:active { box-shadow: 0 0 0; transform: translate(2px, 2px); }
    </style>
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
            Bravo ! Score : <?= $_GET['score'] ?> / <?= $_GET['total'] ?>
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