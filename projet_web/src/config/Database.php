<?php
class Database {
    private static $host = 'localhost';
    private static $dbname = 'quizzeo';
    private static $user = 'root';
    private static $pass = ''; // Mets ton mot de passe si besoin

    public static function getConnection() {
        try {
            $pdo = new PDO("mysql:host=".self::$host.";dbname=".self::$dbname, self::$user, self::$pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Erreur SQL : " . $e->getMessage());
        }
    }
}
?>