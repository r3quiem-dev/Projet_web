<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo "Erreur de connection";
    header("location: erreur.php");
    exit;
}

$id_utilisateur_createur = $_SESSION['id_utilisateur'];

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'quizzeo');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Échec de la connexion à la base de données : " . $conn->connect_error);
}
$conn->begin_transaction(); 

try {
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);

    $sql_quiz = "INSERT INTO quizzes (id_utilisateur_createur, titre, description) VALUES (?, ?, ?)";
    $stmt_quiz = $conn->prepare($sql_quiz);
    $stmt_quiz->bind_param("iss", $id_utilisateur_createur, $titre, $description);
    $stmt_quiz->execute();
    
    $id_quiz = $conn->insert_id;
    $stmt_quiz->close();

    if (isset($_POST['questions']) && is_array($_POST['questions'])) {
        
        $sql_question = "INSERT INTO questions (id_quiz, texte_question) VALUES (?, ?)";
        $stmt_question = $conn->prepare($sql_question);

        $sql_choix = "INSERT INTO choix (id_question, texte_choix, est_correct) VALUES (?, ?, ?)";
        $stmt_choix = $conn->prepare($sql_choix);

        foreach ($_POST['questions'] as $qIndex => $questionData) {
            
            $texte_question = $questionData['texte'];
            
            $stmt_question->bind_param("is", $id_quiz, $texte_question);
            $stmt_question->execute();
            $id_question = $conn->insert_id;
            
            if (isset($questionData['choix']) && is_array($questionData['choix'])) {
                foreach ($questionData['choix'] as $cIndex => $choixData) {
                    $texte_choix = $choixData['texte'];
                    $est_correct = isset($choixData['correct']) ? 1 : 0; 

                    $stmt_choix->bind_param("isi", $id_question, $texte_choix, $est_correct);
                    $stmt_choix->execute();
                }
            }
        }
        
        $stmt_question->close();
        $stmt_choix->close();
    }
    
    $conn->commit();
    echo "<h1>Félicitations ! Votre quiz a été créé avec succès.</h1>";

} catch (Exception $e) {
    $conn->rollback(); 
    echo "<h1>Erreur lors de la création du quiz.</h1>";
    echo "<p>Détail de l'erreur : " . $e->getMessage() . "</p>";

} finally {
    $conn->close();
}
?>