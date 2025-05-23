<?php

require_once '../contact/db.php';
require_once 'user.php';

class UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Додати користувача, повертає true при успіху
    public function add(User $user): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO users (first_name, last_name, email, phone, password)
            VALUES (:first_name, :last_name, :email, :phone, :password)
        ");

        return $stmt->execute([
            ':first_name' => $user->getFirstName(),
            ':last_name'  => $user->getLastName(),
            ':email'      => $user->getEmail(),
            ':phone'      => $user->getPhone(),
            ':password'   => $user->getPassword(),
        ]);
    }

    // Повертає ID останнього вставленого користувача
    public function getLastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    // Перевірка чи існує користувач з таким email
    public function existsByEmail(string $email): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return (bool) $stmt->fetchColumn();
    }

    // Отримати всіх користувачів
    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM users");
        $users = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = $this->mapRowToUser($row);
        }

        return $users;
    }

    // Отримати користувача за ID
    public function getById(int $id): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapRowToUser($row) : null;
    }

    // Отримати користувача за email
    public function getByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapRowToUser($row) : null;
    }

    // Оновити користувача
    public function update(User $user): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE users
            SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, password = :password
            WHERE id = :id
        ");

        return $stmt->execute([
            ':first_name' => $user->getFirstName(),
            ':last_name'  => $user->getLastName(),
            ':email'      => $user->getEmail(),
            ':phone'      => $user->getPhone(),
            ':password'   => $user->getPassword(),
            ':id'         => $user->getId(),
        ]);
    }

    // Видалити користувача
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // Приватний метод для мапінгу рядка БД у об'єкт User
    private function mapRowToUser(array $row): User
    {
        return new User(
            $row['first_name'],
            $row['last_name'],
            $row['email'],
            $row['phone'],
            isset($row['id']) ? (int)$row['id'] : null,
            $row['password'] ?? null,
            $row['created_at'] ?? null
        );
    }
}
