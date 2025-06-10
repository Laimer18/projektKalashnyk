<?php
declare(strict_types=1);

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

require_once BASE_PATH . '/contact/db.php';
require_once BASE_PATH . '/user/user.php';

class UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function add(User $user): bool
    {
        return $this->execute(
            "INSERT INTO users (first_name, last_name, email, phone, password) VALUES (:first_name, :last_name, :email, :phone, :password)",
            $this->mapUserToParams($user)
        );
    }

    public function getLastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    public function existsByEmail(string $email): bool
    {
        return (bool) $this->query("SELECT COUNT(*) FROM users WHERE email = :email", [':email' => $email])->fetchColumn();
    }

    public function getAll(): array
    {
        return array_map([$this, 'mapRowToUser'], $this->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getById(int $id): ?User
    {
        return $this->fetchUser("SELECT * FROM users WHERE id = :id LIMIT 1", [':id' => $id]);
    }

    public function getByEmail(string $email): ?User
    {
        return $this->fetchUser("SELECT * FROM users WHERE email = :email LIMIT 1", [':email' => $email]);
    }

    public function update(User $user): bool
    {
        return $this->execute(
            "UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, password = :password WHERE id = :id",
            $this->mapUserToParams($user) + [':id' => $user->getId()]
        );
    }

    public function delete(int $id): bool
    {
        return $this->execute("DELETE FROM users WHERE id = :id", [':id' => $id]);
    }

    private function fetchUser(string $query, array $params): ?User
    {
        $row = $this->query($query, $params)->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRowToUser($row) : null;
    }

    private function execute(string $query, array $params): bool
    {
        return $this->pdo->prepare($query)->execute($params);
    }

    private function query(string $query, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    private function mapUserToParams(User $user): array
    {
        return [
            ':first_name' => $user->getFirstName(),
            ':last_name'  => $user->getLastName(),
            ':email'      => $user->getEmail(),
            ':phone'      => $user->getPhone(),
            ':password'   => $user->getPassword(),
        ];
    }

    private function mapRowToUser(array $row): User
    {
        return new User(
            $row['first_name'],
            $row['last_name'],
            $row['email'],
            $row['phone'],
            $row['id'] ?? null,
            $row['password'] ?? null,
            $row['created_at'] ?? null
        );
    }
}