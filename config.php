<?php
if (!function_exists('loadEnv')) {
    function loadEnv($path) {
        if (!file_exists($path)) return;
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $_ENV[trim($parts[0])] = trim($parts[1]);
            }
        }
    }
}

loadEnv(dirname(__DIR__) . '/.env');

if (!defined('GEMINI_API_KEY')) {
    define('GEMINI_API_KEY', $_ENV['GEMINI_API_KEY'] ?? 'YOUR_API_KEY_HERE');
}
if (!defined('GEMINI_RECEPTES_KEY')) {
    define('GEMINI_RECEPTES_KEY', $_ENV['GEMINI_RECEPTES_KEY'] ?? '');
}
if (!defined('EDAMAM_APP_ID')) {
    define('EDAMAM_APP_ID', $_ENV['EDAMAM_APP_ID'] ?? '');
}
if (!defined('EDAMAM_APP_KEY')) {
    define('EDAMAM_APP_KEY', $_ENV['EDAMAM_APP_KEY'] ?? '');
}
if (!defined('UNSPLASH_ACCESS_KEY')) {
    define('UNSPLASH_ACCESS_KEY', $_ENV['UNSPLASH_ACCESS_KEY'] ?? '');
}
if (!defined('DB_HOST')) {
    define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', $_ENV['DB_NAME'] ?? '2a35');
}
if (!defined('DB_USER')) {
    define('DB_USER', $_ENV['DB_USER'] ?? 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', $_ENV['DB_PASS'] ?? '');
}

class Db {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {

        if (self::$instance === null) {

            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

            self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        }

        return self::$instance;
    }
}
