<?php
require_once __DIR__ . '/../classes/EditUserController.php';
require_once __DIR__ . '/../views/EditUserView.php';
require_once __DIR__ . '/../user/User.php';

$controller = new EditUserController();
$controller->handleRequest();

$user = $controller->getEditingUser();
$errorMessage = $controller->getErrorMessage();
$successMessage = $controller->getSuccessMessage();
$csrfToken = $controller->getCsrfToken();

$view = new EditUserView($user, $errorMessage, $successMessage, $csrfToken);
$view->render(); // рендер виводе  сторінку зі сторінки редагування
