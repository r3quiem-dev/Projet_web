<?php
// Démarrer la session
session_start();
 
// Vider toutes les variables de session
$_SESSION = array();
 
// Détruire la session
session_destroy();
 
// Rediriger vers la page d'accueil/connexion
header("location: index.php");
exit;
?>