<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Router.php loaded<br>";

class Router
{
    private string $basePath;
    private string $requestPath;
    private PDO $pdo;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/');
        $this->startSession();
        $this->pdo = $this->initializeDatabase();
        $this->requestPath = $this->getRequestPath();
        $this->registerAutoloader();
    }

    private function startSession(): void
    {
        if (class_exists('SessionManager')) {
            SessionManager::getInstance();
        } elseif (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function initializeDatabase(): PDO
    {
        require_once __DIR__ . '/../contact/db.php';
        return Database::getInstance();
    }

    private function getRequestPath(): string
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        $relativeUri = str_starts_with($requestUri, $this->basePath)
            ? substr($requestUri, strlen($this->basePath))
            : $requestUri;

        $path = '/' . trim(parse_url($relativeUri, PHP_URL_PATH), '/');
        $path = strtolower($path);
        $path = str_ends_with($path, '.php') ? substr($path, 0, -4) : $path;
        return '/' . trim($path, '/');
    }

    private function registerAutoloader(): void
    {
        spl_autoload_register(function ($className) {
            $classNamePath = str_replace('\\', DIRECTORY_SEPARATOR, $className);
            $paths = [
                __DIR__ . '/' . $classNamePath . '.php',
                __DIR__ . '/../user/' . $classNamePath . '.php',
                __DIR__ . '/../classes/' . $classNamePath . '.php',
            ];
            foreach ($paths as $path) {
                if (file_exists($path)) {
                    require_once $path;
                    return;
                }
            }
        });
    }

    public function route(): void
    {
        switch ($this->requestPath) {
            case '/':
            case '/index':
                (new HomeController())->index();
                break;

            case '/contact/submit':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller = new ContactHandler($this->pdo);
                    $result = $controller->handleForm($_POST);

                    $_SESSION['contact_form_status'] = [
                        'success' => $result,
                        'message' => $result
                            ? 'Message sent successfully!'
                            : 'Error occurred while sending the form.'
                    ];
                }
                header("Location: {$this->basePath}/#menu-4");
                exit;

            case '/login':
                (new AuthController($this->pdo, $this->basePath))->handleLoginRequest();
                break;

            case '/logout':
                (new AuthController($this->pdo, $this->basePath))->handleLogoutRequest();
                break;

            case '/register':
                (new AuthController($this->pdo, $this->basePath))->handleRegisterRequest();
                break;

            case '/user/save_cookie_consent':
                (new CookieConsentController())->saveConsent();
                break;

            case '/user/personal_page':
                $controller = new PersonalPageController($this->pdo);
                $userData = $controller->getUser();
                $csrfToken = $controller->getCsrfToken();

                require_once __DIR__ . '/../views/PersonalPageView.php';
                (new PersonalPageView($userData, $csrfToken))->render();
                break;

            case '/user/order':
                require_once __DIR__ . '/../views/OrderView.php';
                (new OrderController($this->pdo))->handleRequest();
                break;

            case '/user/orders-history':
                (new OrdersHistoryController($this->pdo))->showOrdersHistory();
                break;

            case '/user/edit-account':
                require_once __DIR__ . '/../classes/EditUserController.php';
                require_once __DIR__ . '/../views/EditAccountView.php';

                $controller = new EditUserController($this->pdo);
                $controller->handleRequest();

                $view = new EditAccountView(
                    $controller->getEditingUser(),
                    $controller->getErrorMessage() ?? '',
                    $controller->getSuccessMessage() ?? '',
                    $controller->getCsrfToken()
                );
                $view->render();
                break;

            default:
                http_response_code(404);
                require_once __DIR__ . '/../views/404_views.php';
                break;
        }
    }
}
