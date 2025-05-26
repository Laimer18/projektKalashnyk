<?php

class LoginView {
    private ?string $message;
    private array $data;
    private string $baseProjectPath;

    public function __construct(?string $message, array $data, string $baseProjectPath = '/projekt1') {
        $this->message = $message;
        $this->data = $data;
        $this->baseProjectPath = $baseProjectPath;
    }

    public function render(): void {
        $loginCssPath = $this->baseProjectPath . '/login.css';
        $registerUrl = $this->baseProjectPath . '/register';
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8" />
            <title>Login</title>
            <link rel="stylesheet" href="<?= htmlspecialchars($loginCssPath) ?>" />
        </head>
        <body>
        <div class="login-container">
            <h2>Login</h2>

            <?php if (!empty($this->message)): ?>
                <div class="error"><?= htmlspecialchars($this->message) ?></div>
            <?php endif; ?>

            <form method="post" action="">
                <input
                        type="email"
                        name="email"
                        placeholder="Email"
                        required
                        value="<?= htmlspecialchars($this->data['email'] ?? '') ?>"
                >
                <input
                        type="password"
                        name="password"
                        placeholder="Password"
                        required
                >
                <button type="submit">Login</button>
            </form>

            <a href="<?= htmlspecialchars($registerUrl) ?>" class="mini-btn">Back to Register</a>
        </div>
        </body>
        </html>
        <?php
    }
}
