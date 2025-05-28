<?php
require_once __DIR__ . '/../controllers/UserController.php';

$userController = new UserController();

$userId = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($userId !== null && $userId > 0) {
    $userController->deleteUser($userId);
} else {
    header('Location: users.php');
    exit;
}
