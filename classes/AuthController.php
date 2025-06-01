<?php

// Підключення залежностей: БД, класи користувача, репозиторій, шаблони для входу і реєстрації
require_once dirname(__DIR__) . '/contact/db.php';
require_once dirname(__DIR__) . '/user/User.php';
require_once dirname(__DIR__) . '/user/UserRepository.php';
require_once dirname(__DIR__) . '/views/LoginView.php';
require_once dirname(__DIR__) . '/views/RegisterView.php';

class AuthController
{
    private UserRepository $userRepo;     // Репозиторій користувачів
    private string $error = '';           // Повідомлення про помилку (наприклад, під час входу)
    private string $baseProjectUrlPath;   // Базовий шлях до проєкту (для коректних редиректів)

    public function __construct(PDO $pdo, string $baseProjectUrlPath)
    {
        // Запуск сесії, якщо ще не запущено
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->userRepo = new UserRepository($pdo);
        $this->baseProjectUrlPath = $baseProjectUrlPath;
    }

    // Обробка запиту на вхід
    public function handleLoginRequest(): void
    {
        $data = ['email' => '']; // Для збереження email при невдалому вході
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->login($_POST)) {
                // Якщо вхід успішний — редирект на особисту сторінку
                header('Location: ' . $this->baseProjectUrlPath . '/user/personal_page');
                exit;
            } else {
                // Помилка входу — повідомлення + збереження email
                $message = $this->getError();
                $data['email'] = trim($_POST['email'] ?? '');
            }
        }

        // Відображення форми входу з повідомленням та введеними даними
        $view = new LoginView($message, $data);
        $view->render();
    }

    // Обробка запиту на реєстрацію
    public function handleRegisterRequest(): void
    {
        // Якщо користувач уже авторизований — перенаправити на особисту сторінку
        if (isset($_SESSION['user_id'])) {
            header("Location: " . $this->baseProjectUrlPath . '/user/personal_page');
            exit;
        }

        $message = '';
        $formData = [
            'first_name' => '',
            'last_name'  => '',
            'email'      => '',
            'phone'      => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->register($_POST);
            $message = $result['message'];
            $formData = $result['formData'];

            if ($result['success']) {
                // Якщо реєстрація успішна — редирект
                header("Location: " . $this->baseProjectUrlPath . '/user/personal_page');
                exit;
            }
        }

        // Відображення форми реєстрації
        $view = new RegisterView($message, $formData, $this->baseProjectUrlPath);
        $view->render();
    }

    // Вхід користувача
    public function login(array $data): bool
    {
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if ($email === '' || $password === '') {
            $this->error = 'Please enter both email and password.';
            return false;
        }

        // Отримання користувача з БД
        $user = $this->userRepo->getByEmail($email);
        if (!$user || !password_verify($password, $user->getPassword())) {
            // Невірний email або пароль
            $this->error = 'Invalid email or password.';
            return false;
        }

        // Авторизація — збереження ID та даних користувача у сесії
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user'] = [
            'id'         => $user->getId(),
            'first_name' => $user->getFirstName(),
            'last_name'  => $user->getLastName(),
            'email'      => $user->getEmail(),
            'phone'      => $user->getPhone()
        ];

        return true;
    }

    // Реєстрація нового користувача
    public function register(array $data): array
    {
        // Попереднє очищення та валідація вхідних даних
        $formData = [
            'first_name' => trim($data['first_name'] ?? ''),
            'last_name'  => trim($data['last_name'] ?? ''),
            'email'      => trim($data['email'] ?? ''),
            'phone'      => trim($data['phone'] ?? '')
        ];
        $password = $data['password'] ?? '';
        $confirm  = $data['confirm'] ?? '';

        // Перевірка на порожні поля
        if (in_array('', [$formData['first_name'], $formData['last_name'], $formData['email'], $password, $confirm], true)) {
            return ['success' => false, 'message' => 'Please fill in all required fields.', 'formData' => $formData];
        }

        // Перевірка формату email
        if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format.', 'formData' => $formData];
        }

        // Перевірка на існуючого користувача з таким email
        if ($this->userRepo->existsByEmail($formData['email'])) {
            return ['success' => false, 'message' => 'A user with this email already exists.', 'formData' => $formData];
        }

        // Перевірка на співпадіння паролів
        if ($password !== $confirm) {
            return ['success' => false, 'message' => 'Passwords do not match.', 'formData' => $formData];
        }

        // Хешування пароля
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Створення об'єкта користувача
        $user = new User(
            $formData['first_name'],
            $formData['last_name'],
            $formData['email'],
            $formData['phone'],
            null,
            $hash
        );

        // Спроба зберегти в БД
        if ($this->userRepo->add($user)) {
            $userId = $this->userRepo->getLastInsertId();
            // Авторизація після успішної реєстрації
            $_SESSION['user_id'] = $userId;
            $_SESSION['user'] = [
                'id'         => $userId,
                'first_name' => $formData['first_name'],
                'last_name'  => $formData['last_name'],
                'email'      => $formData['email'],
                'phone'      => $formData['phone']
            ];
            return ['success' => true, 'message' => '', 'formData' => $formData];
        }

        // Помилка при збереженні користувача
        return ['success' => false, 'message' => 'Registration failed! Please try again.', 'formData' => $formData];
    }

    // Вихід користувача
    public function logout(): void
    {
        session_destroy(); // Завершення сесії
        header('Location: ' . $this->baseProjectUrlPath . '/login');
        exit;
    }

    // Отримати повідомлення про помилку
    public function getError(): string
    {
        return $this->error;
    }

    // Перенаправлення в залежності від авторизації
    public function handleRedirect(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $this->baseProjectUrlPath . '/user/register1.php');
            exit();
        } else {
            header('Location: ' . $this->baseProjectUrlPath . '/user/personal_page.php');
            exit();
        }
    }
}
