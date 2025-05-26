<?php
declare(strict_types=1);


class PersonalPageController
{
    private PDO $pdo;
    private ?array $user = null;
    private string $error = '';

    public function __construct()
    {

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            $loginPageUrl = (defined('BASE_PROJECT_URL_PATH') ? BASE_PROJECT_URL_PATH : '/projekt1') . '/login';
            header('Location: ' . $loginPageUrl);
            exit;
        }

        $this->pdo = Database::getInstance();
        $this->initCsrfToken();

        $this->loadUser();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
            $this->handleDeleteAccount();
        }
    }

    private function initCsrfToken(): void
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    private function loadUser(): void
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                session_destroy();
                $loginPageUrl = (defined('BASE_PROJECT_URL_PATH') ? BASE_PROJECT_URL_PATH : '/projekt1') . '/login';
                header('Location: ' . $loginPageUrl);
                exit;
            }

            $this->user = $user;
        } catch (PDOException $e) {
            $this->error = "Database error: " . $e->getMessage();
        }
    }

    private function handleDeleteAccount(): void
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die('Invalid CSRF token');
        }

        try {
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);

            session_destroy();
            $homePageUrl = (defined('BASE_PROJECT_URL_PATH') ? BASE_PROJECT_URL_PATH : '/projekt1') . '/';
            header('Location: ' . $homePageUrl);
            exit;
        } catch (PDOException $e) {
            $this->error = "Failed to delete account: " . $e->getMessage();
        }
    }

    public function getUser(): ?array
    {
        return $this->user;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getCsrfToken(): string
    {
        return $_SESSION['csrf_token'];
    }
}
