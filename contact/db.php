<?php
class Database
{
    private static ?PDO $instance = null; // Зберігає єдиний екземпляр (синглтон)

    public static function getInstance(): PDO // Повертає єдиний екземпляр PDO
    {
        if (!self::$instance) { // Якщо екземпляр ще не створено
            try {
                self::$instance = new PDO(
                    "mysql:host=localhost;dbname=photosite1;charset=utf8mb4",
                    'root',
                    '',
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            } catch (PDOException $e) { // Обробка помилки підключення до бази даних
                die("DB Error: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}