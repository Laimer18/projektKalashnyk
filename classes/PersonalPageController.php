<?php
declare(strict_types=1); // вимога до строгого типізування
class PersonalPageController
{
    private PDO $pdo; // Підключення до бази даних
    private ?array $user = null; // Інформація про користувача
    private string $error = '';

    public function __construct()
    {
        // Ініціалізація сесії
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Перевірка авторизації
        if (!isset($_SESSION['user_id'])) {
            $loginPageUrl = (defined('BASE_PROJECT_URL_PATH') ? BASE_PROJECT_URL_PATH : '/projekt1') . '/login'; // Отримуємо URL для сторінки логіну
            header('Location: ' . $loginPageUrl);
            exit;
        }

        // Підключення до бази
        $this->pdo = Database::getInstance();

        // Генерація CSRF-токена, якщо ще не згенерований
        $this->initCsrfToken();

        // Завантаження інформації про користувача
        $this->loadUser();

        // Обробка POST-запиту на видалення акаунту
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
            $this->handleDeleteAccount();
        }
    }
    private function initCsrfToken(): void // Ініціалізація CSRF-токена
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    private function loadUser(): void // Завантаження інформації про користувача з бази даних
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?"); // Підготовка запиту для отримання користувача за ID
            $stmt->execute([$_SESSION['user_id']]); // Виконання запиту з ID користувача, який зберігається в сесії
            $user = $stmt->fetch(PDO::FETCH_ASSOC); // Отримання результату запиту як асоціативного масиву

            if (!$user) {
                session_destroy();
                $loginPageUrl = (defined('BASE_PROJECT_URL_PATH') ? BASE_PROJECT_URL_PATH : '/projekt1') . '/login';// Отримуємо URL для сторінки логіну
                header('Location: ' . $loginPageUrl);
                exit;
            }

            $this->user = $user;
        } catch (PDOException $e) { // Обробка помилок при виконанні запиту
            $this->error = "Database error: " . $e->getMessage(); // Зберігаємо повідомлення про помилку
        }
    }

    private function handleDeleteAccount(): void // Обробка запиту на видалення акаунту
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) { // Перевірка CSRF-токена
            die('Invalid CSRF token');
        }

        try {
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?"); // Підготовка запиту для видалення користувача за ID
            $stmt->execute([$_SESSION['user_id']]);

            session_destroy();

            $homePageUrl = (defined('BASE_PROJECT_URL_PATH') ? BASE_PROJECT_URL_PATH : '/projekt1') . '/'; // Отримуємо URL для головної сторінки
            header('Location: ' . $homePageUrl);
            exit;
        } catch (PDOException $e) {
            $this->error = "Failed to delete account: " . $e->getMessage();
        }
    }

    public function getUser(): ?array // Повертає інформацію про користувача
    {
        return $this->user;
    }

    public function getError(): string
    {
        return $this->error;
    }


    public function getCsrfToken(): string
    {
        return $_SESSION['csrf_token'];
    }
}