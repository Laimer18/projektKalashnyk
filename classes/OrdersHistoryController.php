<?php

class OrdersHistoryController
{
    private PDO $pdo;
    private PhotosessionRepository $photosessionRepo;
    private array $orders = [];

    public function __construct()
    {
        // Ініціалізація підключення до бази даних
        $this->pdo = Database::getInstance();

        // Створення репозиторію фотосесій
        $this->photosessionRepo = new PhotosessionRepository($this->pdo);

        // Запуск сесії, якщо ще не запущена
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function showOrdersHistory(): void
    {
        // Якщо користувач не авторизований — редірект на логін
        if (!isset($_SESSION['user_id'])) {
            $loginPageUrl = (defined('BASE_PROJECT_URL_PATH') ? BASE_PROJECT_URL_PATH : '/projekt1') . '/login';
            header('Location: ' . $loginPageUrl);
            exit;
        }

        // Спроба отримати email користувача із сесії
        $userEmail = $_SESSION['user_email'] ?? null;

        // Якщо email не знайдено — витягуємо його з бази по user_id
        if (!$userEmail) {
            $stmt = $this->pdo->prepare("SELECT email FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($userRow && isset($userRow['email'])) {
                $userEmail = $userRow['email'];
                $_SESSION['user_email'] = $userEmail;
            } else {
                // Якщо не вдалось знайти email — лог і повідомлення
                error_log("OrdersHistoryController: Could not retrieve email for user_id: " . $_SESSION['user_id']);
                echo "Error: Could not retrieve user email to fetch order history.";
                return;
            }
        }

        // Отримання історії замовлень за email
        $this->orders = $this->photosessionRepo->getByEmail($userEmail);

        // Шлях до представлення
        $ordersHistoryViewPath = __DIR__ . '/../views/OrdersHistoryView.php';

        // Завантаження та відображення представлення, якщо існує
        if (file_exists($ordersHistoryViewPath)) {
            require_once $ordersHistoryViewPath;

            if (class_exists('OrdersHistoryView')) {
                $view = new OrdersHistoryView($this->getOrders());
                $view->render();
            } else {
                error_log("OrdersHistoryController: OrdersHistoryView class not found after requiring file.");
                echo "Error: Orders history view component is missing.";
            }
        } else {
            error_log("OrdersHistoryController: File OrdersHistoryView.php not found at " . $ordersHistoryViewPath);
            echo "Error: Orders history view file is missing.";
        }
    }

    public function getOrders(): array
    {
        return $this->orders;
    }
}
