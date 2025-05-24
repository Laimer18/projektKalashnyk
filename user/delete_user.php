<?php
require_once __DIR__ . '/../classes/UserController.php';

$userController = new UserController();

// Get the user ID from the query string
$userId = isset($_GET['id']) ? $_GET['id'] : null;

$userController->deleteUser($userId);
