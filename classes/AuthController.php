<?php
require_once '../user/user_rep.php';
require_once '../classes/SessionManager.php';

class AuthController {
    private PDO $pdo;
    private ?string $error;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->error = null;
        SessionManager::start();
    }

    public function login(array $postData): bool {
        $email = trim($postData['email'] ?? '');
        $password = $postData['password'] ?? '';

        if ($email === '' || $password === '') {
            $this->error = 'Please fill in all fields.';
            return false;
        }

        $userRepo = new UserRepository($this->pdo);
        $user = $userRepo->getByEmail($email);

        if ($user && password_verify($password, $user->getPassword())) {
            SessionManager::login($user);
            return true;
        }

        $this->error = 'Invalid email or password.';
        return false;
    }

    public function getError(): ?string {
        return $this->error;
    }
}
