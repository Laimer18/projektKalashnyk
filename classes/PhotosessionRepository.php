<?php

require_once __DIR__ . '/PhotoSession.php';

class PhotosessionRepository
{
    // PDO для роботи з базою даних
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Додає нову фотосесію в базу
    public function add(PhotoSession $session): bool
    {
        $sql = "INSERT INTO photosessions (name, email, phone, date, details, created_at) 
                VALUES (:name, :email, :phone, :date, :details, NOW())";

        $stmt = $this->pdo->prepare($sql);

        // Виконуємо підстановку значень із об'єкта PhotoSession
        return $stmt->execute([
            ':name'    => $session->getName(),
            ':email'   => $session->getEmail(),
            ':phone'   => $session->getPhone(),
            ':date'    => $session->getDate(),
            ':details' => $session->getDetails(),
        ]);
    }


    public function getByEmail(string $email): array
    {
        $sql = "SELECT id, name, email, phone, date, details, created_at FROM photosessions WHERE email = :email ORDER BY created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);

        $sessions = [];

        // Ітеруємося по результатах і створюємо об'єкти PhotoSession
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sessions[] = new PhotoSession(
                $row['name'],
                $row['email'],
                $row['phone'],
                $row['date'],
                $row['details'],
                (int)$row['id'],
                $row['created_at']
            );
        }

        return $sessions;
    }
}
