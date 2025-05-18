<?php

require_once 'Photosession.php';

class PhotosessionRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function add(Photosession $session): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO photosessions (name, email, phone, date, details) VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $session->getName(),
            $session->getEmail(),
            $session->getPhone(),
            $session->getDate(),
            $session->getDetails()
        ]);
    }

    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM photosessions");
        $sessions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sessions[] = new Photosession(
                $row['name'],
                $row['email'],
                $row['phone'],
                $row['date'],
                $row['details'],
                (int)$row['id']
            );
        }
        return $sessions;
    }

    public function getByEmail(string $email): array {
        $stmt = $this->pdo->prepare("SELECT * FROM photosessions WHERE email = ?");
        $stmt->execute([$email]);
        $sessions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sessions[] = new Photosession(
                $row['name'],
                $row['email'],
                $row['phone'],
                $row['date'],
                $row['details'],
                (int)$row['id']
            );
        }
        return $sessions;
    }
}