<?php
declare(strict_types=1);

// require_once '../contact/db.php'; // Заменено на использование автозагрузчика или прямого подключения в index.php
// Предполагается, что Database класс будет доступен через автозагрузчик или уже подключен в index.php
// Если нет, то в index.php перед созданием контроллера нужно будет добавить:
// if (!class_exists('Database')) { require_once BASE_PATH . '/contact/db.php'; }

class PersonalPageController
{
    private PDO $pdo;
    private ?array $user = null;
    private string $error = '';

    public function __construct()
    {
        // session_start(); // Сессия уже должна быть запущена в index.php
        // Проверяем, запущена ли сессия, перед обращением к $_SESSION
        if (session_status() == PHP_SESSION_NONE) {
            // Этого не должно происходить, если index.php работает корректно,
            // но на всякий случай, чтобы избежать ошибок при прямом доступе или тестах.
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            // Предполагаем, что $base_project_url_path будет доступен или передан,
            // но для простоты пока захардкодим. В идеале, его нужно передавать в конструктор или получать из конфигурации.
            $loginPageUrl = (defined('BASE_PROJECT_URL_PATH') ? BASE_PROJECT_URL_PATH : '/projekt1') . '/login'; // или /user/register если это страница регистрации
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
                // Аналогично предыдущему редиректу
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
            // Редирект на главную страницу
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
