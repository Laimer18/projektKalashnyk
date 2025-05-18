<?php
require_once 'EditUserController.php';

$controller = new EditUserController();
$controller->handleRequest();

$user = $controller->getUser();
$errorMessage = $controller->getErrorMessage();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit User</title>
</head>
<body>
<h1>Edit User</h1>

<?php if ($errorMessage): ?>
    <p style="color: red;"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if ($user): ?>
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
<?php endif; ?>

</body>
</html>
