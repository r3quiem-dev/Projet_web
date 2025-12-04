<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion / Inscription</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/auth.css">
</head>
<body class="auth-page">

    <?php if (!empty($error)): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert success"><?= $success ?></div>
    <?php endif; ?>

    <div class="wrapper">
        <div class="switch">
            <input type="checkbox" class="toggle" id="toggle">
            <label for="toggle" class="slider"></label>
            <span class="card-side"></span>
            
            <div class="flip-card__inner">
                
                <div class="flip-card__front">
                    <img src="img/projet_web_logo.png" alt="Quizzeo" style="height: 60px; margin-bottom: 10px; display: block; margin-left: auto; margin-right: auto;">
                    
                    <div class="title">Log in</div>
                    <form class="flip-card__form" method="POST" action="index.php?route=login">
                        <input type="hidden" name="action" value="login">
                        
                        <input class="flip-card__input" name="email" placeholder="Email" type="email" required>
                        <input class="flip-card__input" name="password" placeholder="Password" type="password" required>
                        <button class="flip-card__btn">Let's go!</button>
                    </form>
                </div>
                
                <div class="flip-card__back">
                    <div class="title">Sign up</div>
                    <form class="flip-card__form" method="POST" action="index.php?route=login">
                        <input type="hidden" name="action" value="register">
                        
                        <input class="flip-card__input" name="nom" placeholder="Name" type="text" required>
                        <input class="flip-card__input" name="email" placeholder="Email" type="email" required>
                        <input class="flip-card__input" name="password" placeholder="Password" type="password" required>
                        
                        <select class="flip-card__input" name="role" required>
                            <option value="" disabled selected>Choisir un rôle</option>
                            <option value="user">Utilisateur</option>
                            <option value="ecole">Ecole</option>
                            <option value="entreprise">Entreprise</option>
                        </select>

                        <div class="g-recaptcha" data-sitekey="6Le5Rx8sAAAAAPwb9gGvdVigqDlscQEjDoUlhkUB"></div>

                        <button class="flip-card__btn">Confirm!</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/../footer.php'; ?>

</body>
</html>