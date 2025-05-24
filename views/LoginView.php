<?php

class LoginView {
    private ?string $message;
    private array $data;

    public function __construct(?string $message, array $data) {
        $this->message = $message;
        $this->data = $data;
    }

    public function render(): void {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8" />
            <title>Login</title>
            <?php
                // Предполагаем, что NavigationHelper доступен, так как он используется в других местах
                // и $base_project_url_path определен в index.php и как-то доступен здесь
                // (например, через константу или переданный параметр).
                // Для простоты, если NavigationHelper не определен, используем относительный путь,
                // который должен работать, если index.php в корне.
                $loginCssPath = 'login.css'; // Базовый путь
                if (class_exists('NavigationHelper')) {
                    // $baseProjectPathForUrls должен быть определен или доступен.
                    // Если LoginView вызывается из LoginController, который вызывается из index.php,
                    // то $base_project_url_path из index.php здесь напрямую недоступен.
                    // Нужно либо передавать его, либо использовать фиксированное значение.
                    // Для простоты используем фиксированное значение, предполагая, что оно не меняется часто.
                    $baseProjectPath = '/projekt1'; // Убедитесь, что это соответствует $base_project_url_path в index.php
                    $loginCssPath = NavigationHelper::getAssetUrl('login.css', $baseProjectPath);
                }
            ?>
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

            <?php
                // Используем NavigationHelper для формирования URL к маршруту регистрации
                $registerUrl = 'register1.php'; // Фоллбэк, если NavigationHelper недоступен
                if (class_exists('NavigationHelper')) {
                    $baseProjectPath = '/projekt1'; // Убедитесь, что это соответствует $base_project_url_path в index.php
                    // Предполагаем, что маршрут регистрации это /register
                    $registerUrl = NavigationHelper::getAssetUrl('register', $baseProjectPath);
                }
            ?>
            <a href="<?= htmlspecialchars($registerUrl) ?>" class="mini-btn">Back to Register</a>
        </div>
        </body>
        </html>
        <?php
    }
}