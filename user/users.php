<?php
require_once '../contact/db.php';
require_once 'user.php';
require_once 'user_rep.php';

$userRepo = new UserRepository($pdo);
$users = $userRepo->getAll();
?>
<a href="register1.php">Go back to registration</a>
<table border="1">
    <tr>
        <th>ID</th><th>Name</th><th>Lastname</th><th>Email</th><th>Phone</th><th>Actions</th>
    </tr>
    <?php foreach ($users as $user): ?>
    <tr>
        <td><?= htmlspecialchars($user->id) ?></td>
        <td><?= htmlspecialchars($user->first_name) ?></td>
        <td><?= htmlspecialchars($user->last_name) ?></td>
        <td><?= htmlspecialchars($user->email) ?></td>
        <td><?= htmlspecialchars($user->phone) ?></td>
        <td>
            <a href="edit_user.php?id=<?= $user->id ?>">Edit</a>
            <a href="delete_user.php?id=<?= $user->id ?>" onclick="return confirm('Delete?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>