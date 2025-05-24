<?php
// session_start() and error reporting are handled by the main index.php

// Ensure BASE_PATH is defined (should be by index.php)
if (!defined('BASE_PATH')) {
    // Fallback, should not be necessary if called from index.php
    define('BASE_PATH', dirname(dirname(__FILE__))); // Assumes classes are in BASE_PATH/classes/
}

// Dependencies - rely on autoloader for classes in 'classes' directory.
// For others, use BASE_PATH.
// db.php for Database::getInstance() - this might be needed if Database class itself isn't autoloaded
// or if getInstance() doesn't handle its own include.
$dbPath = BASE_PATH . '/contact/db.php';
if (file_exists($dbPath) && !class_exists('Database', false)) {
    require_once $dbPath;
}

// Assuming User class is in user/User.php and UserRepository in user/UserRepository.php
// These should ideally be moved to 'classes' or a subfolder like 'classes/Model' and 'classes/Repository'
// to be handled by the autoloader. For now, we'll require them explicitly if not found.
if (!class_exists('User')) {
    $userClassPath = BASE_PATH . '/user/User.php'; // Предполагаем, что файл User.php
    if (file_exists($userClassPath)) require_once $userClassPath;
}
if (!class_exists('UserRepository')) {
    $userRepoPath = BASE_PATH . '/user/UserRepository.php'; // Предполагаем, что файл UserRepository.php (бывший user_rep.php)
    if (file_exists($userRepoPath)) require_once $userRepoPath;
}

// RegisterView should be in views/
if (!class_exists('RegisterView')) {
    $registerViewPath = BASE_PATH . '/views/RegisterView.php';
    if (file_exists($registerViewPath)) require_once $registerViewPath;
}


class RegisterController
{
    private UserRepository $userRepo;
    private string $message = '';
    private array $formData = [
        'first_name' => '',
        'last_name'  => '',
        'email'      => '',
        'phone'      => '',
    ];
    private string $baseProjectUrlPath;

    public function __construct(string $baseProjectUrlPath = '/projekt1') // Accept base URL path
    {
        // $pdo should be available after db.php is included
        if (!class_exists('Database') || !class_exists('UserRepository')) {
            // Log error or throw exception, as critical dependencies are missing
            error_log("RegisterController: Database or UserRepository class not found.");
            // Potentially die or throw an exception to prevent further execution
            throw new Exception("Critical server error: Registration components missing.");
        }
        $pdo = Database::getInstance();
        $this->userRepo = new UserRepository($pdo);
        $this->baseProjectUrlPath = $baseProjectUrlPath;
    }

    public function handleRequest(): void
    {
        // Redirect if already logged in - moved from constructor
        if (isset($_SESSION['user_id'])) {
            // Use NavigationHelper or build URL with base path
            $personalPageUrl = $this->baseProjectUrlPath . '/user/personal_page'; // Assuming /user/personal_page will be a route
            if (class_exists('NavigationHelper')) {
                 // TODO: Define a route for personal_page and use it here.
                 // For now, let's assume a direct path that will be routed.
                 // Example: $personalPageUrl = NavigationHelper::getAssetUrl('user/personal_page');
                 // This needs a route like '/user/personal_page' to be defined in index.php
            }
            header("Location: " . $personalPageUrl);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processForm($_POST);
        }

        $this->renderView();
    }

    private function processForm(array $data): void
    {
        $this->formData['first_name'] = trim($data['first_name'] ?? '');
        $this->formData['last_name']  = trim($data['last_name'] ?? '');
        $this->formData['email']      = trim($data['email'] ?? '');
        $this->formData['phone']      = trim($data['phone'] ?? '');
        $password                     = $data['password'] ?? '';
        $confirm                      = $data['confirm'] ?? '';

        if ($this->formData['first_name'] === '' || $this->formData['last_name'] === '' || $this->formData['email'] === '' || $password === '' || $confirm === '') {
            $this->message = "Please fill in all required fields.";
            return;
        }

        if (!filter_var($this->formData['email'], FILTER_VALIDATE_EMAIL)) {
            $this->message = "Invalid email format.";
            return;
        }

        if ($this->userRepo->existsByEmail($this->formData['email'])) {
            $this->message = "A user with this email already exists.";
            return;
        }

        if ($password !== $confirm) {
            $this->message = "Passwords do not match.";
            return;
        }

        if (!class_exists('User')) {
             $this->message = "User class not found. Cannot create user.";
             error_log("RegisterController: User class not found during form processing.");
             return;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $newUser = new User(
            $this->formData['first_name'],
            $this->formData['last_name'],
            $this->formData['email'],
            $this->formData['phone'],
            null,
            $hash
        );

        if ($this->userRepo->add($newUser)) {
            $userId = $this->userRepo->getLastInsertId();
            $_SESSION['user_id'] = $userId;
            $_SESSION['user'] = [
                'id'         => $userId,
                'first_name' => $this->formData['first_name'],
                'last_name'  => $this->formData['last_name'],
                'email'      => $this->formData['email'],
                'phone'      => $this->formData['phone']
            ];
            
            // Redirect to personal page - use NavigationHelper or build URL with base path
            $personalPageUrl = $this->baseProjectUrlPath . '/user/personal_page'; // Assuming /user/personal_page will be a route
            if (class_exists('NavigationHelper')) {
                // $personalPageUrl = NavigationHelper::getAssetUrl('user/personal_page');
            }
            header("Location: " . $personalPageUrl);
            exit;
        } else {
            $this->message = "Registration failed! Please try again.";
        }
    }

    private function renderView(): void
    {
        if (!class_exists('RegisterView')) {
             $this->message = "RegisterView class not found. Cannot display registration page.";
             error_log("RegisterController: RegisterView class not found during rendering.");
             // Display a generic error or redirect
             echo "Error: Registration page is currently unavailable.";
             return;
        }
        // Передаем $baseProjectUrlPath в конструктор RegisterView
        $view = new RegisterView($this->message, $this->formData, $this->baseProjectUrlPath);
        $view->render();
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getFormData(): array
    {
        return $this->formData;
    }
}