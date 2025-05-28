<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../classes/SessionManager.php';

if (class_exists('SessionManager')) {
    $sessionManager = SessionManager::getInstance();
    $sessionManager->logout();
} else {
    session_unset();
    session_destroy();
    error_log("Logout.php: SessionManager class not found. Used standard session destruction.");
}

$baseProjectPath = '/projekt1';
$loginRoute = $baseProjectPath . '/login';
header('Location: ' . $loginRoute);
exit;
