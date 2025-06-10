<?php
// user/register1.php

// Завантажуємо необхідні класи
require_once __DIR__ . '/../classes/SessionManager.php';

// Ініціалізуємо SessionManager
$sessionManager = SessionManager::getInstance();

$basePath = '/projekt1';

if ($sessionManager->isLoggedIn()) {
    // Якщо так, перенаправляємо на персональну сторінку
    header("Location: " . $basePath . "/user/personal_page");
} else {
    // Якщо ні, перенаправляємо на сторінку реєстрації
    header("Location: " . $basePath . "/register");
}
exit;