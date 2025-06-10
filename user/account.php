<?php

require_once __DIR__ . '/../classes/AuthController.php';
require_once __DIR__ . '/../contact/db.php';

$pdo = Database::getInstance();

$baseProjectUrlPath = '/projekt1';

$userRepo = new UserRepository($pdo);
$sessionManager = SessionManager::getInstance();
$authController = new AuthController($userRepo, $sessionManager, $basePath);
$authController->handleRedirect();
