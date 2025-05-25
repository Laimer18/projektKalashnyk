<?php

// NavigationHelper должен быть доступен через автозагрузчик
// или подключен до вызова этого файла.
// BASE_PATH должен быть определен в index.php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__)); // views/ -> /
}
if (!class_exists('NavigationHelper')) {
    $navHelperPath = BASE_PATH . '/classes/NavigationHelper.php';
    if (file_exists($navHelperPath)) {
        require_once $navHelperPath;
    }
}

class RegisterView
{
    private string $message;
    private array $formData;
    private string $baseProjectUrlPath;

    public function __construct(string $message, array $formData, string $baseProjectUrlPath = '/projekt1')
    {
        $this->message = $message;
        $this->formData = $formData;
        $this->baseProjectUrlPath = $baseProjectUrlPath; // Сохраняем для использования в render
    }

    public function render(): void
    {
        $cssUrl = NavigationHelper::getAssetUrl('css/register.css', $this->baseProjectUrlPath);
        // Предполагаемые маршруты для ссылок. Их нужно будет определить в index.php
        $allUsersUrl = NavigationHelper::getAssetUrl('admin/users', $this->baseProjectUrlPath); // Пример: /admin/users
        $loginUrl    = NavigationHelper::getAssetUrl('login', $this->baseProjectUrlPath);
        $homeUrl     = NavigationHelper::getAssetUrl('', $this->baseProjectUrlPath); // Для главной страницы

        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8" />
            <title>Register</title>
            <link rel="stylesheet" href="<?= htmlspecialchars($cssUrl) ?>" />
        </head>
        <body>
        <div class="bg-overlay"></div>
        <div class="container">
            <div class="registration-form">
                <h2>Register</h2>

                <?php if (!empty($this->message)): ?>
                    <div class="message"><?= htmlspecialchars($this->message) ?></div>
                <?php endif; ?>

                <form method="post" action="">
                    <input type="text" name="first_name" placeholder="First Name" value="<?= htmlspecialchars($this->formData['first_name']) ?>" required>
                    <input type="text" name="last_name" placeholder="Last Name" value="<?= htmlspecialchars($this->formData['last_name']) ?>" required>
                    <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($this->formData['email']) ?>" required>
                    <input type="text" name="phone" placeholder="Phone" value="<?= htmlspecialchars($this->formData['phone']) ?>">
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="password" name="confirm" placeholder="Confirm Password" required>

                    <input type="submit" value="Register">
                </form>

                <div class="user-links">
                    <a href="<?= htmlspecialchars($loginUrl) ?>" class="mini-btn">Login</a>
                    <a href="<?= htmlspecialchars($homeUrl) ?>" class="mini-btn">Home</a>
                </div>
            </div>
        </div>
        </body>
        </html>
        <?php
    }
}