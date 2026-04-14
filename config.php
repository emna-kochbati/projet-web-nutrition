<?php
class Db {
    private static $instance = NULL;

    public static function getConnexion() {
        if (!isset(self::$instance)) {
            try {
                self::$instance = new PDO(
                    'mysql:host=localhost;dbname=2a35;charset=utf8', // Change dbname if needed
                    'root',
                    '',
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (Exception $e) {
                die('Erreur: ' . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
