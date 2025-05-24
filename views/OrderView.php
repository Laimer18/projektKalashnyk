<?php

class OrderView {
    private ?string $message;

    public function __construct(?string $message) {
        $this->message = $message;
    }

    public function render(): void {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Book a Photoshoot</title>
            <link rel="stylesheet" href="../css/register.css"> <!-- Assuming CSS path relative to user/order.php -->
            <link rel="stylesheet" href="../css/templatemo_style.css">
            <link rel="stylesheet" href="../css/order.css">
        </head>
        <body>
        <div class="bg-overlay"></div>
        <div class="order-form">
            <h2>Book a Photoshoot</h2>
            <?php if (!empty($this->message)): ?>
                <div class="order-message"><?= htmlspecialchars($this->message) ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <input type="text" name="name" placeholder="Your name" required>
                <input type="text" name="phone" placeholder="Phone" required>
                <input type="date" name="date" placeholder="Photoshoot date" required>
                <textarea name="details" placeholder="Order details (location, wishes, etc.)"></textarea>
                <input type="submit" value="Book">
            </form>
            <div style="text-align:center; margin-top: 18px;">
                <a href="personal_page.php" class="main-btn">Back to Account</a>
            </div>
        </div>
        </body>
        </html>
        <?php
    }
}