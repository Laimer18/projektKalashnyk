<?php
declare(strict_types=1); // вимога до строгого типізування

require_once dirname(__DIR__) . '/user/User.php';
require_once dirname(__DIR__) . '/user/UserRepository.php';
require_once dirname(__DIR__) . '/classes/SessionManager.php';

class AuthController
{
    private UserRepository $userRepo;  //  захищена взаємодія з репозиторієм користувачів
    private SessionManager $session; // менеджер сесій для роботи з авторизацією
    private string $basePath;  // базовий шлях для перенаправлень

    public function __construct(UserRepository $userRepo, SessionManager $session, string $basePath)
    {
        $this->userRepo = $userRepo; // репозиторій користувачів для доступу до даних
        $this->session = $session; // менеджер сесій для перевірки авторизації та управління сесіями
        $this->basePath = $basePath;
    }

    private function checkLoginAndRedirect(): void
    {
        if ($this->session->isLoggedIn()) {
            $this->redirect($this->basePath . '/user/personal_page');
        }
    }

    public function handleLoginRequest(): array
    {
        $this->checkLoginAndRedirect();

        $message = '';
        $data = ['email' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {  // Перевірка, чи це POST-запит
            $email = trim($_POST['email'] ?? ''); // отримуємо email з POST-запиту, якщо він є
            $password = $_POST['password'] ?? '';

            if ($email === '' || $password === '') { // Перевірка на заповненість полів
                $message = 'Please enter both email and password.';
            } else {
                $user = $this->userRepo->getByEmail($email); // отримуємо користувача за email
                if (!$user || !password_verify($password, $user->getPassword())) { // перевірка на існування користувача та правильність пароля
                    $message = 'Invalid login or password.';
                } else {
                    $this->session->login($user);
                    $this->redirect($this->basePath . '/user/personal_page');
                }
            }
            $data['email'] = $email; // зберігаємо email
        }

        return ['message' => $message, 'data' => $data]; // повертаємо повідомлення та дані для форми
    }

    public function handleRegisterRequest(): array // обробка запиту на реєстрацію
    {
        $this->checkLoginAndRedirect();

        $message = '';
        $formData = ['first_name' => '', 'last_name' => '', 'email' => '', 'phone' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Перевірка, чи це POST-запит
            $result = $this->register($_POST);
            $message = $result['message'];// отримуємо повідомлення з результату реєстрації
            $formData = $result['formData']; // отримуємо дані форми з результату реєстрації

            if ($result['success']) {
                $user = $this->userRepo->getByEmail($formData['email']); // отримуємо користувача за email після реєстрації
                if ($user) {
                    $this->session->login($user);
                    $this->redirect($this->basePath . '/user/personal_page');
                } else {
                    $message = 'Error: User not found after successful registration.';
                }
            }
        }

        return ['message' => $message, 'formData' => $formData]; // повертаємо повідомлення та дані форми
    }

    private function register(array $data): array // реєстрація нового користувача
    {
        $formData = [
            'first_name' => trim($data['first_name'] ?? ''),
            'last_name' => trim($data['last_name'] ?? ''),
            'email' => trim($data['email'] ?? ''),
            'phone' => trim($data['phone'] ?? ''),
        ];

        $password = $data['password'] ?? '';
        $confirm = $data['confirm'] ?? '';

        if (in_array('', [...$formData, $password, $confirm], true)) { // Перевірка на заповненість всіх полів
            return ['success' => false, 'message' => 'Please fill in all required fields.', 'formData' => $formData];
        }

        if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) { // Перевірка на коректність email
            return ['success' => false, 'message' => 'Invalid email format.', 'formData' => $formData];
        }

        if ($this->userRepo->existsByEmail($formData['email'])) { // Перевірка на унікальність email
            return ['success' => false, 'message' => 'A user with this email already exists.', 'formData' => $formData];
        }

        if ($password !== $confirm) { // Перевірка на збіг паролів
            return ['success' => false, 'message' => 'Passwords do not match.', 'formData' => $formData];
        }

        $hash = password_hash($password, PASSWORD_DEFAULT); // Хешування пароля
        $user = new User(
            $formData['first_name'],
            $formData['last_name'],
            $formData['email'],
            $formData['phone'],
            null,
            $hash
        );

        if ($this->userRepo->add($user)) {// Додавання користувача до бази даних
            return ['success' => true, 'message' => '', 'formData' => $formData];
        }

        return ['success' => false, 'message' => 'Registration error! Please try again.', 'formData' => $formData];
    }

    public function logout(): void  // вихід з системи
    {
        $this->session->logout();
        $this->redirect($this->basePath . '/login');
    }

    private function redirect(string $url): void // перенаправлення на вказаний URL
    {
        header('Location: ' . $url);
        exit;
    }
}