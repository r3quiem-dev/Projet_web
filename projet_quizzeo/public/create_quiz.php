<?php
session_start();
require_once 'db.php'; // Connexion SQL

// SÉCURITÉ
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] === 'user') {
    header('Location: index.php');
    exit;
}

$message = "";

// TRAITEMENT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);

    if (!empty($titre)) {
        try {
            // Insertion SQL
            $stmt = $pdo->prepare("INSERT INTO quizzes (titre, auteur_id, status) VALUES (?, ?, 'en cours d''écriture')");
            $stmt->execute([$titre, $_SESSION['user']['id']]);
            
            // On récupère l'ID du quiz qu'on vient de créer
            $newQuizId = $pdo->lastInsertId();

            // Redirection vers l'édition
            header('Location: edit_quiz.php?id=' . $newQuizId);
            exit;
        } catch (PDOException $e) {
            $message = "Erreur lors de la création : " . $e->getMessage();
        }
    } else {
        $message = "Le titre est obligatoire.";
    }
}
?>