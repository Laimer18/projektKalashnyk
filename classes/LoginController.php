<?php

// Предполагается, что AuthController, LoginView и NavigationHelper
// будут загружены автозагрузчиком или уже подключены.
// BASE_PATH должен быть определен в точке входа (public/index.php).

class LoginController {
    private AuthController $authController;

    /**
     * Конструктор LoginController.
     *
     * @param AuthController $authController Экземпляр AuthController для обработки логики аутентификации.
     */
    public function __construct(AuthController $authController) {
        $this->authController = $authController;
    }

    /**
     * Обрабатывает GET и POST запросы для страницы входа.
     * Отображает форму входа или обрабатывает попытку входа.
     */
    public function handleRequest(): void {
        // echo "DEBUG: LoginController handleRequest started"; // Удаляем тестовый вывод
        // die(); // Удаляем тестовый вывод

        $message = ''; // Инициализируем пустой строкой по умолчанию
        // Инициализируем $data с ключом 'email', чтобы избежать ошибок в LoginView, если email не был отправлен
        $data = ['email' => '']; 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->authController->login($_POST)) {
                // Успешный вход. Перенаправляем на личную страницу.
                // Используем NavigationHelper для корректного формирования URL.
                // Предполагается, что NavigationHelper::getAssetUrl() доступен.
                // Успешный вход. Перенаправляем на личную страницу.
                // Формируем URL для редиректа на маршрут /user/personal_page
                $baseProjectPath = '/projekt1'; // Убедитесь, что это значение соответствует $base_project_url_path в index.php
                $redirectUrl = $baseProjectPath . '/user/personal_page';
                // error_log("LoginController: Attempting redirect to: " . $redirectUrl . " | Session ID: " . session_id()); // Отладка, можно закомментировать
                header('Location: ' . $redirectUrl);
                exit;
            } else {
                // Ошибка входа. Получаем сообщение об ошибке.
                $message = $this->authController->getError();
                // Сохраняем введенный email для повторного отображения в форме.
                $data['email'] = trim($_POST['email'] ?? '');
            }
        }

        echo "DEBUG: LoginController - Before LoginView check"; // Тестовый вывод
        // die(); // Можно раскомментировать для остановки здесь

        // Загрузка и отображение LoginView.
        // В идеале, LoginView должен быть доступен через автозагрузчик.
        // Добавим проверку и попытку подключения, если класс не найден.
        // Попытка явно подключить NavigationHelper, если он нужен для LoginView
        // и мог не загрузиться, если LoginController вызывается не через index.php (маловероятно сейчас)
        if (!class_exists('NavigationHelper')) {
            $navHelperPath = (defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__)) . '/classes/NavigationHelper.php';
            if (file_exists($navHelperPath)) {
                require_once $navHelperPath;
            } else {
                error_log("LoginController: Could not find NavigationHelper.php at " . $navHelperPath);
            }
        }

        if (!class_exists('LoginView')) {
            $loginViewPath = (defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__)) . '/views/LoginView.php';
            if (file_exists($loginViewPath)) {
                require_once $loginViewPath;
            } else {
                error_log("LoginController: Файл LoginView.php не найден по пути: " . $loginViewPath);
                // Можно отобразить сообщение об ошибке или выбросить исключение
                echo "Ошибка: Компонент представления для страницы входа отсутствует.";
                return;
            }
        }
        
        if (!class_exists('LoginView')) {
            error_log("LoginController: Класс LoginView не найден даже после попытки подключения файла.");
            echo "Ошибка: Не удалось инициализировать представление страницы входа.";
            return;
        }

        // var_dump("LoginController message before view:", $message); // Удаляем var_dump
        // die("Stopped in LoginController to check message.");

        $view = new LoginView($message, $data);
        $view->render();
    }
}