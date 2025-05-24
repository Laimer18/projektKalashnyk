<?php
// Лишний <?php удален
// Проверяем, определена ли константа BASE_PATH.
// Она должна быть определена в public/index.php.
if (!defined('BASE_PATH')) {
    // Определение BASE_PATH относительно текущего файла.
    // __DIR__ это 'c:/xampp/htdocs/projekt1/classes'
    // dirname(__DIR__) это 'c:/xampp/htdocs/projekt1'
    define('BASE_PATH', dirname(__DIR__));
}

// SessionManager.php находится в той же директории 'classes',
// поэтому автозагрузчик из public/index.php должен его найти.
// require_once '../classes/SessionManager.php'; // Можно удалить, полагаясь на автозагрузчик

// user_rep.php содержит UserRepository
require_once BASE_PATH . '/user/UserRepository.php'; // Исправлено имя файла

class AuthController {
    private PDO $pdo;
    private ?string $error;
    private SessionManager $sessionManager;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->error = null;
        $this->sessionManager = SessionManager::getInstance(); // This will also call start() via SessionManager's constructor
    }

    public function login(array $postData): bool {
        $email = trim($postData['email'] ?? '');
        $password = $postData['password'] ?? '';

        if ($email === '' || $password === '') {
            $this->error = 'Please fill in all fields.';
            error_log("AuthController::login - Error: " . $this->error . " | Email: " . $email);
            return false;
        }

        $userRepo = new UserRepository($this->pdo);
        $user = $userRepo->getByEmail($email);

        if (!$user) {
            $this->error = 'Invalid email or password. (User not found)';
            error_log("AuthController::login - Error: User not found for email: " . $email);
            return false;
        }

        if (password_verify($password, $user->getPassword())) {
            $this->sessionManager->login($user);
            error_log("AuthController::login - Login successful for email: " . $email . " | User ID: " . $user->getId());
            return true;
        }

        $this->error = 'Invalid email or password. (Password mismatch)';
        error_log("AuthController::login - Error: Password mismatch for email: " . $email);
        return false;
    }

    public function getError(): ?string {
        return $this->error;
    }
}
