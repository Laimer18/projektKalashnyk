<?php
class Router
{
    private string $basePath;
    private string $requestPath;
    private PDO $pdo;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/');       // Запам'ятовує базовий шлях (підшлях проєкту)
        $this->startSession();                         // Запускає сесію (через SessionManager або PHP сесію)
        $this->pdo = $this->initializeDatabase();     // Ініціалізує PDO (підключення до БД)
        $this->requestPath = $this->getRequestPath(); // Отримує поточний шлях запиту (без базового шляху)
        $this->registerAutoloader();                   // Реєструє автозавантаження класів
    }

    private function startSession(): void
    {
        // Використовує SessionManager, якщо є, або стандартний session_start()
        if (class_exists('SessionManager')) {
            SessionManager::getInstance();
        } elseif (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function initializeDatabase(): PDO
    {
        require_once __DIR__ . '/../contact/db.php';  // Підключення файлу з базою даних
        return Database::getInstance();                // Повертає PDO з сінглтону Database
    }

    private function getRequestPath(): string
    {
        $requestUri = $_SERVER['REQUEST_URI'];         // Поточний URI з браузера
        $relativeUri = str_starts_with($requestUri, $this->basePath)
            ? substr($requestUri, strlen($this->basePath))
            : $requestUri;

        $path = '/' . trim(parse_url($relativeUri, PHP_URL_PATH), '/');
        $path = strtolower($path);                      // Приводимо шлях до нижнього регістру
        $path = str_ends_with($path, '.php') ? substr($path, 0, -4) : $path; // Відкидаємо .php
        return '/' . trim($path, '/');                   // Повертаємо відформатований шлях
    }

    private function registerAutoloader(): void
    {
        // Автоматично підключає клас за ім'ям, шукаючи в трьох папках
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
                (new HomeController())->index();           // Головна сторінка
                break;

            case '/contact/submit':
                // Обробка форми контакту, тільки POST
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller = new ContactHandler($this->pdo);
                    $result = $controller->handleForm($_POST);

                    // Збереження статусу у сесії для повідомлення
                    $_SESSION['contact_form_status'] = [
                        'success' => $result,
                        'message' => $result
                            ? 'Message sent successfully!'
                            : 'Error occurred while sending the form.'
                    ];
                }
                // Редірект назад на контактний блок
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
                // Вивід персональної сторінки користувача
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
                // Обробка редагування акаунта користувача з виводом форми
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
                http_response_code(404);                   // Відповідь 404 для невідомого маршруту
                require_once __DIR__ . '/../views/404_views.php';
                break;
        }
    }
}
