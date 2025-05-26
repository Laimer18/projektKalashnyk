<?php

class NavigationHelper {


    public static function getAccountUrl(string $basePath = '/projekt1'): string {

        $currentSessionUserId = $_SESSION['user_id'] ?? 'NOT SET';
        $sessionId = session_id() ?: 'NO_ACTIVE_SESSION';
        error_log("NavigationHelper::getAccountUrl - Checking session. user_id: " . $currentSessionUserId . " | Session ID: " . $sessionId);

        if (isset($_SESSION['user_id'])) {
            return $basePath . '/user/personal_page';
        } else {
            return $basePath . '/register';
        }
    }


    public static function getAssetUrl(string $relativePath, string $baseProjectPath = '/projekt1'): string {
        $relativePath = ltrim($relativePath, '/');
        if (!empty($baseProjectPath) && substr($baseProjectPath, -1) !== '/') {
            $baseProjectPath .= '/';
        } elseif (empty($baseProjectPath)) {
            $baseProjectPath = '/';
        }
        return $baseProjectPath . $relativePath;
    }
}