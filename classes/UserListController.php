<?php
session_start(); // Ensure session is started

require_once __DIR__ . '/../contact/db.php';       // Database connection
require_once __DIR__ . '/../user/user.php';         // User class
require_once __DIR__ . '/../user/user_rep.php';     // UserRepository class

class UserListController
{
    private UserRepository $userRepo;
    private array $users = [];

    public function __construct()
    {
        // Protect the page: redirect to login if user is not logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php'); // Assumes login.php is in the same directory as users.php
            exit;
        }

        $this->userRepo = new UserRepository(Database::getInstance());
    }

    public function loadUsers(): void
    {
        $this->users = $this->userRepo->getAll();
    }

    public function getUsers(): array
    {
        return $this->users;
    }
}