<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. Определение базового пути проекта
define('BASE_PATH', __DIR__);

// 2. Автозагрузчик классов
spl_autoload_register(function ($class_name) {
    $file = BASE_PATH . '/classes/' . str_replace('\\', '/', $class_name) . '.php';
    if (file_exists($file)) {
        require_once $file;
        return;
    }
    // Попытка загрузить из других известных мест, если необходимо (например, модели, репозитории)
    // Пример для UserRepository, если он остался в user/
    if ($class_name === 'UserRepository') {
        $userRepoFile = BASE_PATH . '/user/UserRepository.php'; // Убедитесь, что файл так называется
        if (file_exists($userRepoFile)) {
            require_once $userRepoFile;
            return;
        }
    }
    // Пример для User, если он остался в user/
    if ($class_name === 'User') {
        $userFile = BASE_PATH . '/user/User.php';
        if (file_exists($userFile)) {
            require_once $userFile;
            return;
        }
    }
});

// 3. Управление сессиями
if (class_exists('SessionManager')) {
    SessionManager::getInstance();
} else {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    error_log("Warning: SessionManager class not found. Using basic session_start().");
}

// 4. Простой маршрутизатор
$request_uri_original = $_SERVER['REQUEST_URI'];

// Базовый путь проекта в URL. ИЗМЕНИТЕ ПРИ НЕОБХОДИМОСТИ.
// Если проект в http://localhost/projekt1/, то '/projekt1'. Если в http://localhost/, то ''.
$base_project_url_path = '/projekt1';

$request_uri_processed = $request_uri_original;
if (!empty($base_project_url_path) && strpos($request_uri_original, $base_project_url_path) === 0) {
    $request_uri_processed = substr($request_uri_original, strlen($base_project_url_path));
}

// Удаляем GET-параметры
$request_path = parse_url($request_uri_processed, PHP_URL_PATH);

// Нормализация пути: убираем начальный и конечный слеши для консистентности,
// затем добавляем один начальный слеш, если путь не пустой.
$request_path = trim($request_path, '/');

// Удаляем .php в конце, если он есть, чтобы обрабатывать такие случаи
if (substr($request_path, -4) === '.php') {
    $request_path = substr($request_path, 0, -4);
}

if (empty($request_path)) {
    $request_path = '/'; // Для корневого URL
} else {
    $request_path = '/' . $request_path; // Всегда начинаем с / для непустых путей
}

// ОТЛАДКА (раскомментируйте при необходимости):
// /* -- Раскомментировано для отладки (die закомментирован)
echo "<pre>";
echo "DEBUGGING ROUTER (index.php):\n";
echo "Original REQUEST_URI: " . htmlspecialchars($request_uri_original) . "\n";
echo "Base Project URL Path: " . htmlspecialchars($base_project_url_path) . "\n";
echo "Processed URI (after stripping base): " . htmlspecialchars($request_uri_processed) . "\n";
echo "Final Request Path for Switch: " . htmlspecialchars($request_path) . "\n";
echo "</pre>";
// die("Debug finished. Current Request Path for Switch: " . htmlspecialchars($request_path)); // Оставляем закомментированным
// */ -- Раскомментировано для отладки (die закомментирован)

// 5. Обработка маршрутов
// Более агрессивная нормализация для исключения невидимых символов
$cleaned_path = preg_replace('/[^\w\/-]+/', '', $request_path);
$normalized_request_path = strtolower(trim($cleaned_path));
// Убедимся, что он все еще начинается с / если не пустой
if (!empty($normalized_request_path) && $normalized_request_path[0] !== '/') {
    $normalized_request_path = '/' . $normalized_request_path;
} elseif (empty($normalized_request_path) && $request_path === '/') { // Если исходный путь был просто '/', а после чистки стал пустым
    $normalized_request_path = '/';
}

