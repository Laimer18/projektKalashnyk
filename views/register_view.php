<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Register</title>
    <link rel="stylesheet" href="../css/register.css" /> <!-- Adjust path if needed -->
</head>
<body>
<div class="bg-overlay"></div>
<div class="container">
    <div class="registration-form">
        <h2>Register</h2>

        <?php if (!empty($message)): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <input type="text" name="first_name" placeholder="First Name " value="<?= htmlspecialchars($data['first_name']) ?>" required>
            <input type="text" name="last_name" placeholder="Last Name " value="<?= htmlspecialchars($data['last_name']) ?>" required>
            <input type="email" name="email" placeholder="Email " value="<?= htmlspecialchars($data['email']) ?>" required>
            <input type="text" name="phone" placeholder="Phone" value="<?= htmlspecialchars($data['phone']) ?>">
            <input type="password" name="password" placeholder="Password " required>
            <input type="password" name="confirm" placeholder="Confirm Password " required>

            <input type="submit" value="Register">
        </form>

        <div class="user-links">
            <a href="../admin/edit_user.php" class="mini-btn">All Users</a>
            <a href="login.php" class="mini-btn">Login</a>
            <a href="../index.php" class="mini-btn">Home</a>
        </div>
    </div>
</div>
</body>
</html>
