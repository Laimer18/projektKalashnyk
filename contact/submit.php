
<?php
require_once 'db.php';
require_once '../user/user.php';
require_once '../user/user_rep.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST["first_name"]);
    $last_name  = trim($_POST["last_name"]);
    $email      = trim($_POST["email"]);
    $phone      = trim($_POST["phone"]);
    $password   = trim($_POST["password"]);
    $confirm    = trim($_POST["confirm"]);
    $userRepo = new UserRepository($pdo);

    foreach ($userRepo->getAll() as $user) {
        if ($user->email === $email) {
            die("A user with this email already exists.");
        }
    }

    $user = new User($first_name, $last_name, $email, $phone);
    $userRepo->add($user);

    echo "Thank you! You have successfully signed up for a photo shoot.";
} else {
    echo "Error: Form not submitted.";
}
exit;
?>