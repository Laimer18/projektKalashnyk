<?php
session_start();

// Protect the page: redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../contact/db.php';       // Database connection
require_once 'user.php';                 // User class
require_once 'user_rep.php';             // UserRepository class

// Get PDO instance
$userRepo = new UserRepository(Database::getInstance());

// Fetch all users
$users = $userRepo->getAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Users List</title>
    <link rel="stylesheet" href="../login.css"> <!-- Adjust path to your CSS -->
</head>
<body>

<h1>Users List</h1>

<a href="register1.php">Back to Registration</a>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
    <tr>
        <th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Phone</th><th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($users) === 0): ?>
        <tr><td colspan="6">No users found.</td></tr>
    <?php else: ?>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user->getId()) ?></td>
                <td><?= htmlspecialchars($user->getFirstName()) ?></td>
                <td><?= htmlspecialchars($user->getLastName()) ?></td>
                <td><?= htmlspecialchars($user->getEmail()) ?></td>
                <td><?= htmlspecialchars($user->getPhone()) ?></td>
                <td>
                    <a href="edit_user.php?id=<?= urlencode($user->getId()) ?>">Edit</a> |
                    <a href="delete_user.php?id=<?= urlencode($user->getId()) ?>" onclick="return confirm('Delete user?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

</body>
</html>
