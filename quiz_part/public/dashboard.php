<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté (variable de session 'loggedin' à true)
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    // S'il n'est pas connecté, le rediriger vers la page de connexion
    header("location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord</title>
    <style>
        body { font-family: sans-serif; text-align: center; padding: 50px; background-color: #e6ffe6; }
        .welcome-box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 15px rgba(0,100,0,0.2); display: inline-block; }
        h1 { color: #008000; }
        .details { text-align: left; margin-top: 20px; }
        .details p { margin: 5px 0; }
        .logout-btn { background-color: #ff4d4d; color: white; padding: 10px 20px; margin-top: 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .logout-btn:hover { background-color: #cc0000; }
    </style>
</head>
<body>
    <div class="welcome-box">
        <h1>Bienvenue sur votre Tableau de Bord !</h1>
        <p>Ceci est la page sécurisée après une connexion réussie.</p>
        
        <div class="details">
            <h3>Vos informations :</h3>
            <p><strong>ID Utilisateur :</strong> <?php echo htmlspecialchars($_SESSION["id_utilisateur"]); ?></p>
            <p><strong>Nom :</strong> <?php echo htmlspecialchars($_SESSION["nom"]); ?></p>
            <p><strong>Prénom :</strong> <?php echo htmlspecialchars($_SESSION["prenom"]); ?></p>
        </div>

        <a href="input_quiz.php" class="logout-btn">add quiz</a>
        <a href="logout.php" class="logout-btn">Se Déconnecter</a>
    </div>
</body>
</html>