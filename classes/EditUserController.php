<?php
require_once '../contact/db.php';
require_once 'user.php';
require_once 'user_rep.php';

class EditUserController
{
    private UserRepository $userRepo;
    private ?string $errorMessage = null;
    private ?User $user = null;

    public function __construct()
    {
        $pdo = Database::getInstance();
        $this->userRepo = new UserRepository($pdo);
    }

    public function handleRequest(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->errorMessage = 'Invalid user ID.';
            return;
        }

        $this->user = $this->userRepo->getById($id);

        if (!$this->user) {
            $this->errorMessage = 'User not found.';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processForm($id);
        }
    }

    private function processForm(int $id): void
    {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (!$firstName || !$lastName || !$email) {
            $this->errorMessage = "First name, last name, and email are required fields.";
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errorMessage = "Invalid email format.";
            return;
        }

        if (mb_strlen($firstName) > 50 || mb_strlen($lastName) > 50) {
            $this->errorMessage = "First name or last name must not exceed 50 characters.";
            return;
        }

        if ($phone && mb_strlen($phone) > 20) {
            $this->errorMessage = "Phone number must not exceed 20 characters.";
            return;
        }

        $userToUpdate = new User($firstName, $lastName, $email, $phone, $id);

        if ($this->userRepo->update($userToUpdate)) {
            header('Location: users.php');
            exit;
        } else {
            $this->errorMessage = "Failed to save user data.";
        }
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }
}
