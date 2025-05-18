<?php
require_once '../contact/db.php';       // Підключаємо клас Database
require_once 'user.php';               // Клас User
require_once 'user_rep.php';           // Клас UserRepository

// Отримуємо PDO-з'єднання через Database::getInstance()
$userRepo = new UserRepository(Database::getInstance());

// Отримуємо всіх користувачів
$users = $userRepo->getAll();
?>
<a href="register1.php">Go back to registration</a>
<table border="1">
    <tr>
        <th>ID</th><th>Name</th><th>Lastname</th><th>Email</th><th>Phone</th><th>Actions</th>
    </tr>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user->getId()) ?></td>
            <td><?= htmlspecialchars($user->getFirstName()) ?></td>
            <td><?= htmlspecialchars($user->getLastName()) ?></td>
            <td><?= htmlspecialchars($user->getEmail()) ?></td>
            <td><?= htmlspecialchars($user->getPhone()) ?></td>
            <td>
                <a href="edit_user.php?id=<?= $user->getId() ?>">Edit</a>
                <a href="delete_user.php?id=<?= $user->getId() ?>" onclick="return confirm('Delete?')">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
