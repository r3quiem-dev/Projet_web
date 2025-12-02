<?php
// Fichier : public/index.php

// On charge le contrôleur d'authentification
require_once __DIR__ . '/../src/controllers/AuthController.php';

// On lance la méthode login()
$auth = new AuthController();
$auth->login();
?>