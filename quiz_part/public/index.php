<!-- <?php

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

session_start(); // <<< IMPORTANT : démarrer la session

// Load .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Running the root
require __DIR__ . '/../src/router.php';
?> -->




<?php
// ----------------------------------------------------
// 1. CONFIGURATION DE LA BASE DE DONNÉES
// ----------------------------------------------------
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Remplacez par votre nom d'utilisateur
define('DB_PASSWORD', '');     // Remplacez par votre mot de passe
define('DB_NAME', 'quizzeo'); // Remplacez par le nom de votre base de données

// Connexion à la base de données MySQL
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion à la base de données : " . $conn->connect_error);
}

$message = ''; // Variable pour stocker les messages de succès ou d'erreur

// ----------------------------------------------------
// 2. TRAITEMENT DU FORMULAIRE D'INSCRIPTION
// ----------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['inscription'])) {
    // Récupération et nettoyage des données
    $nom = $conn->real_escape_string($_POST['nom']);
    $prenom = $conn->real_escape_string($_POST['prenom']);
    $email = $conn->real_escape_string($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];

    // Hachage du mot de passe pour la sécurité
    $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // Préparation de la requête d'insertion
    $sql = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe) VALUES (?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // Liaison des paramètres (s: string)
        $stmt->bind_param("ssss", $nom, $prenom, $email, $mot_de_passe_hache);

        if ($stmt->execute()) {
            $message = "<div style='color: green;'>✅ Inscription réussie ! Vous pouvez maintenant vous connecter.</div>";
        } else {
            // Erreur, souvent due à un email déjà utilisé (contrainte UNIQUE)
            $message = "<div style='color: red;'>❌ Erreur lors de l'inscription : " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}

// ----------------------------------------------------
// 3. TRAITEMENT DU FORMULAIRE DE CONNEXION
// ----------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['connexion'])) {
    session_start(); // Démarre la session pour stocker les infos de l'utilisateur

    $email_connexion = $conn->real_escape_string($_POST['email_connexion']);
    $mot_de_passe_connexion = $_POST['mot_de_passe_connexion'];

    // Préparation de la requête pour récupérer l'utilisateur par email
    $sql = "SELECT id_utilisateur, nom, prenom, mot_de_passe FROM utilisateurs WHERE email = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email_connexion);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $utilisateur = $result->fetch_assoc();
            
            // Vérification du mot de passe haché
            if (password_verify($mot_de_passe_connexion, $utilisateur['mot_de_passe'])) {
                // Mot de passe correct, démarrer la session
                $_SESSION['loggedin'] = true;
                $_SESSION['id_utilisateur'] = $utilisateur['id_utilisateur'];
                $_SESSION['nom'] = $utilisateur['nom'];
                $_SESSION['prenom'] = $utilisateur['prenom'];
                
                // Redirection vers le tableau de bord
                header("location: dashboard.php");
                exit; // Termine l'exécution du script
            } else {
                $message = "<div style='color: red;'>❌ Mot de passe incorrect.</div>";
            }
        } else {
            $message = "<div style='color: red;'>❌ Aucun compte trouvé avec cet email.</div>";
        }
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription et Connexion</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: space-around; padding: 50px; background-color: #f4f4f4; }
        .form-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 45%; }
        input[type="text"], input[type="email"], input[type="password"] { width: 100%; padding: 10px; margin: 8px 0 15px 0; display: inline-block; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        input[type="submit"] { background-color: #4CAF50; color: white; padding: 14px 20px; margin: 8px 0; border: none; border-radius: 4px; cursor: pointer; width: 100%; }
        input[type="submit"]:hover { background-color: #45a049; }
        h2 { border-bottom: 2px solid #ccc; padding-bottom: 10px; }
    </style>
</head>
<body>

    <?php if (!empty($message)): ?>
        <div style="position: absolute; top: 10px; left: 50%; transform: translateX(-50%);"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="form-container">
        <h2>Créer un Compte</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="inscription" value="1">
            
            <label for="nom"><b>Nom</b></label>
            <input type="text" placeholder="Entrez le nom" name="nom" required>

            <label for="prenom"><b>Prénom</b></label>
            <input type="text" placeholder="Entrez le prénom" name="prenom" required>
            
            <label for="email"><b>Email</b></label>
            <input type="email" placeholder="Entrez l'email" name="email" required>

            <label for="mot_de_passe"><b>Mot de passe</b></label>
            <input type="password" placeholder="Entrez le mot de passe" name="mot_de_passe" required>

            <input type="submit" value="S'inscrire">
        </form>
    </div>

    <div class="form-container">
        <h2>Se Connecter</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="connexion" value="1">
            
            <label for="email_connexion"><b>Email</b></label>
            <input type="email" placeholder="Entrez l'email" name="email_connexion" required>

            <label for="mot_de_passe_connexion"><b>Mot de passe</b></label>
            <input type="password" placeholder="Entrez le mot de passe" name="mot_de_passe_connexion" required>

            <input type="submit" value="Se Connecter">
        </form>
    </div>

</body>
</html>