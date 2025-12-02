<?php
session_start();

// Si l'utilisateur est déjà connecté, on l'envoie direct au dashboard
if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

// Configuration
$jsonFile = __DIR__ . '/../database/users.json';
$users = [];

// Chargement des utilisateurs
$users = json_decode(file_get_contents($jsonFile), true) ?? [];

$message = "";
$msgType = ""; 

// --- TRAITEMENT DU FORMULAIRE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // CONNEXION
    if (isset($_POST['action']) && $_POST['action'] === 'login') {
        $email = $_POST['email'] ?? '';
        $pass  = $_POST['password'] ?? '';
        $userFound = null;

        foreach ($users as $u) {
            if ($u['email'] === $email && password_verify($pass, $u['password'])) {
                $userFound = $u;
                break;
            }
        }

        if ($userFound) {
            // On vérifie si le compte est actif
            if (isset($userFound['isActive']) && $userFound['isActive'] === false) {
                $message = "Votre compte a été désactivé par l'administrateur.";
                $msgType = "error";
            } else {
                // Connexion réussie
                $_SESSION['user'] = $userFound;
                header('Location: dashboard.php'); 
                exit;
            }
        } else {
            $message = "Email ou mot de passe incorrect.";
            $msgType = "error";
        }
    }

    // INSCRIPTION
    elseif (isset($_POST['action']) && $_POST['action'] === 'register') {
        
        // Vérification Captcha
        if (intval($_POST['captcha']) !== $_SESSION['captcha_result']) {
            $message = "Captcha incorrect !";
            $msgType = "error";
        } else {
            // Vérifier si l'email existe déjà
            $emailExists = false;
            foreach ($users as $u) {
                if ($u['email'] === $_POST['email']) {
                    $emailExists = true; 
                    break;
                }
            }

            if ($emailExists) {
                $message = "Cet email est déjà utilisé.";
                $msgType = "error";
            } else {
                // Création du nouvel utilisateur
                $newUser = [
                    'id' => uniqid(),
                    'nom' => htmlspecialchars($_POST['nom']),
                    'email' => htmlspecialchars($_POST['email']),
                    'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                    'role' => $_POST['role'],
                    'isActive' => true
                ];

                $users[] = $newUser;
                
                // Sauvegarde
                file_put_contents($jsonFile, json_encode($users, JSON_PRETTY_PRINT));

                $message = "Compte créé ! Connectez-vous.";
                $msgType = "success";
                
                // Reset captcha
                unset($_SESSION['captcha_result']);
            }
        }
    }
}

