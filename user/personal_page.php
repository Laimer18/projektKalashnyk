<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: register1.php");
    exit;
}

require_once '../contact/db.php'; // Використовуємо клас Database

// Ініціалізація CSRF токена, якщо немає
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Обробка видалення акаунту
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Invalid CSRF token');
    }

    try {
        $pdo = Database::getInstance();

        // Видаляємо користувача за email
        $stmt = $pdo->prepare("DELETE FROM users WHERE email = ?");
        $stmt->execute([$_SESSION['user']['email']]);

        // Завершуємо сесію і редіректимо на головну
        session_destroy();
        header("Location: ../index.php");
        exit;
    } catch (PDOException $e) {
        die("Failed to delete account: " . $e->getMessage());
    }
}

$email = $_SESSION['user']['email'];

try {
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        die("User not found.");
    }
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Account</title>
    <link rel="stylesheet" href="../css/personal_page.css" />
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

    <!-- Logout button -->
    <a href="logout.php" class="main-btn logout">Log out</a>

    <!-- Delete account form -->
    <form method="post"
          onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');"
          style="margin-top: 20px;">
        <input type="hidden" name="delete_account" value="1" />
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>" />
        <button type="submit" style="background-color: #e74c3c; color: white; border: none; padding: 10px 15px; cursor: pointer;">
            Delete Account
        </button>
    </form>
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
