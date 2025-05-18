<?php
require_once '../contact/db.php'; // Database connection
require_once 'user.php';          // User class
require_once 'user_rep.php';      // UserRepository class

// Get PDO instance
$pdo = Database::getInstance();

$userRepo = new UserRepository($pdo);
$errorMessage = null;

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)($_POST['id'] ?? 0);
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // Validate data
    if (!$firstName || !$lastName || !$email) {
        $errorMessage = "First name, last name, and email are required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Invalid email format.";
    } elseif (mb_strlen($firstName) > 50 || mb_strlen($lastName) > 50) {
        $errorMessage = "First name or last name must not exceed 50 characters.";
    } elseif ($phone && mb_strlen($phone) > 20) {
        $errorMessage = "Phone number must not exceed 20 characters.";
    }

    // If no errors, update user
    if (!$errorMessage) {
        $user = new User($firstName, $lastName, $email, $phone, $id);
        if ($userRepo->update($user)) {
            header('Location: users.php');
            exit;
        } else {
            $errorMessage = "Failed to save user data.";
        }
    }
}

// Get user ID from GET request
$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    die('Invalid user ID.');
}

$user = $userRepo->getById($id);
if (!$user) {
    die('User not found.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit User</title>
</head>
<body>
<h1>Edit User</h1>

<?php if ($errorMessage): ?>
    <p style="color: red;"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<form method="post" action="">
    <input type="hidden" name="id" value="<?= htmlspecialchars($user->getId(), ENT_QUOTES, 'UTF-8') ?>" />
    <label>
        First Name:<br />
        <input type="text" name="first_name" value="<?= htmlspecialchars($user->getFirstName(), ENT_QUOTES, 'UTF-8') ?>" required />
    </label>
    <br /><br />
    <label>
        Last Name:<br />
        <input type="text" name="last_name" value="<?= htmlspecialchars($user->getLastName(), ENT_QUOTES, 'UTF-8') ?>" required />
    </label>
    <br /><br />
    <label>
        Email:<br />
        <input type="email" name="email" value="<?= htmlspecialchars($user->getEmail(), ENT_QUOTES, 'UTF-8') ?>" required />
    </label>
    <br /><br />
    <label>
        Phone:<br />
        <input type="text" name="phone" value="<?= htmlspecialchars($user->getPhone(), ENT_QUOTES, 'UTF-8') ?>" />
    </label>
    <br /><br />
    <button type="submit">Save</button>
</form>
</body>
</html>
