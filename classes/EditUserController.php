<?php
declare(strict_types=1);

require_once __DIR__ . '/../user/User.php';
require_once __DIR__ . '/../user/UserRepository.php';
require_once __DIR__ . '/SessionManager.php';
require_once __DIR__ . '/../contact/db.php';

class EditUserController
{
    private PDO $pdo;
    private UserRepository $userRepo;
    private SessionManager $sessionManager;
    private ?User $user = null;           // Currently logged-in user
    private ?User $editingUser = null;    // User being edited (self or another)
    private ?string $errorMessage = null;
    private ?string $successMessage = null;
    private string $csrfToken = '';

    public function __construct()
    {
        $this->sessionManager = SessionManager::getInstance();

        if (!$this->sessionManager->isLoggedIn()) {
            header('Location: login.php');
            exit;
        }

        $this->pdo = Database::getInstance();
        $this->userRepo = new UserRepository($this->pdo);

        $this->user = $this->userRepo->getById($this->sessionManager->getUserId());
        if (!$this->user) {
            $this->errorMessage = "Current user not found.";
            return;
        }

        $this->csrfToken = $this->sessionManager->get('csrf_token');
        if (!$this->csrfToken) {
            $this->csrfToken = bin2hex(random_bytes(32));
            $this->sessionManager->set('csrf_token', $this->csrfToken);
        }
    }

    public function handleRequest(): void
    {
        $editId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($editId === 0 || $editId === $this->user->getId()) {
            $this->editingUser = $this->user;
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->processOwnAccountForm($_POST);
            }
        } else {
            $this->editingUser = $this->userRepo->getById($editId);
            if (!$this->editingUser) {
                $this->errorMessage = "User not found to edit.";
                return;
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->processAdminEditForm($_POST);
            }
        }
    }

    private function processOwnAccountForm(array $postData): void
    {
        if (!isset($postData['csrf_token']) || $postData['csrf_token'] !== $this->csrfToken) {
            $this->errorMessage = 'Incorrect CSRF token.';
            return;
        }

        $firstName = trim($postData['first_name'] ?? '');
        $lastName = trim($postData['last_name'] ?? '');
        $email = trim($postData['email'] ?? '');
        $phone = trim($postData['phone'] ?? '');
        $currentPassword = $postData['current_password'] ?? '';
        $newPassword = $postData['new_password'] ?? '';
        $confirmPassword = $postData['confirm_password'] ?? '';

        if ($firstName === '' || $lastName === '' || $email === '') {
            $this->errorMessage = "First name, last name, and email are required.";
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errorMessage = "Invalid email format.";
            return;
        }
        if ($email !== $this->editingUser->getEmail() && $this->userRepo->existsByEmail($email)) {
            $this->errorMessage = 'This email is already in use.';
            return;
        }

        $this->editingUser->setFirstName($firstName);
        $this->editingUser->setLastName($lastName);
        $this->editingUser->setEmail($email);
        $this->editingUser->setPhone($phone);

        if ($currentPassword !== '' || $newPassword !== '' || $confirmPassword !== '') {
            if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
                $this->errorMessage = 'To change your password, fill in all the appropriate fields.';
                return;
            }
            if (!password_verify($currentPassword, $this->editingUser->getPassword())) {
                $this->errorMessage = 'Current password is incorrect.';
                return;
            }
            if ($newPassword !== $confirmPassword) {
                $this->errorMessage = 'New passwords do not match.';
                return;
            }
            if (strlen($newPassword) < 6) {
                $this->errorMessage = 'The new password must be at least 6 characters long.';
                return;
            }
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->editingUser->setPassword($hashedPassword);
        }

        if ($this->userRepo->update($this->editingUser)) {
            $this->successMessage = 'Account updated successfully.';

            if ($this->editingUser->getId() === $this->user->getId()) {
                $_SESSION['user']['first_name'] = $firstName;
                $_SESSION['user']['last_name'] = $lastName;
                $_SESSION['user']['email'] = $email;
                $_SESSION['user']['phone'] = $phone;
            }
        } else {
            $this->errorMessage = 'Failed to update account.';
        }
    }

    private function processAdminEditForm(array $postData): void
    {
        $firstName = trim($postData['first_name'] ?? '');
        $lastName = trim($postData['last_name'] ?? '');
        $email = trim($postData['email'] ?? '');
        $phone = trim($postData['phone'] ?? '');

        if ($firstName === '' || $lastName === '' || $email === '') {
            $this->errorMessage = "First name, last name, and email are required.";
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errorMessage = "Invalid email format.";
            return;
        }
        if ($email !== $this->editingUser->getEmail() && $this->userRepo->existsByEmail($email)) {
            $this->errorMessage = 'This email is already used by another user.';
            return;
        }

        $this->editingUser->setFirstName($firstName);
        $this->editingUser->setLastName($lastName);
        $this->editingUser->setEmail($email);
        $this->editingUser->setPhone($phone);

        if ($this->userRepo->update($this->editingUser)) {
            header('Location: users.php');
            exit;
        } else {
            $this->errorMessage = 'Failed to update user.';
        }
    }

    public function getEditingUser(): ?User
    {
        return $this->editingUser;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getSuccessMessage(): ?string
    {
        return $this->successMessage;
    }

    public function getCsrfToken(): string
    {
        return $this->csrfToken;
    }
}
