<?php

// 1. Базовий шлях проекту
define('BASE_PATH', __DIR__);
$baseProjectUrlPath = '/projekt1'; // змініть при потребі

// 2. Автозавантаження класів
spl_autoload_register(function ($className) {
    $paths = [
        BASE_PATH . '/classes/' . str_replace('\\', '/', $className) . '.php',
        BASE_PATH . '/user/' . $className . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// 3. Старт сесії
if (class_exists('SessionManager')) {
    SessionManager::getInstance();
} elseif (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 4. Отримання і нормалізація URI
$requestUri = $_SERVER['REQUEST_URI'];
$relativeUri = $requestUri;

if (!empty($baseProjectUrlPath) && str_starts_with($requestUri, $baseProjectUrlPath)) {
    $relativeUri = substr($requestUri, strlen($baseProjectUrlPath));
}

$parsedPath = parse_url($relativeUri, PHP_URL_PATH);
$requestPath = trim(strtolower($parsedPath), '/');

if (str_ends_with($requestPath, '.php')) {
    $requestPath = substr($requestPath, 0, -4);
}

$requestPath = '/' . ($requestPath ?: '');

// 5. Обробка маршрутів
switch ($requestPath) {
    case '/':
    case '/index':
        $controller = new HomeController();
        $controller->index();
        break;

    case '/contact/submit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new ContactController();
            $controller->submitForm($_POST);
            $_SESSION['contact_form_status'] = $controller->getResult();
        }
        header("Location: $baseProjectUrlPath/#menu-4");
        exit;

    case '/login':
        require_once BASE_PATH . '/contact/db.php';
        $pdo = Database::getInstance();
        $authController = new AuthController($pdo, $baseProjectUrlPath);
        $authController->handleLoginRequest();
        break;

    case '/logout':
        require_once BASE_PATH . '/contact/db.php';
        $pdo = Database::getInstance();
        $authController = new AuthController($pdo, $baseProjectUrlPath);
        $authController->handleLogoutRequest();
        break;

    case '/register':
        require_once BASE_PATH . '/contact/db.php';
        $pdo = Database::getInstance();
        $authController = new AuthController($pdo, $baseProjectUrlPath);
        $authController->handleRegisterRequest();
        break;


    case '/user/save_cookie_consent':
        $controller = new CookieConsentController();
        $controller->saveConsent();
        break;

    case '/user/personal_page':
        require_once BASE_PATH . '/contact/db.php';
        $controller = new PersonalPageController();
        $userData = $controller->getUser();
        $csrfToken = $controller->getCsrfToken();

        require_once BASE_PATH . '/views/PersonalPageView.php';
        $view = new PersonalPageView($userData, $csrfToken);
        $view->render();
        break;

    case '/user/order':
        require_once BASE_PATH . '/contact/db.php';
        require_once BASE_PATH . '/views/OrderView.php';
        $controller = new OrderController();
        $controller->handleRequest();
        break;

    case '/user/orders-history':
        require_once BASE_PATH . '/contact/db.php';
        $controller = new OrdersHistoryController();
        $controller->showOrdersHistory();
        break;

    case '/user/edit-account':
        require_once BASE_PATH . '/contact/db.php';
        require_once BASE_PATH . '/views/EditAccountView.php';
        $controller = new EditAccountController();
        $controller->handleRequest();

        $userToEdit = $controller->getUser();
        $view = new EditAccountView(
            $userToEdit,
            $controller->getErrorMessage(),
            $controller->getSuccessMessage(),
            $controller->getCsrfToken()
        );
        $view->render();
        break;

    default:
        http_response_code(404);
        require_once BASE_PATH . '/views/404_view.php';
        break;
}