if (!isset($_SESSION['captcha_result'])) {
    $n1 = rand(1, 10);
    $n2 = rand(1, 10);
    $_SESSION['captcha_challenge'] = "$n1 + $n2";
    $_SESSION['captcha_result'] = $n1 + $n2;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Quizzeo - Login</title>
    <style>
        /* RESET & BODY - Pour bien centrer le tout sur la page */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f0f0;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            /* Pas de flex-direction column ici pour laisser le centrage se faire naturellement */
        }

        /* VARIABLES DE COULEURS */
        .wrapper {
            --input-focus: #2d8cf0;
            --font-color: #323232;
            --font-color-sub: #666;
            --bg-color: #fff;
            --bg-color-alt: #666;
            --main-color: #323232;
            /* On s'assure que le wrapper ne bloque pas */
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 50px; /* Marge de sécurité haut */
            padding-bottom: 50px; /* Marge de sécurité bas */
        }

        /* LE CONTENEUR DU SWITCH */
        .switch {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center; 
            /* IMPORTANT : On retire la hauteur fixe de 20px qui écrasait tout */
            /* width: 50px;  <-- On retire ça */
            /* height: 20px; <-- On retire ça */
        }

        /* LE DESIGN DU BOUTON (SLIDER) */
        /* On crée une fausse boite pour le slider pour garder le design */
        .slider {
            position: relative; /* Changé de absolute à relative pour qu'il prenne sa place */
            width: 50px;
            height: 20px;
            box-sizing: border-box;
            border-radius: 5px;
            border: 2px solid var(--main-color);
            box-shadow: 4px 4px var(--main-color);
            background-color: var(--bg-color);
            transition: 0.3s;
            cursor: pointer;
            z-index: 10; /* Pour être sûr qu'il est cliquable au dessus de la carte */
        }

        /* Le petit carré dans le slider */
        .slider:before {
            box-sizing: border-box;
            position: absolute;
            content: "";
            height: 16px; /* Ajusté pour rentrer dans les 20px */
            width: 16px;
            border: 2px solid var(--main-color);
            border-radius: 5px;
            left: 0px;
            bottom: 0px; /* Ajustement fin */
            background-color: var(--bg-color);
            box-shadow: 0 3px 0 var(--main-color);
            transition: 0.3s;
        }

        .toggle { display: none; } /* On cache la checkbox brute */

        /* Animation du slider */
        .toggle:checked + .slider { background-color: var(--input-focus); }
        .toggle:checked + .slider:before { transform: translateX(30px); }

        /* LES TEXTES "LOG IN / SIGN UP" À CÔTÉ DU BOUTON */
        .card-side::before {
            position: absolute; content: 'Log in'; 
            top: 0; left: 25px; width: 70px; /* Position ajustée */
            text-decoration: underline; color: var(--font-color); font-weight: 600; text-align: right;
        }
        .card-side::after {
            position: absolute; content: 'Sign up'; 
            top: 0; right: 25px; width: 70px; /* Position ajustée */
            text-decoration: none; color: var(--font-color); font-weight: 600; text-align: left;
        }

        /* Changement du soulignement des textes */
        .toggle:checked ~ .card-side:before { text-decoration: none; }
        .toggle:checked ~ .card-side:after { text-decoration: underline; }

        /* LA CARTE (LA GROSSE PARTIE) */
        .flip-card__inner {
            width: 300px;
            height: 480px; /* Hauteur suffisante pour tout contenir */
            position: relative;
            background-color: transparent;
            perspective: 1000px;
            text-align: center;
            transition: transform 0.8s;
            transform-style: preserve-3d;
            
            /* C'EST ICI LA CLÉ DU PROBLÈME : */
            margin-top: 40px; /* On pousse la carte vers le bas pour ne pas toucher le bouton */
        }

        .toggle:checked ~ .flip-card__inner { transform: rotateY(180deg); }
        .toggle:checked ~ .flip-card__front { box-shadow: none; }

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
            box-sizing: border-box; /* Important pour que le padding ne casse pas la largeur */
        }

        .flip-card__back { transform: rotateY(180deg); }

        /* FORMULAIRES & INPUTS */
        .flip-card__form { display: flex; flex-direction: column; align-items: center; gap: 15px; width: 100%; }
        .title { margin: 10px 0; font-size: 25px; font-weight: 900; color: var(--main-color); }
        
        .flip-card__input {
            width: 100%; /* Prend toute la largeur dispo */
            max-width: 250px;
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
        
        .flip-card__input::placeholder { color: var(--font-color-sub); opacity: 0.8; }
        .flip-card__input:focus { border: 2px solid var(--input-focus); }
        
        .flip-card__btn {
            margin: 10px 0;
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
        .flip-card__btn:active { box-shadow: 0px 0px var(--main-color); transform: translate(3px, 3px); }

        /* STYLES SPÉCIFIQUES */
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
        
        .captcha-label { font-weight: bold; font-size: 14px; margin-bottom: -10px; color: #323232; }
        select.flip-card__input { cursor: pointer; }

    </style>
</head>
<body>

    <?php if ($message): ?>
        <div class="alert <?= $msgType ?>"><?= $message ?></div>
    <?php endif; ?>

    <div class="wrapper">
        <div class="card-switch">
            <label class="switch">
                <input type="checkbox" class="toggle">
                <span class="slider"></span>
                <span class="card-side"></span>
                
                <div class="flip-card__inner">
                    
                    <div class="flip-card__front">
                        <div class="title">Log in</div>
                        <form class="flip-card__form" action="" method="POST">
                            <input type="hidden" name="action" value="login">
                            <input class="flip-card__input" name="email" placeholder="Email" type="email" required>
                            <input class="flip-card__input" name="password" placeholder="Password" type="password" required>
                            <button class="flip-card__btn">Let's go!</button>
                        </form>
                    </div>
                    
                    <div class="flip-card__back">
                        <div class="title">Sign up</div>
                        <form class="flip-card__form" action="" method="POST">
                            <input type="hidden" name="action" value="register">
                            
                            <input class="flip-card__input" name="nom" placeholder="Name" type="text" required>
                            <input class="flip-card__input" name="email" placeholder="Email" type="email" required>
                            <input class="flip-card__input" name="password" placeholder="Password" type="password" required>
                            
                            <select class="flip-card__input" name="role" required>
                                <option value="" disabled selected>Choisir un rôle</option>
                                <option value="user">Utilisateur Simple</option>
                                <option value="ecole">Ecole</option>
                                <option value="entreprise">Entreprise</option>
                            </select>

                            <label class="captcha-label">Calcul : <?= $_SESSION['captcha_challenge'] ?> = ?</label>
                            <input class="flip-card__input" name="captcha" placeholder="Réponse ?" type="number" required>

                            <button class="flip-card__btn">Confirm!</button>
                        </form>
                    </div>

                </div>
            </label>
        </div>   
    </div>

</body>
</html>