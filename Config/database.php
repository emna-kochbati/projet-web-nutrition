<?php

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {

        if (self::$instance === null) {

            $host = "localhost";
            $dbname = "projet_web_user";
            $user = "root";
            $pass = "";

            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

            self::$instance = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        }

        return self::$instance;
    }
}