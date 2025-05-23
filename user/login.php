<?php
require_once '../contact/db.php';
require_once '../classes/AuthController.php';

$pdo = Database::getInstance();
$authController = new AuthController($pdo);

$message = '';
$data = ['email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($authController->login($_POST)) {
        header('Location: personal_page.php');
        exit;
    } else {
        $message = $authController->getError();
        $data['email'] = trim($_POST['email'] ?? '');
    }
}

include '../views/login_view.php';
