<?php
session_start();
if (isset($_SESSION['user'])) {
    header("Location: personal_page.php");
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../contact/db.php';
require_once 'user.php';
require_once 'user_rep.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = trim($_POST["first_name"]);
    $last_name  = trim($_POST["last_name"]);
    $email      = trim($_POST["email"]);
    $phone      = trim($_POST["phone"]);
    $password   = trim($_POST["password"]);
    $confirm    = trim($_POST["confirm"]);

    $userRepo = new UserRepository(Database::getInstance());

    // Перевірка наявності користувача з таким email
    foreach ($userRepo->getAll() as $user) {
        if ($user->getEmail() === $email) {
            $message = "A user with this email already exists.";
            break;
        }
    }

    if (!$message && $password !== $confirm) {
        $message = "Passwords do not match.";
    }

    if (!$message) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $newUser = new User($first_name, $last_name, $email, $phone, null, $hash);

        if ($userRepo->add($newUser)) {
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
        <h2 style="text-align:center;color:#fff;">REGISTRATION</h2>

        <?php if ($message): ?>
            <div class="message" style="color:#bfa046;font-weight:bold;margin-bottom:15px;">
                <?= htmlspecialchars($message) ?>
            </div>
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

        <div class="user-links" style="margin-top:15px;text-align:center;">
            <a href="/projekt1/user/users.php" class="mini-btn">All Users</a> |
            <a href="login.php" class="mini-btn">Login</a> |
            <a href="/projekt1/index.php" class="mini-btn">Go to Main Page</a>
        </div>
    </div>
</div>
</body>
</html>
