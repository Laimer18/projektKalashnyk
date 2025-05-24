<?php

class EditUserView {
    private ?User $user;
    private ?string $errorMessage;

    public function __construct(?User $user, ?string $errorMessage) {
        $this->user = $user;
        $this->errorMessage = $errorMessage;
    }

    public function render(): void {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8" />
            <title>Edit User</title>
        </head>
        <body>
        <h1>Edit User</h1>

        <?php if ($this->errorMessage): ?>
            <p style="color: red;"><?= htmlspecialchars($this->errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <?php if ($this->user): ?>
            <form method="post" action="">
                <input type="hidden" name="id" value="<?= htmlspecialchars($this->user->getId(), ENT_QUOTES, 'UTF-8') ?>" />
                <label>
                    First Name:<br />
                    <input type="text" name="first_name" value="<?= htmlspecialchars($this->user->getFirstName(), ENT_QUOTES, 'UTF-8') ?>" required />
                </label>
                <br /><br />
                <label>
                    Last Name:<br />
                    <input type="text" name="last_name" value="<?= htmlspecialchars($this->user->getLastName(), ENT_QUOTES, 'UTF-8') ?>" required />
                </label>
                <br /><br />
                <label>
                    Email:<br />
                    <input type="email" name="email" value="<?= htmlspecialchars($this->user->getEmail(), ENT_QUOTES, 'UTF-8') ?>" required />
                </label>
                <br /><br />
                <label>
                    Phone:<br />
                    <input type="text" name="phone" value="<?= htmlspecialchars($this->user->getPhone(), ENT_QUOTES, 'UTF-8') ?>" />
                </label>
                <br /><br />
                <button type="submit">Save</button>
            </form>
        <?php endif; ?>

        </body>
        </html>
        <?php
    }
}