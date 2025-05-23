<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Login</title>
    <link rel="stylesheet" href="../login.css" />


</head>
<body>
<div class="login-container">
    <h2>Login</h2>

    <?php if (!empty($message)): ?>
        <div class="error"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <input
                type="email"
                name="email"
                placeholder="Email"
                required
                value="<?= htmlspecialchars($data['email'] ?? '') ?>"
        >
        <input
                type="password"
                name="password"
                placeholder="Password"
                required
        >
        <button type="submit">Login</button>
    </form>

    <a href="register1.php" class="mini-btn">Back to Register</a>
</div>
</body>
</html>
