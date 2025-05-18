<?php
session_start();

require_once '../contact/db.php';
require_once 'user.php';
require_once 'user_rep.php';

$pdo = Database::getInstance();

if (isset($_SESSION['user_id'])) {
    header('Location: personal_page.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } else {
        $userRepo = new UserRepository($pdo);
        $user = $userRepo->getByEmail($email);

        // 🔍 Debug output
        echo "<pre>";
        var_dump($user);
        if ($user) {
            var_dump("Entered password: " . $password);
            var_dump("Stored hash: " . $user->getPassword());
            var_dump("Verify result: ", password_verify($password, $user->getPassword()));
        }
        echo "</pre>";
        exit;

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
