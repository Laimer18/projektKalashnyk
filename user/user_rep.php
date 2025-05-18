<?php

require_once '../contact/db.php'; // Шлях до файлу з PDO-підключенням
require_once 'user.php'; // Клас User з геттерами

class UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Додати користувача
    public function add(User $user): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO users (first_name, last_name, email, phone, password)
            VALUES (?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $user->getFirstName(),
            $user->getLastName(),
            $user->getEmail(),
            $user->getPhone(),
            $user->getPassword()
        ]);
    }

    // Отримати всіх користувачів
    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM users");
        $users = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User(
                $row['first_name'],
                $row['last_name'],
                $row['email'],
                $row['phone'],
                (int)$row['id'],
                $row['password'] ?? null
            );
        }

        return $users;
    }

    // Отримати користувача по ID
    public function getById(int $id): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new User(
                $row['first_name'],
                $row['last_name'],
                $row['email'],
                $row['phone'],
                (int)$row['id'],
                $row['password'] ?? null
            );
        }

        return null;
    }

    // Отримати користувача по email (для логіну)
    public function getByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new User(
                $row['first_name'],
                $row['last_name'],
                $row['email'],
                $row['phone'],
                (int)$row['id'],
                $row['password'] ?? null
            );
        }

        return null;
    }

    // Оновити користувача
    public function update(User $user): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE users 
            SET first_name = ?, last_name = ?, email = ?, phone = ?, password = ? 
            WHERE id = ?
        ");

        return $stmt->execute([
            $user->getFirstName(),
            $user->getLastName(),
            $user->getEmail(),
            $user->getPhone(),
            $user->getPassword(),
            $user->getId()
        ]);
    }

    // Видалити користувача
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
