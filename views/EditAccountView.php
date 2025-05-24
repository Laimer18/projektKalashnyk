<?php

class EditAccountView
{
    private ?User $user;
    private string $errorMessage;
    private string $successMessage;
    private string $csrfToken;

    public function __construct(?User $user, string $errorMessage, string $successMessage, string $csrfToken)
    {
        $this->user = $user;
        $this->errorMessage = $errorMessage;
        $this->successMessage = $successMessage;
        $this->csrfToken = $csrfToken;
    }

    public function render(): void
    {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8" />
            <title>Edit Account</title>
            <!-- Using the same CSS as personal_page.css -->
            <link rel="stylesheet" href="../css/personal_page.css" />
            <style>
                /* Additional styles for form if needed, or can be added to personal_page.css */
                .edit-account-container {
                    padding: 20px;
                    background-color: #fff; /* Similar to personal-container */
                    border-radius: 8px;
                    box-shadow: 0 0 15px rgba(0,0,0,0.1);
                    max-width: 600px;
                    margin: 40px auto;
                }
                .edit-account-container h2 {
                    text-align: center;
                    color: #333;
                    margin-bottom: 20px;
                }
                .edit-account-container form label {
                    display: block;
                    margin-bottom: 5px;
                    font-weight: bold;
                    color: #555;
                }
                .edit-account-container form input[type="text"],
                .edit-account-container form input[type="email"],
                .edit-account-container form input[type="password"] {
                    width: calc(100% - 22px);
                    padding: 10px;
                    margin-bottom: 15px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    box-sizing: border-box;
                }
                .edit-account-container form button[type="submit"] {
                    background-color: #5cb85c; /* Green */
                    color: white;
                    padding: 10px 20px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 16px;
                    display: block;
                    width: auto;
                    margin: 10px auto 0;
                }
                .edit-account-container form button[type="submit"]:hover {
                    background-color: #4cae4c;
                }
                .message {
                    padding: 10px;
                    margin-bottom: 15px;
                    border-radius: 4px;
                    text-align: center;
                }
                .error-message {
                    background-color: #f2dede;
                    color: #a94442;
                    border: 1px solid #ebccd1;
                }
                .success-message {
                    background-color: #dff0d8;
                    color: #3c763d;
                    border: 1px solid #d6e9c6;
                }
                .password-section {
                    margin-top: 20px;
                    padding-top: 15px;
                    border-top: 1px solid #eee;
                }
                .password-section h3 {
                    font-size: 1.2em;
                    color: #444;
                    margin-bottom: 10px;
                }
            </style>
        </head>
        <body>
        <div class="bg-overlay"></div> <!-- Assuming bg-overlay is from personal_page.css -->
        <div class="edit-account-container">
            <h2>Edit Your Account</h2>

            <?php if ($this->errorMessage): ?>
                <p class="message error-message"><?= htmlspecialchars($this->errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <?php if ($this->successMessage): ?>
                <p class="message success-message"><?= htmlspecialchars($this->successMessage, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <?php if ($this->user): ?>
                <form method="post" action="">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($this->csrfToken, ENT_QUOTES, 'UTF-8') ?>" />

                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($this->user->getFirstName(), ENT_QUOTES, 'UTF-8') ?>" required />

                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($this->user->getLastName(), ENT_QUOTES, 'UTF-8') ?>" required />

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($this->user->getEmail(), ENT_QUOTES, 'UTF-8') ?>" required />

                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($this->user->getPhone() ?? '', ENT_QUOTES, 'UTF-8') ?>" />
                    
                    <div class="password-section">
                        <h3>Change Password (optional)</h3>
                        <label for="current_password">Current Password:</label>
                        <input type="password" id="current_password" name="current_password" />

                        <label for="new_password">New Password:</label>
                        <input type="password" id="new_password" name="new_password" />

                        <label for="confirm_password">Confirm New Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" />
                    </div>

                    <button type="submit">Save Changes</button>
                </form>
            <?php else: ?>
                <p class="message error-message">Could not load user data to edit.</p>
            <?php endif; ?>
            <div style="text-align: center; margin-top: 20px;">
                <a href="personal_page.php" class="main-btn">Back to Personal Page</a>
            </div>
        </div>
        </body>
        </html>
        <?php
    }
}