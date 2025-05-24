<?php
session_start();

require_once '../contact/db.php';    // Клас Database для PDO
require_once 'user_rep.php';          // UserRepository
require_once 'user.php';              // Клас User

class AccountController
{
    private UserRepository $userRepo;
    private ?User $user = null;
    private string $message = '';

    public function __construct()
    {
        $pdo = Database::getInstance();
        $this->userRepo = new UserRepository($pdo);

        $this->checkLogin();
        $this->initCsrfToken();
    }

    private function checkLogin(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: register1.php");
            exit;
        }

        $this->user = $this->userRepo->getById((int)$_SESSION['user_id']);
        if (!$this->user) {
            session_destroy();
            header("Location: register1.php");
            exit;
        }
    }

    private function initCsrfToken(): void
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public function handleRequest(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
            $this->handleDeleteAccount();
        }

        $this->renderView();
    }

    private function handleDeleteAccount(): void
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die('Invalid CSRF token');
        }

        if ($this->userRepo->delete($this->user->getId())) {
            session_destroy();
            header("Location: ../index.php");
            exit;
        } else {
            $this->message = "Error deleting account, please try again.";
        }
    }

    private function renderView(): void
    {
        $user = $this->user;
        $message = $this->message;
        $csrfToken = $_SESSION['csrf_token'];

        include __DIR__ . '/../views/account_view.php';
    }
}
$controller = new AccountController();
$controller->handleRequest();
