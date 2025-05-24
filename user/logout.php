<?php
// Убедимся, что сессия запущена перед использованием SessionManager
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../classes/SessionManager.php';

// Проверяем, существует ли класс перед вызовом getInstance
if (class_exists('SessionManager')) {
    $sessionManager = SessionManager::getInstance();
    $sessionManager->logout();
} else {
    // Если SessionManager не найден, просто уничтожаем сессию стандартным способом
    session_unset();
    session_destroy();
    error_log("Logout.php: SessionManager class not found. Used standard session destruction.");
}

// Redirect to the login route
// Предполагаем, что $base_project_url_path определен в index.php и доступен здесь,
// но так как это отдельный скрипт, лучше его определить или захардкодить.
// Для простоты и консистентности с другими исправлениями:
$baseProjectPath = '/projekt1'; // Это должно совпадать с $base_project_url_path в index.php
$loginRoute = $baseProjectPath . '/login';
header('Location: ' . $loginRoute);
exit;
