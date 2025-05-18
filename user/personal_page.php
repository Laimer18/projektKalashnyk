<?php
require_once __DIR__ . '/../classes/AccountController.php';
require_once __DIR__ . '/../contact/db.php';

$pdo = Database::getInstance();

$controller = new AccountController($pdo);
$controller->handleRequest();
