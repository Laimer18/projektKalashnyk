<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: register1.php");
    exit;
}

// Get all user data from the database by email
$email = $_SESSION['user']['email'];
$host = 'localhost';
$db   = 'photosite';
$dbuser = 'root';
$dbpass = '';
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account</title>
   <link rel="stylesheet" href="../css/personal_page.css">
</head>
<body>
    <div class="bg-overlay"></div>
    <div class="personal-container">
        <h2>Welcome, <?= htmlspecialchars($user['first_name']) ?>!</h2>
        <div class="personal-info">
            <p><strong>Name:</strong> <?= htmlspecialchars($user['first_name']) ?> <?= htmlspecialchars($user['last_name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
            <?php if (!empty($user['created_at'])): ?>
                <p><strong>Registered:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
            <?php endif; ?>
            <?php if (!empty($user['id'])): ?>
                <p><strong>User ID:</strong> <?= htmlspecialchars($user['id']) ?></p>
            <?php endif; ?>
        </div>
        <div class="btn-group">
            <a href="/projekt1/index.php" class="main-btn home-btn">Go to Main Page</a>
            <a href="/projekt1/user/order.php" class="main-btn order-btn">Book a Photoshoot</a>
            <a href="/projekt1/user/orders_history.php" class="main-btn history-btn">Order History</a>
            <a href="#" class="main-btn wish-btn" id="wish-btn">Random Wishes</a>
        </div>
        <div id="wish-result"></div>
        <a href="logout.php" class="main-btn logout">Log out</a>
    </div>
    <script>
    document.getElementById('wish-btn').onclick = function(e) {
        e.preventDefault();
        fetch('get_wish.php')
            .then(response => response.text())
            .then(text => {
                document.getElementById('wish-result').innerText = text;
            })
            .catch(() => {
                document.getElementById('wish-result').innerText = "Failed to get a wish.";
            });
    };
    </script>
</body>
</html>