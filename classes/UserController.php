<?php
declare(strict_types=1);

require_once __DIR__ . '/../user/User.php';
require_once __DIR__ . '/../user/UserRepository.php';
require_once __DIR__ . '/SessionManager.php';

class UserController
{
    private UserRepository $userRepo;
    private SessionManager $session;
    private ?User $user = null; // Поточний залогінений користувач
    private string $csrfToken;
    private ?string $statusMessage = null; // Залишаємо для загальних повідомлень

    public function __construct(UserRepository $userRepo, SessionManager $session)
    {
        $this->userRepo = $userRepo;
        $this->session = $session;

        // Перевірка авторизації є критичною
        if (!$session->isLoggedIn()) {
            $this->redirect('login.php'); // Перенаправлення, якщо не авторизований
        }

        // Отримання поточного користувача (може бути потрібним для відображення його даних деінде)
        $this->user = $userRepo->getById($session->getUserId());

        // Управління CSRF-токеном (якщо він потрібен для інших дій, не пов'язаних з редагуванням профілю)
        $tokenFromSession = $session->get('csrf_token');
        $this->csrfToken = $tokenFromSession !== null ? $tokenFromSession : $this->generateCsrfToken();
    }
    public function handleRequest(): void
    {

    }

    public function showAccountPage(): void
    {
        $user = $this->getUser();
        $csrfToken = $this->getCsrfToken();
        
        // Завантажуємо подання для сторінки акаунта
        require_once __DIR__ . '/../user/account.php';
    }
    private function validateCsrfToken(string $token): bool
    {
        return hash_equals($this->csrfToken, $token);
    }

    private function generateCsrfToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $this->session->set('csrf_token', $token);
        return $token;
    }

    private function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    public function getStatusMessage(): ?string
    {
        return $this->statusMessage;
    }

    public function getCsrfToken(): string
    {
        return $this->csrfToken;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }


}