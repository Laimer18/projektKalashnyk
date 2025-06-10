<?php
// C:\xampp\htdocs\projekt1\index.php

// 1. Включення файлу бази даних та SessionManager
// Використовуємо Database::getInstance() для PDO
require_once __DIR__ . '/contact/db.php';
require_once __DIR__ . '/classes/SessionManager.php';

// 2. Ініціалізація головних залежностей
// Отримуємо об'єкт PDO для роботи з базою даних
$pdo = Database::getInstance();

// Запускаємо сесію через SessionManager на самому початку
// Це гарантує, що session_start() викликається лише один раз і перед будь-яким виводом.
$sessionManager = SessionManager::getInstance();

// Ініціалізуємо репозиторій користувачів, який буде використовувати PDO
require_once __DIR__ . '/user/UserRepository.php';
$userRepository = new UserRepository($pdo);

// 3. Визначення базового шляху до проєкту (для коректних редиректів та посилань)
// Змініть '/projekt1', якщо ваш проєкт знаходиться в іншому підкаталозі або в корені.
$basePath = '/projekt1';

// 4. Ініціалізація та запуск Router
require_once __DIR__ . '/classes/Router.php';
$router = new Router($basePath, $pdo, $sessionManager, $userRepository);
$router->route();

// Після того, як Router::route() виконається, він або зробить редирект, або відобразить сторінку,
// і повинен завершити виконання скрипта за допомогою exit;.
?>