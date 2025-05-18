<?php
require_once __DIR__ . '/../contact/db.php';
require_once __DIR__ . '/../classes/ContactMessage.php';
require_once __DIR__ . '/../classes/ContactMessageRepository.php';

$pdo = Database::getInstance();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = trim($_POST["first_name"] ?? '');
    $lastName  = trim($_POST["last_name"] ?? '');
    $email     = trim($_POST["email"] ?? '');
    $phone     = trim($_POST["phone"] ?? '');
    $question  = trim($_POST["questions"] ?? '');

    if (!$firstName || !$lastName || !$email || !$phone || !$question) {
        die("All fields are required.");
    }

    $message = new ContactMessage($firstName, $lastName, $email, $phone, $question);
    $repo = new ContactMessageRepository($pdo);

    if ($repo->add($message)) {
        header("Location: /projekt1/index.php");
        exit;

    } else {
        echo "Error: Could not send your message.";
    }
} else {
    echo "Error: Form not submitted.";
}
