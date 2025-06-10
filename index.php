<?php
require_once __DIR__ . '/contact/db.php';
require_once __DIR__ . '/classes/SessionManager.php';

$pdo = Database::getInstance();

$sessionManager = SessionManager::getInstance();

require_once __DIR__ . '/user/UserRepository.php';
$userRepository = new UserRepository($pdo);

$basePath = '/projekt1';

require_once __DIR__ . '/classes/Router.php';
$router = new Router($basePath, $pdo, $sessionManager, $userRepository);
$router->route();

?>