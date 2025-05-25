<?php

require_once dirname(__DIR__) . '/contact/db.php'; // Підключаємо базу
require_once dirname(__DIR__) . '/user/User.php';
require_once dirname(__DIR__) . '/user/UserRepository.php';
require_once dirname(__DIR__) . '/views/LoginView.php';
require_once dirname(__DIR__) . '/views/RegisterView.php';

class AuthController
{
    private UserRepository $userRepo;
    private string $error = '';
    private string $baseProjectUrlPath;

    public function __construct(PDO $pdo, string $baseProjectUrlPath)

    {
        $pdo = Database::getInstance();
        $this->userRepo = new UserRepository($pdo);
        $this->baseProjectUrlPath = $baseProjectUrlPath;
    }

    public function handleLoginRequest(): void
    {
        $data = ['email' => ''];
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->login($_POST)) {
                header('Location: ' . $this->baseProjectUrlPath . '/user/personal_page');
                exit;
            } else {
                $message = $this->getError();
                $data['email'] = trim($_POST['email'] ?? '');
            }
        }

        $view = new LoginView($message, $data);
        $view->render();
    }

    public function handleRegisterRequest(): void
    {
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
                header("Location: " . $this->baseProjectUrlPath . '/user/personal_page');
                exit;
            }
        }

        $view = new RegisterView($message, $formData, $this->baseProjectUrlPath);
        $view->render();
    }

    public function login(array $data): bool
    {
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if ($email === '' || $password === '') {
            $this->error = 'Please enter both email and password.';
            return false;
        }

        $user = $this->userRepo->getByEmail($email);
        if (!$user || !password_verify($password, $user->getPassword())) {
            $this->error = 'Invalid email or password.';
            return false;
        }

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

    public function register(array $data): array
    {
        $formData = [
            'first_name' => trim($data['first_name'] ?? ''),
            'last_name'  => trim($data['last_name'] ?? ''),
            'email'      => trim($data['email'] ?? ''),
            'phone'      => trim($data['phone'] ?? '')
        ];
        $password = $data['password'] ?? '';
        $confirm  = $data['confirm'] ?? '';

        if (in_array('', [$formData['first_name'], $formData['last_name'], $formData['email'], $password, $confirm], true)) {
            return ['success' => false, 'message' => 'Please fill in all required fields.', 'formData' => $formData];
        }

        if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format.', 'formData' => $formData];
        }

        if ($this->userRepo->existsByEmail($formData['email'])) {
            return ['success' => false, 'message' => 'A user with this email already exists.', 'formData' => $formData];
        }

        if ($password !== $confirm) {
            return ['success' => false, 'message' => 'Passwords do not match.', 'formData' => $formData];
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $user = new User(
            $formData['first_name'],
            $formData['last_name'],
            $formData['email'],
            $formData['phone'],
            null,
            $hash
        );

        if ($this->userRepo->add($user)) {
            $userId = $this->userRepo->getLastInsertId();
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

        return ['success' => false, 'message' => 'Registration failed! Please try again.', 'formData' => $formData];
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: ' . $this->baseProjectUrlPath . '/login');
        exit;
    }

    public function getError(): string
    {
        return $this->error;
    }
}
