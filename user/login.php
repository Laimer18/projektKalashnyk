<?php
session_start();

require_once '../contact/db.php';
require_once 'user.php';
require_once 'user_rep.php';

$pdo = Database::getInstance();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } else {
        $userRepo = new UserRepository($pdo);
        $user = $userRepo->getByEmail($email);

        if ($user && password_verify($password, $user->getPassword())) {
            $_SESSION['user_id'] = $user->getId();
            header('Location: personal_page.php');
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Login</title>
    <link rel="stylesheet" href="../css/login.css" />
</head>
<body>
<form method="post" action="">
    <input type="email" name="email" placeholder="Email" required /><br />
    <input type="password" name="password" placeholder="Password" required /><br />
    <button type="submit">Login</button>
</form>

<?php if ($error): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
</body>
</html>
