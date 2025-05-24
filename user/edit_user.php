<?php
require_once __DIR__ . '/../classes/EditUserController.php';
require_once __DIR__ . '/../views/EditUserView.php';
require_once __DIR__ . '/user.php'; // Ensure User class is available

$controller = new EditUserController();
$controller->handleRequest();

$user = $controller->getUser();
$errorMessage = $controller->getErrorMessage();

$view = new EditUserView($user, $errorMessage);
$view->render();
