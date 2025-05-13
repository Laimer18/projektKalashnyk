<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: register1.php");
    exit;
}

$email = $_SESSION['user']['email'];
$host = 'localhost';
$db   = 'photosite';
$user = 'root';
$pass = '';
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("SELECT * FROM photosessions WHERE email = ? ORDER BY date DESC, id DESC");
    $stmt->execute([$email]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Order history</title>
    <link rel="stylesheet" href="../css/register.css">
    <link rel="stylesheet" href="../css/templatemo_style.css">
    <style>
       body {
    background: #262626;
    font-family: Arial, sans-serif;
    min-height: 100vh;
    margin: 0;
}

.bg-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: #262626;
    opacity: 0.95;
    z-index: -1;
}

.personal-container, .order-form, .orders-container {
    max-width: 480px;
    margin: 80px auto;
    background: #1a1a1a;
    padding: 40px 30px 32px 30px;
    border-radius: 20px;
    box-shadow: 0 2px 24px rgba(0,0,0,0.25);
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
}

h2 {
    color: #bfa046;
    font-size: 2.4rem;
    font-weight: bold;
    letter-spacing: 4px;
    margin-bottom: 32px;
    text-transform: uppercase;
}

.personal-info {
    text-align: left;
    margin: 0 auto 32px auto;
    max-width: 340px;
}

.personal-info p {
    margin: 14px 0;
    font-size: 19px;
    color: #ccc;
}

.personal-info strong {
    color: #bfa046;
    font-weight: bold;
}

.btn-group {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
    margin-bottom: 10px;
}

.main-btn, .wish-btn, .history-btn, .order-btn, .home-btn, .mini-btn, input[type="submit"] {
    display: block;
    margin-top: 18px;
    margin-right: 0;
    padding: 16px 0;
    width: 100%;
    max-width: 320px;
    font-size: 20px;
    background: #bfa046;
    color: #fff;
    border-radius: 10px;
    text-decoration: none;
    font-weight: bold;
    letter-spacing: 1px;
    transition: background 0.2s, color 0.2s;
    border: none;
    text-align: center;
    box-sizing: border-box;
    cursor: pointer;
}

.main-btn.logout {
    background: #a33;
    margin-top: 32px;
}

.main-btn.logout:hover {
    background: #c44;
}

.main-btn:hover, .wish-btn:hover, .history-btn:hover, .order-btn:hover, .home-btn:hover, .mini-btn:hover, input[type="submit"]:hover {
    background: #a88c2c;
    color: #fff;
}

#wish-result {
    margin-top: 18px;
    font-size: 17px;
    color: #4682b4;
    min-height: 24px;
    font-style: italic;
}

.order-form input, .order-form textarea {
    width: 100%;
    padding: 14px;
    margin-bottom: 18px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 17px;
    box-sizing: border-box;
}

.order-form textarea {
    resize: vertical;
    min-height: 60px;
}

.order-message {
    color: #4682b4;
    font-weight: bold;
    text-align: center;
    margin-bottom: 18px;
}

.orders-container .order-item {
    border-bottom: 1px solid #333;
    padding: 16px 0;
    color: #ccc;
    text-align: left;
}

.orders-container .order-item:last-child {
    border-bottom: none;
}

.orders-container .order-label {
    color: #bfa046;
    font-weight: bold;
}

@media (max-width: 600px) {
    .personal-container, .order-form, .orders-container {
        max-width: 98vw;
        padding: 18px 5vw 18px 5vw;
    }
    .personal-info {
        max-width: 98vw;
    }
    .main-btn, .wish-btn, .history-btn, .order-btn, .home-btn, .mini-btn, input[type="submit"] {
        max-width: 98vw;
        font-size: 16px;
    }
}
    </style>
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
                    <div><span class="order-label">Photoshoot date:</span> <?= htmlspecialchars($order['date']) ?></div>
                    <div><span class="order-label">Phone:</span> <?= htmlspecialchars($order['phone']) ?></div>
                    <div><span class="order-label">Details:</span> <?= nl2br(htmlspecialchars($order['details'])) ?></div>
                    <div><span class="order-label">Photographer:</span> Sofia</div>
                    <div>
                        <span class="order-label">Reserved:</span>
                        <?php
                        if (!empty($order['created_at'])) {
                            echo htmlspecialchars(date('Y-m-d', strtotime($order['created_at'])));
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