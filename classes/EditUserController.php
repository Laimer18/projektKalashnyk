<?php
declare(strict_types=1); // вимога до строгого типізування

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
    private string $csrfToken = ''; // CSRF токен для захисту від CSRF атак

    public function __construct(UserRepository $userRepo, SessionManager $sessionManager, string $basePath) // конструктор для ініціалізації залежностей
    {
        $this->userRepo = $userRepo;
        $this->sessionManager = $sessionManager;
        $this->basePath = $basePath;

        if (!$this->sessionManager->isLoggedIn()) {
            $this->redirect($this->basePath . '/login');
        }

        $this->user = $this->userRepo->getById($this->sessionManager->getUserId()); // Отримуємо поточного користувача за ID з сесії
        if (!$this->user) {
            $this->errorMessage = "Current user not found.";
            $this->sessionManager->logout();
            $this->redirect($this->basePath . '/login');
        }

        $this->initCsrfToken(); // Ініціалізуємо CSRF токен для захисту від CSRF атак
        $this->editingUser = $this->user;
    }

    private function initCsrfToken(): void
    {
        $tokenFromSession = $this->sessionManager->get('csrf_token');
        if (!$tokenFromSession) {
            $token = bin2hex(random_bytes(32)); // Генеруємо новий CSRF токен
            $this->sessionManager->set('csrf_token', $token); // Зберігаємо його в сесії
            $this->csrfToken = $token; // Встановлюємо токен для використання в формі
        } else {
            $this->csrfToken = $tokenFromSession; // Використовуємо токен з сесії, якщо він вже існує
        }
    }

    private function isValidCsrfToken(): bool // Перевіряємо CSRF токен, щоб захистити від CSRF атак
    {
        return isset($_POST['csrf_token']) && hash_equals($this->csrfToken, $_POST['csrf_token']); // Порівнюємо токен з сесії з токеном з POST-запиту
    }

    public function handleRequest(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->isValidCsrfToken()) { // Перевіряємо, чи це POST-запит і чи CSRF токен валідний
            $this->processUpdate($_POST);
        }
    }

    private function processUpdate(array $postData): void // Обробляємо оновлення профілю користувача
    {
        $firstName = trim($postData['first_name'] ?? '');
        $lastName = trim($postData['last_name'] ?? '');
        $email = trim($postData['email'] ?? '');
        $phone = trim($postData['phone'] ?? '');

        if ($firstName === '' || $lastName === '' || $email === '') { // Перевіряємо, чи заповнені обов'язкові поля
            $this->errorMessage = "First name, last name and email are required.";
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errorMessage = "Invalid email format.";
            return;
        }
        if ($email !== $this->editingUser->getEmail() && $this->userRepo->existsByEmail($email)) { // Перевіряємо, чи email унікальний, якщо він змінюється
            $this->errorMessage = 'This email is already in use by another user.';
            return;
        }

        $this->editingUser->setFirstName($firstName); // Встановлюємо нові значення для користувача
        $this->editingUser->setLastName($lastName);
        $this->editingUser->setEmail($email);
        $this->editingUser->setPhone($phone);

        if ($this->userRepo->update($this->editingUser)) { // Спробуємо оновити користувача в базі даних
            $this->successMessage = 'Profile information has been successfully updated.';
            $this->sessionManager->login($this->editingUser);
        } else {
            $this->errorMessage = 'Failed to update user.';
        }
    }

    public function getEditingUser(): ?User // Повертаємо користувача, який редагується
    {
        return $this->editingUser;
    }

    public function getErrorMessage(): ?string // Повертаємо повідомлення про помилку, якщо є
    {
        return $this->errorMessage;
    }

    public function getSuccessMessage(): ?string // Повертаємо повідомлення про успішне оновлення, якщо є
    {
        return $this->successMessage;
    }

    public function getCsrfToken(): string // Повертаємо CSRF токен для використання в формі
    {
        return $this->csrfToken;
    }

    private function redirect(string $url): void // Метод для перенаправлення користувача на іншу сторінку
    {
        header('Location: ' . $url);
        exit;
    }
}