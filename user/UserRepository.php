<?php

if (!defined('BASE_PATH')) {

    define('BASE_PATH', dirname(__DIR__));
}

require_once BASE_PATH . '/contact/db.php'; // Используем BASE_PATH
require_once BASE_PATH . '/user/user.php';   // Используем BASE_PATH, предполагая, что user.php в user/

class UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo; // зберігаємо  приватно
    }

    // Додати користувача, повертає true при успіху  CRUD
    public function add(User $user): bool // бул або тру або фолс
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

    public function getLastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    public function existsByEmail(string $email): bool // перевірка існування за емейл
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email"); //  перевірка існування  повертає  (0 або 1).
        $stmt->execute([':email' => $email]); // отримує кількість
        return (bool) $stmt->fetchColumn();
    }

    public function getAll(): array // отримуємо всіх корист (array саме масив)
    {
        $stmt = $this->pdo->query("SELECT * FROM users");
        $users = []; // порожній масив

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { // вже повертає готовий масив а не сирі дані
            $users[] = $this->mapRowToUser($row);
        }

        return $users;
    }

    public function getById(int $id): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC); // повертає один рядок як асоціативний масив

        return $row ? $this->mapRowToUser($row) : null; //  перетворює масив у об'єкт User
    }

    public function getByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapRowToUser($row) : null;
    }

    public function update(User $user): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE users
            SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, password = :password // це  не змінюємо
            WHERE id = :id
        ");

        return $stmt->execute([ // виконує підготовлений запит з переданими значеннями. або тру або фолс
            ':first_name' => $user->getFirstName(),
            ':last_name'  => $user->getLastName(),
            ':email'      => $user->getEmail(),
            ':phone'      => $user->getPhone(),
            ':password'   => $user->getPassword(),
            ':id'         => $user->getId(),
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

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
