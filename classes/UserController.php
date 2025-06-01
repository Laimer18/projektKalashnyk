<?php
session_start();

require_once __DIR__ . '/../contact/db.php';
require_once __DIR__ . '/../user/user.php';
require_once __DIR__ . '/../user/user_rep.php';

class UserController
{
    private UserRepository $userRepository; // Репозиторій для роботи з користувачами
    private array $users = [];               // Масив користувачів

    public function __construct()
    {
        // Якщо користувач не авторизований, перекидаємо на логін
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }

        // Ініціалізуємо репозиторій з підключенням до бази
        $this->userRepository = new UserRepository(Database::getInstance());
    }

    public function loadUsers(): void
    {
        // Завантажуємо всіх користувачів з бази
        $this->users = $this->userRepository->getAll();
    }

    public function getUsers(): array
    {
        return $this->users; // Повертаємо завантажених користувачів
    }

    public function deleteUser($id): void
    {
        // Перевірка коректності id
        if (!isset($id) || !is_numeric($id)) {
            die("User ID not specified or invalid.");
        }

        // Видаляємо користувача за id
        $this->userRepository->delete((int)$id);

        // Переадресація на сторінку зі списком користувачів
        header("Location: users.php");
        exit;
    }
}
