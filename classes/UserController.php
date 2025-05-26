<?php
session_start();

require_once __DIR__ . '/../contact/db.php';
require_once __DIR__ . '/../user/user.php';
require_once __DIR__ . '/../user/user_rep.php';

class UserController
{
    private UserRepository $userRepository;
    private array $users = [];

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }

        $this->userRepository = new UserRepository(Database::getInstance());
    }

    // Метод для отримання списку користувачів
    public function loadUsers(): void
    {
        $this->users = $this->userRepository->getAll();
    }

    // Метод для отримання вже завантажених користувачів
    public function getUsers(): array
    {
        return $this->users;
    }

    // Метод для видалення користувача за id
    public function deleteUser($id): void
    {
        if (!isset($id) || !is_numeric($id)) {
            die("User ID not specified or invalid.");
        }

        $this->userRepository->delete((int)$id);
        header("Location: users.php");
        exit;
    }
}
