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

    <?php if (!empty($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <h2>Welcome, <?= htmlspecialchars($user->getFirstName()) ?>!</h2>
    <div class="personal-info">
        <p><strong>Name:</strong> <?= htmlspecialchars($user->getFirstName() . ' ' . $user->getLastName()) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user->getEmail()) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($user->getPhone()) ?></p>

        <?php if (method_exists($user, 'getCreatedAt') && $user->getCreatedAt()): ?>
            <p><strong>Registered:</strong> <?= htmlspecialchars($user->getCreatedAt()) ?></p>
        <?php endif; ?>

        <p><strong>User ID:</strong> <?= htmlspecialchars($user->getId()) ?></p>
    </div>

    <div class="btn-group">
        <a href="/projekt1/index.php" class="main-btn home-btn">Go to Main Page</a>
        <a href="/projekt1/user/order.php" class="main-btn order-btn">Book a Photoshoot</a>
        <a href="/projekt1/user/orders_history.php" class="main-btn history-btn">Order History</a>
        <a href="#" class="main-btn wish-btn" id="wish-btn">Random Wishes</a>
    </div>
    <div id="wish-result"></div>

    <a href="logout.php" class="main-btn logout">Log out</a>

    <form method="post"
          onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');"
          style="margin-top: 20px;">
        <input type="hidden" name="delete_account" value="1" />
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>" />
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
