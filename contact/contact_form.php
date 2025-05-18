<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Contact Form</title>
</head>
<body>
<h2>Contact Us</h2>
<form action="submit.php" method="post">
    <input type="text" name="name" placeholder="Your Name" required /><br>
    <input type="email" name="email" placeholder="Your Email" required /><br>
    <input type="text" name="subject" placeholder="Subject" required /><br>
    <textarea name="message" placeholder="Your Message" required></textarea><br>
    <button type="submit">Send</button>
</form>
</body>
</html>