switch ($normalized_request_path) { // Используем нормализованный путь
    case '/': // Главная страница
    case '/index.php': // Явно обрабатываем /index.php как главную страницу
    case '/index': // Добавляем обработку для /index
        if (class_exists('HomeController')) {
            $controller = new HomeController(); // Передаем $base_project_url_path, если он нужен конструктору
            $controller->index();
        } else {
            http_response_code(500); echo "<h1>500 - Server Error</h1><p>Home page controller missing.</p>";
            error_log("Routing error: HomeController not found for /");
        }
        break;

    case '/contact/submit':
        if ($_SERVER["REQUEST_METHOD"] === "POST" && class_exists('ContactController')) {
            $controller = new ContactController(); // Передаем $base_project_url_path, если нужен
            $result = $controller->submitForm($_POST);
            $_SESSION['contact_form_status'] = $result;
            header("Location: " . $base_project_url_path . "/#menu-4");
            exit;
        } else {
            $_SESSION['contact_form_status'] = ['success' => false, 'message' => 'Invalid request.'];
            header("Location: " . $base_project_url_path . "/#menu-4");
            exit;
        }
        break;

    case '/user/login':
    case '/login':
        // /* // Убираем внешние комментарии, чтобы код выполнился
        if (class_exists('AuthController') && class_exists('LoginController')) {
            $dbPath = BASE_PATH . '/contact/db.php';
            if (file_exists($dbPath) && !class_exists('Database', false)) { require_once $dbPath; }

            if (!class_exists('Database')) {
                 http_response_code(500); echo "<h1>500 - Server Error</h1><p>Database component missing for login.</p>";
                 error_log("Routing error: Database class not found for /login.");
                 break;
            }
            $pdo = Database::getInstance();
            $authController = new AuthController($pdo);
            $loginController = new LoginController($authController);
            $loginController->handleRequest();
        } else {
            http_response_code(500); echo "<h1>500 - Server Error</h1><p>Login components missing.</p>";
            error_log("Routing error: AuthController or LoginController not found for /login.");
        }
        // */ // Убираем внешние комментарии
        break;

    case '/user/save_cookie_consent':
        if (class_exists('CookieConsentController')) {
            $controller = new CookieConsentController(); // Передаем $base_project_url_path, если нужен
            $controller->saveConsent();
        } else {
            http_response_code(500); header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Server error: Cookie consent handler missing.']);
            error_log("Routing error: CookieConsentController not found for /user/save_cookie_consent.");
        }
        break;

    case '/user/register':
    case '/register':
        if (class_exists('RegisterController')) {
            $controller = new RegisterController($base_project_url_path);
            $controller->handleRequest();
        } else {
            http_response_code(500); echo "<h1>500 - Server Error</h1><p>Registration components missing.</p>";
            error_log("Routing error: RegisterController not found for /register. Processed path: " . htmlspecialchars($request_path));
        }
        break;

    case '/user/personal_page':
        if (class_exists('PersonalPageController')) {
            // Предполагаем, что AuthRedirectController нужен для проверки, авторизован ли пользователь
            // и для перенаправления, если нет.
            // Проверка аутентификации и редирект теперь полностью обрабатываются
            // в конструкторе PersonalPageController.
            // Поэтому следующий блок удален:
            // if (class_exists('AuthRedirectController')) { ... }
            // Убедимся, что Database класс доступен, если он не был загружен автозагрузчиком
            // и не был подключен ранее (например, для /login)
            $dbPath = BASE_PATH . '/contact/db.php';
            if (file_exists($dbPath) && !class_exists('Database', false)) {
                require_once $dbPath;
            }

            if (!class_exists('Database')) {
                 http_response_code(500); echo "<h1>500 - Server Error</h1><p>Database component missing for personal page.</p>";
                 error_log("Routing error: Database class not found for /user/personal_page.");
                 break;
            }

            $controller = new PersonalPageController(); // Конструктор PersonalPageController сам обрабатывает редирект, если пользователь не найден или не авторизован
            
            $userData = $controller->getUser();
            $csrfToken = $controller->getCsrfToken();
            $error = $controller->getError();

            if (!empty($error)) {
                // Если контроллер установил ошибку (например, проблема с БД при загрузке пользователя)
                http_response_code(500);
                echo "<h1>500 - Server Error</h1><p>" . htmlspecialchars($error) . "</p>";
                error_log("Error in PersonalPageController for /user/personal_page: " . $error);
            } elseif ($userData) { // Условие class_exists('PersonalPageView') будет проверено ниже, после попытки подключения
                // Явно подключаем файл представления, так как автозагрузчик ищет только в /classes
                $personalPageViewPath = BASE_PATH . '/views/PersonalPageView.php';
                if (file_exists($personalPageViewPath)) {
                    require_once $personalPageViewPath;
                }

                if (class_exists('PersonalPageView')) {
                    $view = new PersonalPageView($userData, $csrfToken);
                    $view->render();
                } // Закрывающая скобка для if (class_exists('PersonalPageView'))
            } elseif (!$userData && empty($error)) {
                // Это состояние не должно возникать, если конструктор контроллера работает правильно (уже должен был быть редирект)
                // Но на всякий случай:
                error_log("Error: User data not found for /user/personal_page, but no error set and no redirect from controller.");
                header('Location: ' . $base_project_url_path . '/login'); // Перенаправляем на логин
                exit;
            } elseif (!class_exists('PersonalPageView')) {
                http_response_code(500); echo "<h1>500 - Server Error</h1><p>Personal page view component missing.</p>";
                error_log("Routing error: PersonalPageView class not found for /user/personal_page.");
            }
        } else {
            http_response_code(500); echo "<h1>500 - Server Error</h1><p>Personal page components missing.</p>";
            error_log("Routing error: PersonalPageController not found for /user/personal_page.");
        }
        break;

    case '/user/order':
        if (class_exists('OrderController')) {
            // Убедимся, что Database класс доступен
            $dbPath = BASE_PATH . '/contact/db.php';
            if (file_exists($dbPath) && !class_exists('Database', false)) {
                require_once $dbPath;
            }
            if (!class_exists('Database')) {
                 http_response_code(500); echo "<h1>500 - Server Error</h1><p>Database component missing for order page.</p>";
                 error_log("Routing error: Database class not found for /user/order.");
                 break;
            }

            // Убедимся, что OrderView класс доступен (OrderController его использует)
            $orderViewPath = BASE_PATH . '/views/OrderView.php';
            if (file_exists($orderViewPath) && !class_exists('OrderView', false)) {
                require_once $orderViewPath;
            }
            if (!class_exists('OrderView')) {
                 http_response_code(500); echo "<h1>500 - Server Error</h1><p>Order view component missing.</p>";
                 error_log("Routing error: OrderView class not found for /user/order.");
                 break;
            }
            // Также OrderController использует Photosession и PhotosessionRepository,
            // но они должны загружаться автозагрузчиком из /classes

            $controller = new OrderController();
            $controller->handleRequest(); // Этот метод сам обрабатывает проверку сессии и рендеринг
        } else {
            http_response_code(500); echo "<h1>500 - Server Error</h1><p>Order page components missing.</p>";
            error_log("Routing error: OrderController not found for /user/order.");
        }
        break;

    case '/user/orders-history':
        if (class_exists('OrdersHistoryController')) {
            // Убедимся, что Database класс доступен
            $dbPath = BASE_PATH . '/contact/db.php';
            if (file_exists($dbPath) && !class_exists('Database', false)) {
                require_once $dbPath;
            }
            if (!class_exists('Database')) {
                 http_response_code(500); echo "<h1>500 - Server Error</h1><p>Database component missing for orders history page.</p>";
                 error_log("Routing error: Database class not found for /user/orders-history.");
                 break;
            }

            // OrdersHistoryController сам подключает свое представление (OrdersHistoryView)
            // Убедимся, что Photosession и PhotosessionRepository доступны (должны быть через автозагрузчик)

            $controller = new OrdersHistoryController();
            $controller->showOrdersHistory(); // Вызываем новый метод
        } else {
            http_response_code(500); echo "<h1>500 - Server Error</h1><p>Orders history page components missing.</p>";
            error_log("Routing error: OrdersHistoryController not found for /user/orders-history.");
        }
        break;

    case '/user/edit-account':
        // die("DEBUG: Reached /user/edit-account case in switch. Normalized path: " . $normalized_request_path); // Тестовая остановка
        if (class_exists('EditAccountController')) {
            // Убедимся, что Database класс доступен
            $dbPath = BASE_PATH . '/contact/db.php';
            if (file_exists($dbPath) && !class_exists('Database', false)) {
                require_once $dbPath;
            }
            if (!class_exists('Database')) {
                 http_response_code(500); echo "<h1>500 - Server Error</h1><p>Database component missing for edit account page.</p>";
                 error_log("Routing error: Database class not found for /user/edit-account.");
                 break;
            }

            // Убедимся, что User класс доступен (используется EditAccountController)
            // Автозагрузчик должен его подхватить из /user/User.php, если он там есть и настроен в автозагрузчике
            // Если нет, то:
            // $userClassPath = BASE_PATH . '/user/User.php';
            // if (file_exists($userClassPath) && !class_exists('User', false)) {
            //     require_once $userClassPath;
            // }
            // if (!class_exists('User')) {
            //      http_response_code(500); echo "<h1>500 - Server Error</h1><p>User class component missing for edit account page.</p>";
            //      error_log("Routing error: User class not found for /user/edit-account.");
            //      break;
            // }
            
            // Убедимся, что EditAccountView класс доступен
            $editAccountViewPath = BASE_PATH . '/views/EditAccountView.php';
            if (file_exists($editAccountViewPath) && !class_exists('EditAccountView', false)) {
                require_once $editAccountViewPath;
            }
             if (!class_exists('EditAccountView')) {
                 http_response_code(500); echo "<h1>500 - Server Error</h1><p>Edit account view component missing.</p>";
                 error_log("Routing error: EditAccountView class not found for /user/edit-account.");
                 break;
            }

            $controller = new EditAccountController();
            $controller->handleRequest(); // Обработка POST данных
            
            // Отображение представления
            // Конструктор EditAccountController уже загружает пользователя
            $userToEdit = $controller->getUser();
            if ($userToEdit) {
                $view = new EditAccountView(
                    $userToEdit,
                    $controller->getErrorMessage(),
                    $controller->getSuccessMessage(),
                    $controller->getCsrfToken()
                );
                $view->render();
            } else {
                // Если пользователь не загружен (например, из-за ошибки сессии, обработанной в конструкторе контроллера)
                // Контроллер должен был уже сделать редирект. Если нет, то это проблема.
                // Можно добавить здесь редирект на логин или отображение общей ошибки.
                if(empty($controller->getErrorMessage())) { // Если нет специфической ошибки от контроллера
                    echo "<h1>Error</h1><p>Could not load user data for editing.</p>";
                } else { // Показываем ошибку от контроллера
                     echo "<h1>Error</h1><p>" . htmlspecialchars($controller->getErrorMessage()) . "</p>";
                }
            }
        } else {
            http_response_code(500); echo "<h1>500 - Server Error</h1><p>Edit account page components missing.</p>";
            error_log("Routing error: EditAccountController not found for /user/edit-account.");
        }
        break;

    default:
        http_response_code(404);
        error_log("404 Not Found: Original URI: " . htmlspecialchars($request_uri_original) . ", Processed Path: " . htmlspecialchars($request_path));
        $view404Path = BASE_PATH . '/views/404_view.php';
        if (file_exists($view404Path)) {
            require_once $view404Path;
        } else {
            echo "<h1>404 - Page Not Found</h1><p>The page you are looking for ('" . htmlspecialchars($request_path) . "') does not exist.</p>";
        }
        break;
}
?>