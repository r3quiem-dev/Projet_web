<?php
// Paramètres de connexion
$host = 'localhost';
$dbname = 'quizzeo';
$username = 'root';
$password = ''; // Mets ton mot de passe si tu en as un (souvent vide sur XAMPP)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // On active les erreurs pour voir les problèmes SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion base de données : " . $e->getMessage());
}
?>