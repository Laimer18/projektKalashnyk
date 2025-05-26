<?php

require_once __DIR__ . '/../classes/AuthController.php';
require_once __DIR__ . '/../contact/db.php';

// Ініціалізуємо PDO (базу даних)
$pdo = Database::getInstance();

// Шлях бази проєкту (відкоригуйте під свій)
$baseProjectUrlPath = '/projekt1';

$authController = new AuthController($pdo, $baseProjectUrlPath);
$authController->handleRedirect();
