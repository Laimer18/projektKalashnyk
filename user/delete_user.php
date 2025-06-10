<?php
require_once __DIR__ . '/../classes/UserController.php';
require_once __DIR__ . '/../user/UserRepository.php';
require_once __DIR__ . '/../classes/SessionManager.php';
require_once __DIR__ . '/../contact/db.php';

$pdo = Database::getInstance();
$userRepo = new UserRepository($pdo);
$sessionManager = SessionManager::getInstance();
$validator = new UserValidator();

$userController = new UserController($userRepo, $sessionManager, $validator);

$userId = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($userId !== null && $userId > 0) {
    $userController->deleteUser($userId);
} else {
    header('Location: users.php');
    exit;
}
