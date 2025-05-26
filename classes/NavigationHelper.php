<?php

// SessionManager должен быть доступен, если он используется для проверки user_id
// require_once __DIR__ . '/SessionManager.php'; // Раскомментируйте, если SessionManager используется напрямую

class NavigationHelper {

    /**
     * Определяет URL для кнопки "ACCOUNT" на основе статуса сессии пользователя.
     * 
     * Предполагается, что сессия уже запущена к моменту вызова этого метода.
     *
     * @param string $basePath Базовый путь проекта (например, /projekt1).
     * @return string URL для страницы аккаунта.
     */
    public static function getAccountUrl(string $basePath = '/projekt1'): string {

        $currentSessionUserId = $_SESSION['user_id'] ?? 'NOT SET';
        $sessionId = session_id() ?: 'NO_ACTIVE_SESSION';
        error_log("NavigationHelper::getAccountUrl - Checking session. user_id: " . $currentSessionUserId . " | Session ID: " . $sessionId);

        if (isset($_SESSION['user_id'])) {
            // error_log("User is considered LOGGED IN by NavigationHelper.");
            return $basePath . '/user/personal_page';
        } else {
            // error_log("User is considered LOGGED OUT by NavigationHelper, linking to /login.");
            return $basePath . '/register'; // Изменено на /register
        }
    }

    /**
     * Генерирует полный URL для ресурса в проекте.
     *
     * @param string $relativePath Относительный путь к ресурсу (например, 'css/style.css' или 'user/login.php').
     * @param string $baseProjectPath Базовый путь проекта (например, '/projekt1').
     * @return string Полный URL.
     */
    public static function getAssetUrl(string $relativePath, string $baseProjectPath = '/projekt1'): string {
        // Убираем возможные ведущие слеши из $relativePath, чтобы избежать двойных слешей
        $relativePath = ltrim($relativePath, '/');
        // Убеждаемся, что $baseProjectPath заканчивается слешем, если он не пустой
        if (!empty($baseProjectPath) && substr($baseProjectPath, -1) !== '/') {
            $baseProjectPath .= '/';
        } elseif (empty($baseProjectPath)) {
            $baseProjectPath = '/'; // Если базовый путь пуст, начинаем с корня сайта
        }
        return $baseProjectPath . $relativePath;
    }
}