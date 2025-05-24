<?php
// It's good practice to ensure dependencies are loaded.
// If you have an autoloader, these might not be strictly necessary here.
// Assuming db.php is in contact/ and CookieConsentRepository.php is in classes/
// require_once __DIR__ . '/../contact/db.php'; // Предполагается, что Database загружен автозагрузчиком или в index.php
// require_once __DIR__ . '/CookieConsentRepository.php'; // Предполагается, что CookieConsentRepository загружен автозагрузчиком

class SessionManager
{
    private static ?SessionManager $instance = null;

    private function __construct()
    {
        $this->start();
    }

    // Забороняємо клонування об'єкта
    private function __clone()
    {
    }

    // Забороняємо десеріалізацію
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a Singleton");
    }

    public static function getInstance(): SessionManager
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login($user): void
    {
        $userIdToSet = $user->getId();
        $_SESSION['user_id'] = $userIdToSet;
        $_SESSION['email'] = $user->getEmail(); // Предполагаем, что getEmail() существует и нужен
        error_log("SessionManager::login - Set user_id: " . $userIdToSet . " | Session ID: " . session_id());

        // Fetch and store cookie consent status from the cookie_consents table
        $userId = $user->getId();
        if ($userId) {
            try {
                $pdo = Database::getInstance();
                $cookieConsentRepo = new CookieConsentRepository($pdo);
                $consentStatus = $cookieConsentRepo->getUserConsentStatus($userId);
                $_SESSION['user_cookie_consent_status'] = $consentStatus ?? 'pending'; // Default to 'pending' if null
            } catch (Exception $e) {
                // Log error $e->getMessage()
                // Fallback in case of DB error during consent status fetch
                $_SESSION['user_cookie_consent_status'] = 'pending';
            }
        } else {
            // Should not happen if $user object is valid, but as a safeguard
            $_SESSION['user_cookie_consent_status'] = 'pending';
        }
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public function logout(): void
    {
        if (session_status() !== PHP_SESSION_NONE) {
            session_destroy();
        }
    }

    public function getUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    // Для додаткової роботи із сесією
    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }
}