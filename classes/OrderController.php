<?php
// session_start(); // Сессия уже должна быть запущена в index.php
// require_once __DIR__ . '/../contact/db.php'; // Будет подключен в index.php или через автозагрузчик
// require_once __DIR__ . '/../classes/Photosession.php'; // Должен быть загружен автозагрузчиком
// require_once __DIR__ . '/../classes/PhotosessionRepository.php'; // Должен быть загружен автозагрузчиком
// require_once __DIR__ . '/../views/OrderView.php'; // Будет подключен в index.php перед renderView

class OrderController
{
    private PDO $pdo;
    private PhotosessionRepository $repo;
    private string $message = '';

    public function __construct()
    {
        $this->pdo = Database::getInstance();
        $this->repo = new PhotosessionRepository($this->pdo);
        // Проверяем, запущена ли сессия, перед обращением к $_SESSION
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function handleRequest(): void
    {
        // Используем user_id для проверки, как в других контроллерах
        if (!isset($_SESSION['user_id'])) {
            // Редирект на страницу логина с использованием $base_project_url_path (должен быть доступен или передан)
            $loginPageUrl = (defined('BASE_PROJECT_URL_PATH') ? BASE_PROJECT_URL_PATH : '/projekt1') . '/login';
            header('Location: ' . $loginPageUrl);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Предполагаем, что email пользователя можно получить из БД по user_id, если он нужен
            // или он уже есть в $_SESSION['user_email'] после логина.
            // Для processForm нужен email. Если его нет в сессии, нужно его получить.
            // Пока оставим $_SESSION['user']['email'], но это может потребовать доработки при логине.
            // Лучше получать email из базы данных по $_SESSION['user_id'] внутри processForm или передавать его.
            // Для упрощения, предположим, что $_SESSION['user_email'] устанавливается при логине.
            $userEmail = $_SESSION['user_email'] ?? ''; // Или получить из БД по $_SESSION['user_id']
            if (empty($userEmail) && isset($_SESSION['user_id'])) {
                // Попытка получить email из БД, если его нет в сессии, но есть user_id
                // Это пример, реальная реализация может отличаться
                $stmt = $this->pdo->prepare("SELECT email FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($userRow) {
                    $userEmail = $userRow['email'];
                    $_SESSION['user_email'] = $userEmail; // Сохраняем в сессию для будущих запросов
                }
            }
            $this->processForm($_POST, $userEmail);
        }

        $this->renderView();
    }

    private function processForm(array $postData, string $email): void
    {
        $name = trim($postData['name'] ?? '');
        $phone = trim($postData['phone'] ?? '');
        $date = trim($postData['date'] ?? '');
        $details = trim($postData['details'] ?? '');

        if (!$email) {
            $this->message = "User email not found in session.";
            return;
        }

        if (empty($name) || empty($phone) || empty($date)) {
            $this->message = "Please fill in all required fields.";
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->message = "Invalid user email format.";
            return;
        }

        $session = new Photosession($name, $email, $phone, $date, $details);

        if ($this->repo->add($session)) {
            $this->message = "Your order has been received! We will contact you soon.";
        } else {
            $this->message = "There was an error submitting your order. Please try again.";
        }
    }

    private function renderView(): void
    {
        // The message is already a property of this controller
        $view = new OrderView($this->message);
        $view->render();
    }

    // Getter for the message if needed externally, though not strictly necessary for this refactor
    public function getMessage(): string
    {
        return $this->message;
    }
}

// The instantiation and request handling will be in user/order.php
// So, remove these lines from here:
// $controller = new OrderController();
// $controller->handleRequest();