<?php
declare(strict_types=1);

require_once __DIR__ . '/../user/User.php';
require_once __DIR__ . '/../user/UserRepository.php';
require_once __DIR__ . '/SessionManager.php';

class EditUserController
{
    private UserRepository $userRepo;
    private SessionManager $sessionManager;
    private string $basePath;
    private ?User $user = null;
    private ?User $editingUser = null;
    private ?string $errorMessage = null;
    private ?string $successMessage = null;
    private string $csrfToken = '';

    public function __construct(UserRepository $userRepo, SessionManager $sessionManager, string $basePath)
    {
        $this->userRepo = $userRepo;
        $this->sessionManager = $sessionManager;
        $this->basePath = $basePath;

        if (!$this->sessionManager->isLoggedIn()) {
            $this->redirect($this->basePath . '/login');
        }

        $this->user = $this->userRepo->getById($this->sessionManager->getUserId());
        if (!$this->user) {
            $this->errorMessage = "Поточного користувача не знайдено.";
            $this->sessionManager->logout();
            $this->redirect($this->basePath . '/login');
        }

        $this->initCsrfToken();
        $this->editingUser = $this->user;
    }

    private function initCsrfToken(): void
    {
        $tokenFromSession = $this->sessionManager->get('csrf_token');
        if (!$tokenFromSession) {
            $token = bin2hex(random_bytes(32));
            $this->sessionManager->set('csrf_token', $token);
            $this->csrfToken = $token;
        } else {
            $this->csrfToken = $tokenFromSession;
        }
    }

    private function isValidCsrfToken(): bool
    {
        return isset($_POST['csrf_token']) && hash_equals($this->csrfToken, $_POST['csrf_token']);
    }

    public function handleRequest(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->isValidCsrfToken()) {
            $this->processUpdate($_POST);
        }
    }

    private function processUpdate(array $postData): void
    {
        $firstName = trim($postData['first_name'] ?? '');
        $lastName = trim($postData['last_name'] ?? '');
        $email = trim($postData['email'] ?? '');
        $phone = trim($postData['phone'] ?? '');

        if ($firstName === '' || $lastName === '' || $email === '') {
            $this->errorMessage = "Ім'я, прізвище та електронна пошта є обов'язковими.";
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errorMessage = "Невірний формат електронної пошти.";
            return;
        }
        if ($email !== $this->editingUser->getEmail() && $this->userRepo->existsByEmail($email)) {
            $this->errorMessage = 'Ця електронна пошта вже використовується іншим користувачем.';
            return;
        }

        $this->editingUser->setFirstName($firstName);
        $this->editingUser->setLastName($lastName);
        $this->editingUser->setEmail($email);
        $this->editingUser->setPhone($phone);

        if ($this->userRepo->update($this->editingUser)) {
            $this->successMessage = 'Інформація профілю успішно оновлена.';
            $this->sessionManager->login($this->editingUser);
        } else {
            $this->errorMessage = 'Не вдалося оновити користувача.';
        }
    }

    public function getEditingUser(): ?User
    {
        return $this->editingUser;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getSuccessMessage(): ?string
    {
        return $this->successMessage;
    }

    public function getCsrfToken(): string
    {
        return $this->csrfToken;
    }

    private function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}