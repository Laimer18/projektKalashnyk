<?php
session_start();
if (isset($_SESSION['user'])) {
    header("Location: personal_page.php");
    exit;
}
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = 'localhost';
    $db   = 'photosite';
    $user = 'root';
    $pass = '';
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    try {
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("DB connection failed: " . $e->getMessage());
    }
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'first_name' => $user['first_name'],
            'last_name'  => $user['last_name'],
            'email'      => $user['email'],
            'phone'      => $user['phone']
        ];
        header("Location: personal_page.php");
        exit;
    } else {
        $message = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../css/register.css">
    <link rel="stylesheet" href="../css/templatemo_style.css">
</head>
<body>
    <div class="bg-overlay"></div>
    <div class="container">
        <div class="registration-form" style="max-width:400px;margin:60px auto;background:#222;padding:32px 30px 24px 30px;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,0.08);">
            <h2>LOGIN</h2>
            <?php if ($message): ?>
                <div class="message" style="color:#a33;font-weight:bold;margin-bottom:15px;"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="submit" value="Login">
            </form>
            <div class="user-links">
                <a href="register1.php" class="mini-btn">Register</a>
                <a href="/projekt1/index.php" class="mini-btn">Go to Main Page</a>
            </div>
        </div>
    </div>
</body>
</html>