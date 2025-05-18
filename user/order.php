<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../contact/db.php';               // Підключення класу Database
require_once '../classes/Photosession.php';
require_once '../classes/PhotosessionRepository.php';

// Отримуємо PDO-з'єднання через Database::getInstance()
$pdo = Database::getInstance();

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name    = trim($_POST["name"]);
    $email   = trim($_POST["email"]);
    $phone   = trim($_POST["phone"]);
    $date    = trim($_POST["date"]);
    $details = trim($_POST["details"]);

    $repo = new PhotosessionRepository($pdo);
    $session = new Photosession($name, $email, $phone, $date, $details);

    if ($repo->add($session)) {
        $message = "Your order has been received! We will contact you soon.";
    } else {
        $message = "There was an error submitting your order. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book a Photoshoot</title>
    <link rel="stylesheet" href="../css/register.css">
    <link rel="stylesheet" href="../css/templatemo_style.css">
    <link rel="stylesheet" href="../css/order.css">
</head>
<body>
<div class="bg-overlay"></div>
<div class="order-form">
    <h2>Book a Photoshoot</h2>
    <?php if ($message): ?>
        <div class="order-message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form method="post" action="">
        <input type="text" name="name" placeholder="Your name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone" placeholder="Phone" required>
        <input type="date" name="date" placeholder="Photoshoot date" required>
        <textarea name="details" placeholder="Order details (location, wishes, etc.)"></textarea>
        <input type="submit" value="Book">
    </form>
    <div style="text-align:center;margin-top:18px;">
        <a href="personal_page.php" class="main-btn">Back to Account</a>
    </div>
</div>
</body>
</html>
