<?php
session_start();
if (isset($_SESSION['user'])) {
    header("Location: personal_page.php");
    exit;
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

    $first_name = trim($_POST["first_name"]);
    $last_name  = trim($_POST["last_name"]);
    $email      = trim($_POST["email"]);
    $phone      = trim($_POST["phone"]);
    $password   = trim($_POST["password"]);
    $confirm    = trim($_POST["confirm"]);

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        $message = "A user with this email already exists.";
    } elseif ($password !== $confirm) {
        $message = "Passwords do not match.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, password) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$first_name, $last_name, $email, $phone, $hash])) {
            $_SESSION['user'] = [
                'first_name' => $first_name,
                'last_name'  => $last_name,
                'email'      => $email,
                'phone'      => $phone
            ];
            header("Location: personal_page.php");
            exit;
        } else {
            $message = "Registration failed!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration</title>
    <link rel="stylesheet" href="../css/register.css">  
    
</head>
<body>
    <div class="bg-overlay"></div>
    <div class="container">
        <div class="registration-form" style="max-width:400px;margin:60px auto;background:#222;padding:32px 30px 24px 30px;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,0.08);">
            <h2>REGISTRATION</h2>
            <?php if ($message): ?>
                <div class="message" style="color:#bfa046;font-weight:bold;margin-bottom:15px;"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <input type="text" name="first_name" placeholder="First name" required>
                <input type="text" name="last_name" placeholder="Last name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm" placeholder="Confirm" required>
                <input type="text" name="phone" placeholder="Phone">
                <input type="submit" value="Register">
            </form>
            <div class="user-links">
                <a href="/projekt1/user/users.php" class="mini-btn">All Users</a>
                <a href="login.php" class="mini-btn">Login</a>
                <a href="/projekt1/index.php" class="mini-btn">Go to Main Page</a>
            </div>
        </div>
    </div>
</body>
</html>