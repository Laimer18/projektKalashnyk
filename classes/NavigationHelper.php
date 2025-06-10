<?php
class NavigationHelper {

    public static function getAccountUrl(string $basePath = '/projekt1'): string { // отримуємо URL для облікового запису користувача

        // Для дебагу: логування ID сесії та user_id
        $currentSessionUserId = $_SESSION['user_id'] ?? 'NOT SET'; // якщо користувач не увійшов, то буде 'NOT SET'
        $sessionId = session_id() ?: 'NO_ACTIVE_SESSION'; // якщо сесія не активна, то буде 'NO_ACTIVE_SESSION'
        error_log("NavigationHelper::getAccountUrl - Checking session. user_id: " . $currentSessionUserId . " | Session ID: " . $sessionId);// Логування для дебагу

        // Якщо користувач увійшов — ведемо на його сторінку
        if (isset($_SESSION['user_id'])) {
            return $basePath . '/user/personal_page';
        } else {
            // Інакше — ведемо на реєстрацію
            return $basePath . '/register';
        }
    }

    public static function getAssetUrl(string $relativePath, string $baseProjectPath = '/projekt1'): string { // отримуємо URL для активів (зображень, CSS, JS тощо)
        // Видаляємо початковий слеш, якщо він є
        $relativePath = ltrim($relativePath, '/');

        // Додаємо слеш до кінця базового шляху, якщо потрібно
        if (!empty($baseProjectPath) && substr($baseProjectPath, -1) !== '/') {
            $baseProjectPath .= '/';
        } elseif (empty($baseProjectPath)) {
            $baseProjectPath = '/';
        }

        return $baseProjectPath . $relativePath;
    }
}