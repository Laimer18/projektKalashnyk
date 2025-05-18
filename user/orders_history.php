<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: register1.php");
    exit;
}

require_once '../contact/db.php';
require_once '../classes/Photosession.php';
require_once '../classes/PhotosessionRepository.php';

// Ініціалізуємо PDO через свій клас Database (підкоригуй під свій код)
$pdo = Database::getInstance();

$email = $_SESSION['user']['email'];

$repo = new PhotosessionRepository($pdo);
$orders = $repo->getByEmail($email);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Order history</title>
    <link rel="stylesheet" href="../css/register.css">
    <link rel="stylesheet" href="../css/templatemo_style.css">
    <link rel="stylesheet" href="../css/orders_history.css">
</head>
<body>
<div class="bg-overlay"></div>
<div class="orders-container">
    <h2>Order history</h2>
    <?php if (empty($orders)): ?>
        <p style="text-align:center;">You have no orders yet.</p>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-item">
                <div><span class="order-label">Photoshoot date:</span> <?= htmlspecialchars($order->getDate()) ?></div>
                <div><span class="order-label">Phone:</span> <?= htmlspecialchars($order->getPhone()) ?></div>
                <div><span class="order-label">Details:</span> <?= nl2br(htmlspecialchars($order->getDetails())) ?></div>
                <div><span class="order-label">Photographer:</span> Sofia</div>
                <div>
                    <span class="order-label">Reserved:</span>
                    <?php
                    if (method_exists($order, 'getCreatedAt') && $order->getCreatedAt()) {
                        echo htmlspecialchars(date('Y-m-d', strtotime($order->getCreatedAt())));
                    }
                    ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <a href="personal_page.php" class="main-btn" style="margin-top:24px;">Back to Account</a>
</div>
</body>
</html>
