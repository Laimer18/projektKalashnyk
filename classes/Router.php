
<?php
class Router
{
    private string $basePath; // Базовий шлях проєкту, наприклад '/projekt1'
    private string $requestPath;
    private PDO $pdo; // Підключення до бази даних через PDO

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
            $classNamePath = str_replace('\\', DIRECTORY_SEPARATOR, $className); // Перетворюємо клас на шлях
            $paths = [
                __DIR__ . '/' . $classNamePath . '.php',
                __DIR__ . '/../user/' . $classNamePath . '.php',
                __DIR__ . '/../classes/' . $classNamePath . '.php',
                __DIR__ . '/../views/' . $classNamePath . '.php',
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
        switch ($this->requestPath) { // Визначаємо дію на основі запиту

            case '/':
            case '/index':
                (new HomeController())->index();           // Головна сторінка
                break;

            case '/contact/submit':
                // Обробка форми контакту, тільки POST
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller = new ContactController($this->pdo);
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
                $userRepo = new UserRepository($this->pdo);
                $session = SessionManager::getInstance();
                $result = (new AuthController($userRepo, $session, $this->basePath))->handleLoginRequest();// Обробка запиту на логін
                (new LoginView($result['message'], $result['data'], $this->basePath))->render();// Відображення форми логіну
                break;

            case '/logout':
                $userRepo = new UserRepository($this->pdo); // Репозиторій користувачів для доступу до даних
                $session = SessionManager::getInstance(); // Менеджер сесій для управління авторизацією
                (new AuthController($userRepo, $session, $this->basePath))->logout(); // Вихід з системи
                break;

            case '/register':
                $userRepo = new UserRepository($this->pdo);
                $session = SessionManager::getInstance();// Менеджер сесій для управління авторизацією
                $result = (new AuthController($userRepo, $session, $this->basePath))->handleRegisterRequest(); // Обробка запиту на реєстрацію
                (new RegisterView($result['message'], $result['formData'], $this->basePath))->render();
                break;

            case '/user/account':
                $userRepo = new UserRepository($this->pdo); // Репозиторій користувачів для доступу до даних
                $session = SessionManager::getInstance(); // Менеджер сесій для управління авторизацією
                (new UserController($userRepo, $session))->showAccountPage(); // Відображення сторінки акаунта користувача
                break;
            case '/user/personal_page':
                // Вивід персональної сторінки користувача
                $controller = new PersonalPageController($this->pdo); // Ініціалізація контролера персональної сторінки
                $userData = $controller->getUser(); // Отримання даних користувача
                $csrfToken = $controller->getCsrfToken();

                require_once __DIR__ . '/../views/PersonalPageView.php';
                (new PersonalPageView($userData, $csrfToken))->render(); // Відображення персональної сторінки користувача
                break;
            case '/user/edit-account':
                // Обробка редагування акаунта користувача з виводом форми
                require_once __DIR__ . '/../classes/EditUserController.php';
                require_once __DIR__ . '/../views/EditAccountView.php';

                $userRepo = new UserRepository($this->pdo); // Репозиторій користувачів для доступу до даних
                $session = SessionManager::getInstance(); // Менеджер сесій для управління авторизацією
                $controller = new EditUserController($userRepo, $session, $this->basePath); // Ініціалізація контролера редагування акаунта
                $controller->handleRequest();

                $view = new EditAccountView(
                    $controller->getEditingUser(), // Отримання користувача для редагування
                    $controller->getErrorMessage() ?? '', // Отримання повідомлення про помилку, якщо є
                    $controller->getSuccessMessage() ?? '', // Отримання повідомлення про успіх, якщо є
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
