<?php

class PersonalPageView
{
    private array $user;
    private string $csrfToken;

    public function __construct(array $user, string $csrfToken)
    {
        $this->user = $user;
        $this->csrfToken = $csrfToken;
    }

    public function render(): void
    {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8" />
            <title>Personal Page</title>
            <link rel="stylesheet" href="../css/personal_page.css" /> <!-- Adjust path as necessary -->
        </head>
        <body>
        <div class="bg-overlay"></div>
        <div class="personal-container">
            <h2>Welcome, <?= htmlspecialchars($this->user['first_name']) ?>!</h2>
            <div class="personal-info">
                <p><strong>Name:</strong> <?= htmlspecialchars($this->user['first_name'] . ' ' . $this->user['last_name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($this->user['email']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($this->user['phone'] ?? 'N/A') ?></p> <!-- Handle potentially missing phone -->
                <?php if (!empty($this->user['created_at'])): ?>
                    <p><strong>Registered at:</strong> <?= htmlspecialchars($this->user['created_at']) ?></p>
                <?php endif; ?>
                <p><strong>User ID:</strong> <?= htmlspecialchars($this->user['id']) ?></p>
            </div>
            <div class="btn-group">
                <a href="/projekt1/index.php" class="main-btn home-btn">Home</a>
                <?php
                    $editAccountUrl = 'edit_account.php'; // Фоллбэк
                    if (class_exists('NavigationHelper')) {
                        $baseProjectPath = '/projekt1'; // Убедитесь, что это соответствует $base_project_url_path в index.php
                        $editAccountUrl = NavigationHelper::getAssetUrl('user/edit-account', $baseProjectPath);
                    }
                ?>
                <a href="<?= htmlspecialchars($editAccountUrl) ?>" class="main-btn edit-account-btn">Edit Account</a>
            </div>
            <div id="wish-result"></div>

            <a href="logout.php" class="main-btn logout">Logout</a> <!-- Adjust path as necessary -->

            <form method="post" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');" style="margin-top: 20px;">
                <input type="hidden" name="delete_account" value="1" />
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($this->csrfToken) ?>" />
                <button type="submit" style="background-color: #e74c3c; color: white; border: none; padding: 10px 15px; cursor: pointer;">
                    Delete Account
                </button>
            </form>
        </div>

        </body>
        </html>
        <?php
    }
}