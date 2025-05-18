<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../contact/db.php';
require_once '../classes/Photosession.php';
require_once '../classes/PhotosessionRepository.php';

class OrderController
{
    private $pdo;
    private $message = '';

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function handleRequest()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: register1.php");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processForm();
        }

        $this->renderView();
    }

    private function processForm()
    {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $date = trim($_POST['date'] ?? '');
        $details = trim($_POST['details'] ?? '');

        $email = $_SESSION['user']['email'] ?? '';

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

        $repo = new PhotosessionRepository($this->pdo);
        $session = new Photosession($name, $email, $phone, $date, $details);

        if ($repo->add($session)) {
            $this->message = "Your order has been received! We will contact you soon.";
        } else {
            $this->message = "There was an error submitting your order. Please try again.";
        }
    }

    private function renderView()
    {
        $message = $this->message;
        // Важливо: шлях до файлу з урахуванням нової структури
        include __DIR__ . '/../views/order_view.php';
    }
}

// Ініціалізація та запуск контролера
$pdo = Database::getInstance();
$controller = new OrderController($pdo);
$controller->handleRequest();
