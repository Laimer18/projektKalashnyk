<?php
require_once __DIR__ . '/../contact/db.php';
require_once __DIR__ . '/../classes/ContactMessage.php';
require_once __DIR__ . '/../classes/ContactMessageRepository.php';

$pdo = Database::getInstance(); // <- додано отримання PDO

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = trim($_POST["name"] ?? '');
    $email   = trim($_POST["email"] ?? '');
    $subject = trim($_POST["subject"] ?? '');
    $message = trim($_POST["message"] ?? '');

    if (!$name || !$email || !$subject || !$message) {
        die("All fields are required.");
    }

    $repo = new ContactMessageRepository($pdo);
    $contactMessage = new ContactMessage($name, $email, $subject, $message);

    if ($repo->add($contactMessage)) {
        echo "Thank you! Your message has been sent.";
    } else {
        echo "Error: Could not send your message.";
    }
} else {
    echo "Error: Form not submitted.";
}
exit;
