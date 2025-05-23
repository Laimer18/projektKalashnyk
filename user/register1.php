<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../contact/db.php';
require_once 'user.php';
require_once 'user_rep.php';

class RegisterController
{
    private UserRepository $userRepo;
    private string $message = '';
    private array $formData = [
        'first_name' => '',
        'last_name'  => '',
        'email'      => '',
        'phone'      => '',
    ];

    public function __construct()
    {
        // Якщо користувач вже залогінений - редірект на персональну сторінку
        if (isset($_SESSION['user_id'])) {
            header("Location: personal_page.php");
            exit;
        }

        $pdo = Database::getInstance();
        $this->userRepo = new UserRepository($pdo);
    }

    public function handleRequest(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processForm($_POST);
        }

        $this->renderView();
    }

    private function processForm(array $data): void
    {
        $this->formData['first_name'] = trim($data['first_name'] ?? '');
        $this->formData['last_name']  = trim($data['last_name'] ?? '');
        $this->formData['email']      = trim($data['email'] ?? '');
        $this->formData['phone']      = trim($data['phone'] ?? '');
        $password                     = $data['password'] ?? '';
        $confirm                      = $data['confirm'] ?? '';

        // Валідація
        if ($this->formData['first_name'] === '' || $this->formData['last_name'] === '' || $this->formData['email'] === '' || $password === '' || $confirm === '') {
            $this->message = "Please fill in all required fields.";
            return;
        }

        if (!filter_var($this->formData['email'], FILTER_VALIDATE_EMAIL)) {
            $this->message = "Invalid email format.";
            return;
        }

        if ($this->userRepo->existsByEmail($this->formData['email'])) {
            $this->message = "A user with this email already exists.";
            return;
        }

        if ($password !== $confirm) {
            $this->message = "Passwords do not match.";
            return;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $newUser = new User(
            $this->formData['first_name'],
            $this->formData['last_name'],
            $this->formData['email'],
            $this->formData['phone'],
            null,
            $hash
        );

        if ($this->userRepo->add($newUser)) {
            // Отримуємо id нового користувача
            $userId = $this->userRepo->getLastInsertId();

            // Зберігаємо user_id в сесію для подальших перевірок
            $_SESSION['user_id'] = $userId;

            // Додатково зберігаємо інші дані (опціонально)
            $_SESSION['user'] = [
                'first_name' => $this->formData['first_name'],
                'last_name'  => $this->formData['last_name'],
                'email'      => $this->formData['email'],
                'phone'      => $this->formData['phone']
            ];

            header("Location: personal_page.php");
            exit;
        } else {
            $this->message = "Registration failed!";
        }
    }

    private function renderView(): void
    {
        $message = $this->message;
        $data = $this->formData;

        include __DIR__ . '/../views/register_view.php';
    }
}

$controller = new RegisterController();
$controller->handleRequest();
