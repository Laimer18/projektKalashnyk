<?php
declare(strict_types=1);

session_start();

// require_once __DIR__ . '/../contact/db.php'; // Предполагается, что Database загружен автозагрузчиком или в index.php
// require_once __DIR__ . '/../user/user.php'; // Предполагается, что User загружен автозагрузчиком
require_once __DIR__ . '/../user/UserRepository.php'; // Исправлено имя файла, и это специфичное подключение, если UserRepository не в /classes
// require_once __DIR__ . '/SessionManager.php'; // Предполагается, что SessionManager загружен автозагрузчиком

class EditAccountController
{
    private PDO $pdo;
    private UserRepository $userRepo;
    private SessionManager $sessionManager;
    private ?User $user = null;
    private string $errorMessage = '';
    private string $successMessage = '';
    private string $csrfToken = '';

    public function __construct()
    {
        $this->sessionManager = SessionManager::getInstance(); // Ensures session is started and provides CSRF

        if (!$this->sessionManager->isLoggedIn()) {
            header('Location: login.php'); // Redirect to login if not logged in
            exit;
        }

        $this->pdo = Database::getInstance();
        $this->userRepo = new UserRepository($this->pdo);
        $this->csrfToken = $this->sessionManager->get('csrf_token');
        if (!$this->csrfToken) {
            $this->csrfToken = bin2hex(random_bytes(32));
            $this->sessionManager->set('csrf_token', $this->csrfToken);
        }

        $this->loadUser();
    }

    private function loadUser(): void
    {
        $userId = $this->sessionManager->getUserId();
        if ($userId) {
            $this->user = $this->userRepo->getById($userId);
            if (!$this->user) {
                $this->errorMessage = "User not found.";
                // Potentially log out or handle error
            }
        } else {
            $this->errorMessage = "User session not found.";
            // Redirect or error
        }
    }

    public function handleRequest(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processForm($_POST);
        }
    }

    private function processForm(array $postData): void
    {
        if (!isset($postData['csrf_token']) || $postData['csrf_token'] !== $this->csrfToken) {
            $this->errorMessage = 'Invalid CSRF token.';
            return;
        }

        $firstName = trim($postData['first_name'] ?? '');
        $lastName = trim($postData['last_name'] ?? '');
        $email = trim($postData['email'] ?? '');
        $phone = trim($postData['phone'] ?? '');
        // Password fields
        $currentPassword = $postData['current_password'] ?? '';
        $newPassword = $postData['new_password'] ?? '';
        $confirmPassword = $postData['confirm_password'] ?? '';

        if (!$this->user) {
            $this->errorMessage = "Cannot update: User data not loaded.";
            return;
        }

        // Basic validation
        if (empty($firstName) || empty($lastName) || empty($email)) {
            $this->errorMessage = "First name, last name, and email are required.";
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errorMessage = "Invalid email format.";
            return;
        }

        // Check if email is being changed and if it already exists for another user
        if ($email !== $this->user->getEmail() && $this->userRepo->existsByEmail($email)) {
            $this->errorMessage = "This email address is already in use by another account.";
            return;
        }

        $this->user->setFirstName($firstName);
        $this->user->setLastName($lastName);
        $this->user->setEmail($email);
        $this->user->setPhone($phone);

        // Password update logic
        if (!empty($currentPassword) || !empty($newPassword) || !empty($confirmPassword)) {
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                $this->errorMessage = "To change password, please fill all password fields: current, new, and confirm.";
                return;
            }
            if (!password_verify($currentPassword, $this->user->getPassword())) {
                $this->errorMessage = "Incorrect current password.";
                return;
            }
            if ($newPassword !== $confirmPassword) {
                $this->errorMessage = "New passwords do not match.";
                return;
            }
            if (strlen($newPassword) < 6) { // Example: minimum password length
                $this->errorMessage = "New password must be at least 6 characters long.";
                return;
            }
            $this->user->setPassword(password_hash($newPassword, PASSWORD_DEFAULT));
        }


        if ($this->userRepo->update($this->user)) {
            $this->successMessage = "Account details updated successfully.";
            // Update session data if name/email changed
            $_SESSION['user']['first_name'] = $firstName; // Assuming 'user' key exists in session
            $_SESSION['user']['last_name'] = $lastName;
            $_SESSION['user']['email'] = $email;
            $_SESSION['user']['phone'] = $phone;

        } else {
            $this->errorMessage = "Failed to update account details. Please try again.";
        }
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getSuccessMessage(): string
    {
        return $this->successMessage;
    }
    
    public function getCsrfToken(): string
    {
        return $this->csrfToken;
    }
}