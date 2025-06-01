<?php
session_start(); // Запускаємо сесію для збереження даних користувача між запитами

require_once '../contact/db.php'; // Підключення до класу бази даних
require_once 'user_rep.php';      // Репозиторій користувача (робота з БД)
require_once 'user.php';          // Клас користувача

class AccountController
{
    private UserRepository $userRepo; // Репозиторій для доступу до користувачів
    private ?User $user = null;       // Поточний користувач або null
    private string $message = '';     // Повідомлення для відображення у вигляді

    public function __construct()
    {
        $pdo = Database::getInstance();              // Отримання екземпляра PDO через синглтон
        $this->userRepo = new UserRepository($pdo);  // Ініціалізація репозиторію користувачів

        $this->checkLogin();     // Перевірка, чи користувач увійшов у систему
        $this->initCsrfToken();  // Ініціалізація CSRF токена (захист від підробки запиту)
    }

    // Перевірка, чи користувач авторизований
    private function checkLogin(): void
    {
        if (!isset($_SESSION['user_id'])) {
            // Якщо немає ID користувача в сесії — перенаправлення на сторінку реєстрації
            header("Location: register1.php");
            exit;
        }

        // Завантаження користувача з БД за ID
        $this->user = $this->userRepo->getById((int)$_SESSION['user_id']);
        if (!$this->user) {
            // Якщо користувач не знайдений — знищити сесію і перенаправити
            session_destroy();
            header("Location: register1.php");
            exit;
        }
    }

    // Генерація CSRF токена, якщо він ще не існує
    private function initCsrfToken(): void
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Унікальний безпечний токен
        }
    }

    // Основний метод для обробки запиту
    public function handleRequest(): void
    {
        // Якщо форма надсилалася методом POST і користувач натиснув кнопку "Видалити акаунт"
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
            $this->handleDeleteAccount(); // Обробити видалення акаунта
        }

        $this->renderView(); // Відобразити сторінку з акаунтом
    }

    // Обробка запиту на видалення акаунта
    private function handleDeleteAccount(): void
    {
        // Перевірка дійсності CSRF токена
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die('Invalid CSRF token'); // Зупинити виконання при підробці
        }

        // Видалення акаунта через репозиторій
        if ($this->userRepo->delete($this->user->getId())) {
            session_destroy(); // Видалити сесію після успішного видалення
            header("Location: ../index.php"); // Повернутися на головну
            exit;
        } else {
            // Якщо не вдалося видалити — повідомити про помилку
            $this->message = "Error deleting account, please try again.";
        }
    }

    // Відображення HTML-сторінки з даними акаунта
    private function renderView(): void
    {
        $user = $this->user;
        $message = $this->message;
        $csrfToken = $_SESSION['csrf_token'];

        include __DIR__ . '/../views/account_view.php'; // Підключення шаблону відображення
    }
}

// Створення об'єкта контролера і запуск обробки запиту
$controller = new AccountController();
$controller->handleRequest();
