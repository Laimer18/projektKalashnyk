<?php

require_once __DIR__ . '/../contact/db.php';
require_once __DIR__ . '/../user/user_rep.php'; // Assuming UserRepository is in user_rep.php

class UserController {
    private $userRepository;

    public function __construct() {
        $pdo = Database::getInstance();
        $this->userRepository = new UserRepository($pdo);
    }

    public function deleteUser($id) {
        if (!isset($id) || !is_numeric($id)) {
            // It's better to handle errors more gracefully,
            // but for now, we'll stick to the original script's behavior.
            // In a real application, you might throw an exception or return an error message.
            die("User ID not specified or invalid.");
        }

        $this->userRepository->delete((int)$id);
        header("Location: users.php"); // Assuming users.php is in the same directory as the original delete_user.php
        exit;
    }
}