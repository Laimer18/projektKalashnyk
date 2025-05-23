<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // Якщо користувач не залогінений — переходимо на сторінку реєстрації
    header('Location: /projekt1/user/register1.php');
    exit();
} else {
    // Якщо залогінений — переходимо на персональну сторінку
    header('Location: /projekt1/user/personal_page.php');
    exit();
}
