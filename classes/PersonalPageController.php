<?php
declare(strict_types=1);

require_once '../contact/db.php';

class PersonalPageController
{
    private PDO $pdo;
    private ?array $user = null;
    private string $error = '';

    public function __construct()
    {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header('Location: register1.php');
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
                header('Location: register1.php');
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
            header('Location: ../index.php');
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
