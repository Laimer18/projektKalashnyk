<?php
class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $host = 'localhost';
            $db   = 'photosite1'; // Database name
            $user = 'root';       // XAMPP default user
            $pass = '';           // XAMPP default password (empty)
            $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

            try {
                self::$instance = new PDO($dsn, $user, $pass);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Database connection error: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
