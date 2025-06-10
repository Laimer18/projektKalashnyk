<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/user/User.php';
require_once dirname(__DIR__) . '/user/UserRepository.php';
require_once dirname(__DIR__) . '/classes/SessionManager.php';

class AuthController
{
    private UserRepository $userRepo;  //  захищена взаємодія
    private SessionManager $session;
    private string $basePath;

    public function __construct(UserRepository $userRepo, SessionManager $session, string $basePath)
    {
        $this->userRepo = $userRepo;
        $this->session = $session;
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($email === '' || $password === '') {
                $message = 'Please enter both email and password.';
            } else {
                $user = $this->userRepo->getByEmail($email);
                if (!$user || !password_verify($password, $user->getPassword())) {
                    $message = 'Invalid login or password.';
                } else {
                    $this->session->login($user);
                    $this->redirect($this->basePath . '/user/personal_page');
                }
            }
            $data['email'] = $email;
        }

        return ['message' => $message, 'data' => $data];
    }

    public function handleRegisterRequest(): array
    {
        $this->checkLoginAndRedirect();

        $message = '';
        $formData = ['first_name' => '', 'last_name' => '', 'email' => '', 'phone' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->register($_POST);
            $message = $result['message'];
            $formData = $result['formData'];

            if ($result['success']) {
                $user = $this->userRepo->getByEmail($formData['email']);
                if ($user) {
                    $this->session->login($user);
                    $this->redirect($this->basePath . '/user/personal_page');
                } else {
                    $message = 'Error: User not found after successful registration.';
                }
            }
        }

        return ['message' => $message, 'formData' => $formData];
    }

    private function register(array $data): array
    {
        $formData = [
            'first_name' => trim($data['first_name'] ?? ''),
            'last_name' => trim($data['last_name'] ?? ''),
            'email' => trim($data['email'] ?? ''),
            'phone' => trim($data['phone'] ?? ''),
        ];

        $password = $data['password'] ?? '';
        $confirm = $data['confirm'] ?? '';

        if (in_array('', [...$formData, $password, $confirm], true)) {
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
            return ['success' => true, 'message' => '', 'formData' => $formData];
        }

        return ['success' => false, 'message' => 'Registration error! Please try again.', 'formData' => $formData];
    }

    public function logout(): void
    {
        $this->session->logout();
        $this->redirect($this->basePath . '/login');
    }

    private function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}