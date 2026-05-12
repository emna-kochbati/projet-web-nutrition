<?php
// Function to load .env variables
function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

loadEnv(__DIR__ . '/.env');
define('GEMINI_API_KEY', $_ENV['GEMINI_API_KEY'] ?? 'YOUR_API_KEY_HERE');

class Db {
    private static $instance = NULL;

    public static function getConnexion() {
        if (!isset(self::$instance)) {
            try {
                self::$instance = new PDO(
                    'mysql:host=localhost;dbname=2a35;charset=utf8',
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
