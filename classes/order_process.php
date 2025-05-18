<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: register1.php");
    exit;
}

require_once '../contact/db.php';
require_once '../classes/Photosession.php';
require_once '../classes/PhotosessionRepository.php';

$pdo = Database::getInstance();

$email = $_SESSION['user']['email'] ?? '';

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$date = $_POST['date'] ?? '';
$details = trim($_POST['details'] ?? '');

if (!$name || !$phone || !$date) {

    die('Please fill all required fields.');
}

$photosession = new PhotoSession($name, $email, $phone, $date, $details);

$repo = new PhotosessionRepository($pdo);

if ($repo->add($photosession)) {
    header("Location: orders_history.php");
    exit;
} else {
    die('Failed to save order. Please try again.');
}
