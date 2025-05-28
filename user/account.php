<?php

require_once __DIR__ . '/../classes/AuthController.php';
require_once __DIR__ . '/../contact/db.php';

$pdo = Database::getInstance();

$baseProjectUrlPath = '/projekt1';

$authController = new AuthController($pdo, $baseProjectUrlPath);
$authController->handleRedirect();
