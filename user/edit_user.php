
<?php
require_once '../contact/db.php';
require_once 'user.php';
require_once 'user_rep.php';

$userRepo = new UserRepository($pdo);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST["id"];
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $user = new User($first_name, $last_name, $email, $phone, $id);
    $userRepo->update($user);
    header("Location: users.php");
    exit;
}

if (!isset($_GET["id"])) {
    die("User ID not specified.");
}

$id = (int)$_GET["id"];
$user = $userRepo->getById($id);

if (!$user) {
    die("User not found.");
}
?>
<form method="post">
    <input type="hidden" name="id" value="<?= htmlspecialchars($user->id) ?>">
    <input type="text" name="first_name" value="<?= htmlspecialchars($user->first_name) ?>" required>
    <input type="text" name="last_name" value="<?= htmlspecialchars($user->last_name) ?>" required>
    <input type="email" name="email" value="<?= htmlspecialchars($user->email) ?>" required>
    <input type="text" name="phone" value="<?= htmlspecialchars($user->phone) ?>">
    <input type="submit" value="Save">
</form>