<?php

class SessionManager
{
    private static ?SessionManager $instance = null; // Зберігає єдиний екземпляр (синглтон)

    private function __construct()
    {
        $this->start();  // Запускаємо сесію при створенні обʼєкта
    }

    private function __clone() {} // Забороняємо клонування

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a Singleton"); // Забороняємо десеріалізацію
    }

    public static function getInstance(): SessionManager
    {
        if (self::$instance === null) {
            self::$instance = new self();  // Якщо екземпляра немає — створюємо
        }
        return self::$instance; // Повертаємо єдиний екземпляр
    }

    public function start(): void
    {
        // Запускаємо сесію, якщо вона ще не запущена
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login($user): void
    {
        // Зберігаємо ID користувача і email у сесії при логіні
        $userIdToSet = $user->getId();
        $_SESSION['user_id'] = $userIdToSet;
        $_SESSION['email'] = $user->getEmail();

        error_log("SessionManager::login - Set user_id: " . $userIdToSet . " | Session ID: " . session_id());

        // Отримуємо статус cookie consent для користувача (через репозиторій)
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
        return isset($_SESSION['user_id']);  // Перевіряємо, чи є id користувача у сесії
    }

    public function logout(): void
    {
        // Завершуємо сесію (якщо вона відкрита)
        if (session_status() !== PHP_SESSION_NONE) {
            session_destroy();
        }
    }

    public function getUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null; // Повертаємо id користувача або null
    }

    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value; // Записуємо довільне значення в сесію
    }

    public function get(string $key)
    {
        return $_SESSION[$key] ?? null; // Отримуємо значення з сесії по ключу
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]); // Видаляємо ключ із сесії
    }
}
