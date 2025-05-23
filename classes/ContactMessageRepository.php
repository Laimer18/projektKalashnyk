<?php
class ContactMessageRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function add(ContactMessage $contactMessage): bool
    {
        $sql = "INSERT INTO contacts (first_name, last_name, email, phone, question, created_at) 
                VALUES (:first_name, :last_name, :email, :phone, :question, NOW())";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':first_name' => $contactMessage->getFirstName(),
            ':last_name'  => $contactMessage->getLastName(),
            ':email'      => $contactMessage->getEmail(),
            ':phone'      => $contactMessage->getPhone(),
            ':question'   => $contactMessage->getQuestion(),
        ]);
    }
}