<?php

class OrdersHistoryView
{
    private array $orders;

    public function __construct(array $orders)
    {
        $this->orders = $orders;
    }

    public function render(): void
    {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Order History</title>
            <link rel="stylesheet" href="../css/register.css"> <!-- Adjust path as necessary -->
            <link rel="stylesheet" href="../css/templatemo_style.css">
            <link rel="stylesheet" href="../css/orders_history.css">
        </head>
        <body>
        <div class="bg-overlay"></div>
        <div class="orders-container">
            <h2>Order History</h2>
            <?php if (empty($this->orders)): ?>
                <p style="text-align:center;">You have no orders yet.</p>
            <?php else: ?>
                <?php foreach ($this->orders as $order): ?>
                    <div class="order-item">
                        <div><strong>Photoshoot date:</strong> <?= htmlspecialchars($order->getDate()) ?></div>
                        <div><strong>Phone:</strong> <?= htmlspecialchars($order->getPhone()) ?></div>
                        <div><strong>Details:</strong> <?= nl2br(htmlspecialchars($order->getDetails())) ?></div>
                        <div><strong>Photographer:</strong> Sofia</div> <!-- Assuming this is static -->
                        <div>
                            <strong>Reserved:</strong>
                            <?php
                            // Ensure Photosession class has getCreatedAt method or handle appropriately
                            if (method_exists($order, 'getCreatedAt') && $order->getCreatedAt()) {
                                echo htmlspecialchars(date('Y-m-d', strtotime($order->getCreatedAt())));
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <a href="personal_page.php" class="main-btn" style="margin-top:24px;">Back to Account</a> <!-- Adjust path as necessary -->
        </div>
        </body>
        </html>
        <?php
    }
}