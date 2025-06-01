<?php
require_once '../contact/db.php';                  // Підключення до БД
require_once '../classes/PhotoSession.php';        // Підключення класу PhotoSession
require_once '../classes/PhotoSession.php.php';

class PhotoshootBookingController
{
    private PDO $pdo;
    private PhotosessionRepository $repo;
    private string $message = '';

    public function __construct()
    {
        $this->pdo = Database::getInstance();           // Ініціалізація PDO з singleton Database
        $this->repo = new PhotosessionRepository($this->pdo); // Репозиторій для роботи з фотосесіями
    }

    public function handleRequest(): void
    {
        session_start();                                 // Запуск сесії

        // Якщо користувач не авторизований, редірект на сторінку реєстрації
        if (!isset($_SESSION['user'])) {
            header("Location: register1.php");
            exit;
        }

        // Обробка POST-запиту (відправка форми)
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->processForm($_POST, $_SESSION['user']['email'] ?? '');
        }
    }

    private function processForm(array $postData, string $email): void
    {
        $name    = trim($postData["name"] ?? '');
        $phone   = trim($postData["phone"] ?? '');
        $date    = trim($postData["date"] ?? '');
        $details = trim($postData["details"] ?? '');

        // Перевірка чи email є в сесії
        if (!$email) {
            $this->message = "User email not found in session.";
            return;
        }

        // Перевірка обов’язкових полів
        if (empty($name) || empty($phone) || empty($date)) {
            $this->message = "Please fill in all required fields.";
            return;
        }

        // Валідація email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->message = "Invalid user email format.";
            return;
        }

        // Створення об'єкта фотосесії
        $session = new Photosession($name, $email, $phone, $date, $details);

        // Спроба додати фотосесію в БД через репозиторій
        if ($this->repo->add($session)) {
            $this->message = "Your order has been received! We will contact you soon.";
        } else {
            $this->message = "There was an error submitting your order. Please try again.";
        }
    }

    // Повертає повідомлення статусу форми
    public function getMessage(): string
    {
        return $this->message;
    }
}
