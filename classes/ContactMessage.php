<?php
require_once 'ContactMessage.php';

class ContactMessageRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function add(ContactMessage $message): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO contact_messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())"
        );
        return $stmt->execute([
            $message->getName(),
            $message->getEmail(),
            $message->getSubject(),
            $message->getMessage()
        ]);
    }

    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
        $messages = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $messages[] = new ContactMessage(
                $row['name'],
                $row['email'],
                $row['subject'],
                $row['message'],
                $row['created_at'],
                (int)$row['id']
            );
        }
        return $messages;
    }
}