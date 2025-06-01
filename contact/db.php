<?php
class Database
{
    private static ?PDO $instance = null; // Статичне поле для єдиного екземпляру PDO

    public static function getInstance(): PDO
    {
        // Якщо екземпляр ще не створений — створюємо підключення
        if (self::$instance === null) {
            $host = 'localhost';
            $db   = 'photosite1'; // Назва бази даних
            $user = 'root';       // Користувач БД (XAMPP за замовчуванням)
            $pass = '';           // Пароль (XAMPP за замовчуванням порожній)
            $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

            try {
                // Підключаємося до бази через PDO
                self::$instance = new PDO($dsn, $user, $pass);

                // Встановлюємо режим помилок PDO — виключення
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                // Якщо не вдалося підключитись — припиняємо виконання з повідомленням
                die("Database connection error: " . $e->getMessage());
            }
        }

        // Повертаємо єдиний екземпляр PDO (синглтон)
        return self::$instance;
    }
}
