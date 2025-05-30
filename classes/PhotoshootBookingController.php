<?php
require_once '../contact/db.php';
require_once '../classes/PhotoSession.php';
require_once '../classes/PhotoSession.php.php';

class PhotoshootBookingController
{
    private PDO $pdo;
    private PhotosessionRepository $repo;
    private string $message = '';

    public function __construct()
    {
        $this->pdo = Database::getInstance();
        $this->repo = new PhotosessionRepository($this->pdo);
    }

    public function handleRequest(): void
    {
        session_start();

        if (!isset($_SESSION['user'])) {
            header("Location: register1.php");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->processForm($_POST, $_SESSION['user']['email'] ?? '');
        }
    }

    private function processForm(array $postData, string $email): void
    {
        $name    = trim($postData["name"] ?? '');
        $phone   = trim($postData["phone"] ?? '');
        $date    = trim($postData["date"] ?? '');
        $details = trim($postData["details"] ?? '');

        if (!$email) {
            $this->message = "User email not found in session.";
            return;
        }

        if (empty($name) || empty($phone) || empty($date)) {
            $this->message = "Please fill in all required fields.";
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->message = "Invalid user email format.";
            return;
        }

        $session = new Photosession($name, $email, $phone, $date, $details);

        if ($this->repo->add($session)) {
            $this->message = "Your order has been received! We will contact you soon.";
        } else {
            $this->message = "There was an error submitting your order. Please try again.";
        }
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
