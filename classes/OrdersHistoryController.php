<?php
class OrdersHistoryController
{
    private PDO $pdo;
    private PhotosessionRepository $photosessionRepo;
    private array $orders = [];

    public function __construct()
    {
        $this->pdo = Database::getInstance();
        $this->photosessionRepo = new PhotosessionRepository($this->pdo);
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Метод для отображения страницы истории заказов
    public function showOrdersHistory(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $loginPageUrl = (defined('BASE_PROJECT_URL_PATH') ? BASE_PROJECT_URL_PATH : '/projekt1') . '/login';
            header('Location: ' . $loginPageUrl);
            exit;
        }

        // Получаем email пользователя. Предполагаем, что он есть в сессии или его можно получить из БД.
        $userEmail = $_SESSION['user_email'] ?? null;
        if (!$userEmail) {
            // Пытаемся получить email из БД, если его нет в сессии
            $stmt = $this->pdo->prepare("SELECT email FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($userRow && isset($userRow['email'])) {
                $userEmail = $userRow['email'];
                $_SESSION['user_email'] = $userEmail; // Сохраняем в сессию
            } else {
                // Не удалось получить email, возможно, стоит показать ошибку или перенаправить
                error_log("OrdersHistoryController: Could not retrieve email for user_id: " . $_SESSION['user_id']);

                echo "Error: Could not retrieve user email to fetch order history.";
                return;
            }
        }
        
        $this->orders = $this->photosessionRepo->getByEmail($userEmail);

        // Подключаем и отображаем представление
        $ordersHistoryViewPath = BASE_PATH . '/views/OrdersHistoryView.php';
        if (file_exists($ordersHistoryViewPath)) {
            if(!class_exists('OrdersHistoryView')) { // Убедимся, что класс еще не загружен
                 require_once $ordersHistoryViewPath;
            }
            if(class_exists('OrdersHistoryView')) {
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
        return $this->orders; // Этот метод все еще может быть полезен, если представление вызывается отдельно
    }
}