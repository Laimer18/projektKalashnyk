<?php
require_once '../contact/db.php';
require_once 'user_rep.php';

$pdo = Database::getInstance();

if (!isset($_GET["id"])) {
    die("User ID not specified.");
}

$id = (int)$_GET["id"];
$userRepo = new UserRepository($pdo);
$userRepo->delete($id);
header("Location: users.php");
exit;
