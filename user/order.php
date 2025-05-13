<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = 'localhost';
    $db   = 'photosite';
    $user = 'root';
    $pass = '';
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    try {
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("DB connection failed: " . $e->getMessage());
    }

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $date = trim($_POST["date"]);
    $details = trim($_POST["details"]);

    $stmt = $pdo->prepare("INSERT INTO photosessions (name, email, phone, date, details) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $phone, $date, $details])) {
        $message = "Your order has been received! We will contact you soon.";
    } else {
        $message = "There was an error submitting your order. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book a Photoshoot</title>
    <link rel="stylesheet" href="../css/register.css">
    <link rel="stylesheet" href="../css/templatemo_style.css">
    <style>
.order-form {
    max-width: 420px;
    margin: 80px auto;
    background: #1a1a1a;
    padding: 32px 30px 24px 30px;
    border-radius: 20px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.18);
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.order-form h2 {
    color: #bfa046;
    font-size: 2.2rem;
    font-weight: bold;
    letter-spacing: 4px;
    margin-bottom: 28px;
    text-transform: uppercase;
}

.order-form form {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 18px;
    align-items: center;
}

.order-form input[type="text"],
.order-form input[type="email"],
.order-form input[type="date"],
.order-form textarea {
    width: 100%;
    max-width: 100%;
    padding: 12px;
    font-size: 16px;
    border: 1.5px solid #bfa046;
    border-radius: 10px;
    background: #fff;
    color: #222;
    box-sizing: border-box;
    outline: none;
    transition: border 0.2s, box-shadow 0.2s, background 0.2s;
}

.order-form textarea {
    resize: vertical;
    min-height: 60px;
}

.order-form input[type="submit"] {
    margin-top: 18px;
    background: #bfa046;
    color: #fff;
    border-radius: 10px;
    font-size: 1.2rem;
    font-weight: bold;
    letter-spacing: 1px;
    border: none;
    text-align: center;
    box-sizing: border-box;
    cursor: pointer;
    padding: 14px 0;
    transition: background 0.2s, color 0.2s;
    width: 100%;
    max-width: 260px;
    display: block;
    margin-left: auto;
    margin-right: auto;
}

.order-form input[type="submit"]:hover {
    background: #a88c2c;
    color: #fff;
}

.order-form .main-btn {
    width: 100%;
    max-width: 260px;
    margin: 24px auto 0 auto;
    font-size: 1.2rem;
    padding: 14px 0;
    display: block;
}

@media (max-width: 500px) {
    .order-form {
        max-width: 98vw;
        padding: 12px 2vw 12px 2vw;
    }
    .order-form input,
    .order-form textarea,
    .order-form input[type="submit"],
    .order-form .main-btn {
        max-width: 98vw;
        font-size: 1rem;
        padding: 10px 0;
    }
}
    </style>
</head>
<body>
    <div class="bg-overlay"></div>
    <div class="order-form">
        <h2>Book </h2>
        <?php if ($message): ?>
            <div class="order-message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <input type="text" name="name" placeholder="Your name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="phone" placeholder="Phone" required>
            <input type="date" name="date" placeholder="Photoshoot date" required>
            <textarea name="details" placeholder="Order details (location, wishes, etc.)"></textarea>
            <input type="submit" value="Book">
        </form>
        <div style="text-align:center;margin-top:18px;">
            <a href="personal_page.php" class="main-btn" ">Back to Account</a>
        </div>
    </div>
</body>
</html>