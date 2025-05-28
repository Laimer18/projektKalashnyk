<?php

class SessionManager
{
    private static ?SessionManager $instance = null;

    private function __construct()
    {
        $this->start();
    }

    private function __clone()
    {
    }
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
        $_SESSION['email'] = $user->getEmail();
        error_log("SessionManager::login - Set user_id: " . $userIdToSet . " | Session ID: " . session_id());
        $userId = $user->getId();
        if ($userId) {
            try {
                $pdo = Database::getInstance();
                $cookieConsentRepo = new CookieConsentRepository($pdo);
                $consentStatus = $cookieConsentRepo->getUserConsentStatus($userId);
                $_SESSION['user_cookie_consent_status'] = $consentStatus ?? 'pending';
            } catch (Exception $e) {
                $_SESSION['user_cookie_consent_status'] = 'pending';
            }
        } else {

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