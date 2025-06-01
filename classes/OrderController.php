<?php

class OrderController
{
    private PDO $pdo;
    private PhotosessionRepository $repo;
    private string $message = '';

    public function __construct()
    {
        // Підключення до бази даних
        $this->pdo = Database::getInstance();

        // Ініціалізація репозиторію фотосесій
        $this->repo = new PhotosessionRepository($this->pdo);

        // Запуск сесії, якщо ще не запущена
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function handleRequest(): void
    {
        // Якщо користувач не авторизований — редірект на сторінку входу
        if (!isset($_SESSION['user_id'])) {
            $loginPageUrl = (defined('BASE_PROJECT_URL_PATH') ? BASE_PROJECT_URL_PATH : '/projekt1') . '/login';
            header('Location: ' . $loginPageUrl);
            exit;
        }

        // Якщо метод — POST, то обробити форму
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Отримуємо email з сесії, якщо ще не отриманий — запитуємо з БД
            $userEmail = $_SESSION['user_email'] ?? '';
            if (empty($userEmail) && isset($_SESSION['user_id'])) {
                $stmt = $this->pdo->prepare("SELECT email FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($userRow) {
                    $userEmail = $userRow['email'];
                    $_SESSION['user_email'] = $userEmail;
                }
            }

            // Обробка даних з форми
            $this->processForm($_POST, $userEmail);
        }

        // Відображення сторінки
        $this->renderView();
    }


    private function processForm(array $postData, string $email): void
    {
        // Витягуємо та фільтруємо дані з форми
        $name = trim($postData['name'] ?? '');
        $phone = trim($postData['phone'] ?? '');
        $date = trim($postData['date'] ?? '');
        $details = trim($postData['details'] ?? '');

        // Перевірки на помилки
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

        // Створення об'єкта фотосесії та збереження через репозиторій
        $session = new Photosession($name, $email, $phone, $date, $details);

        if ($this->repo->add($session)) {
            $this->message = "Your order has been received! We will contact you soon.";
        } else {
            $this->message = "There was an error submitting your order. Please try again.";
        }
    }

    private function renderView(): void
    {
        $view = new OrderView($this->message);
        $view->render();
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
