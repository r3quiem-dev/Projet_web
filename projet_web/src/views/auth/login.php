<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion / Inscription</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f0f0;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            flex-direction: column;
        }

        /* ALERTS */
        .alert {
            position: absolute;
            top: 20px;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            border: 2px solid #323232;
            box-shadow: 4px 4px #323232;
            z-index: 100;
        }
        .error { background-color: #ffcccc; color: #cc0000; }

        .success { background-color: #ccffcc; color: #006600; }

        /* DESIGN UIVERSE NEUBRUTALISM */
        .wrapper {
            --input-focus: #2d8cf0;
            --font-color: #323232;
            --font-color-sub: #666;
            --bg-color: #fff;
            --bg-color-alt: #666;
            --main-color: #323232;
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 50px;
        }

        .switch {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .slider {
            position: relative;
            width: 50px; height: 20px;
            border-radius: 5px;
            border: 2px solid var(--main-color);
            box-shadow: 4px 4px var(--main-color);
            background-color: var(--bg-color);
            transition: 0.3s;
            cursor: pointer;
            z-index: 10;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            border: 2px solid var(--main-color);
            border-radius: 5px;
            left: 0px;
            bottom: 0px;
            background-color: var(--bg-color);
            box-shadow: 0 3px 0 var(--main-color);
            transition: 0.3s;
        }
        .toggle {
            display: none;
        }
        .toggle:checked + .slider {
            background-color: var(--input-focus);
        }
        .toggle:checked + .slider:before {
            transform: translateX(30px);
        }

        /* TEXTES */
        .card-side::before {
            position: absolute;
            content: 'Log in';
            top: 0; left: 20px;
            width: 60px;
            text-decoration: underline;
            color: var(--font-color);
            font-weight: 600;
            text-align: right;
        }
        .card-side::after {
            position: absolute;
            content: 'Sign up';
            top: 0;
            right: 20px;
            width: 60px;
            text-decoration: none;
            color: var(--font-color);
            font-weight: 600;
            text-align: left;
        }
        .toggle:checked ~ .card-side:before {
            text-decoration: none;
        }
        .toggle:checked ~ .card-side:after {
            text-decoration: underline;
        }

        /* CARTE FLIP */
        .flip-card__inner {
            width: 300px;
            height: 500px;
            position: relative;
            background-color: transparent;
            perspective: 1000px;
            text-align: center;
            transition: transform 0.8s;
            transform-style: preserve-3d;
            margin-top: 40px;
        }
        .toggle:checked ~ .flip-card__inner {
            transform: rotateY(180deg);
        }
        
        .flip-card__front, .flip-card__back {
            padding: 20px;
            position: absolute;
            display: flex;
            flex-direction: column;
            justify-content: center;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            background: lightgrey;
            gap: 15px;
            border-radius: 5px;
            border: 2px solid var(--main-color);
            box-shadow: 4px 4px var(--main-color);
            width: 100%;
            height: 100%;
            box-sizing: border-box;
        }
        .flip-card__back {
            transform: rotateY(180deg);
        }

        .flip-card__form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            width: 100%;
        }
        .title {
            margin: 10px 0;
            font-size: 25px;
            font-weight: 900;
            color: var(--main-color);
        }
        
        .flip-card__input {
            width: 100%;
            height: 40px;
            border-radius: 5px;
            border: 2px solid var(--main-color);
            background-color: var(--bg-color);
            box-shadow: 4px 4px var(--main-color);
            font-size: 15px;
            font-weight: 600;
            color: var(--font-color);
            padding: 5px 10px;
            outline: none;
            box-sizing: border-box;
        }
        .flip-card__input:focus {
            border: 2px solid var(--input-focus);
        }
        
        .flip-card__btn {
            width: 120px;
            height: 40px;
            border-radius: 5px;
            border: 2px solid var(--main-color);
            background-color: var(--bg-color);
            box-shadow: 4px 4px var(--main-color);
            font-size: 17px;
            font-weight: 600;
            color: var(--font-color);
            cursor: pointer;
        }
        .flip-card__btn:active {
            box-shadow: 0px 0px var(--main-color);
            transform: translate(3px, 3px);
        }
        
        select.flip-card__input {
            cursor: pointer;
        }

        /* Ajustement Captcha */
        .g-recaptcha {
            transform: scale(0.85);
            transform-origin: center;
            margin: 10px 0;
            display: flex;
            justify-content: center;
        }
    </style>
</head>
<body>

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