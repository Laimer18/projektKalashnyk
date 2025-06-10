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
            "INSERT INTO users (first_name, last_name, email, phone, password) VALUES (:first_name, :last_name, :email, :phone, :password)",// підготовка запиту для додавання нового користувача
            $this->mapUserToParams($user) // підготовка параметрів для запиту
        );
    }

    public function getLastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    public function existsByEmail(string $email): bool
    {
        return (bool) $this->query("SELECT COUNT(*) FROM users WHERE email = :email", [':email' => $email])->fetchColumn(); // перевірка наявності користувача з таким email
    }

    public function getAll(): array
    {
        return array_map([$this, 'mapRowToUser'], $this->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC)); // отримання всіх користувачів з бази даних
    }

    public function getById(int $id): ?User
    {
        return $this->fetchUser("SELECT * FROM users WHERE id = :id LIMIT 1", [':id' => $id]); // отримання користувача за ID
    }

    public function getByEmail(string $email): ?User
    {
        return $this->fetchUser("SELECT * FROM users WHERE email = :email LIMIT 1", [':email' => $email]);
    }

    public function update(User $user): bool
    {
        return $this->execute(
            "UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, password = :password WHERE id = :id",//оновлення
            $this->mapUserToParams($user) + [':id' => $user->getId()] // додавання ID користувача до параметрів
        );
    }

    public function delete(int $id): bool
    {
        return $this->execute("DELETE FROM users WHERE id = :id", [':id' => $id]); // видалення користувача за ID
    }

    private function fetchUser(string $query, array $params): ?User // отримання користувача за запитом
    {
        $row = $this->query($query, $params)->fetch(PDO::FETCH_ASSOC); // виконання запиту та отримання результату як асоціативного масиву
        return $row ? $this->mapRowToUser($row) : null;
    }

    private function execute(string $query, array $params): bool // виконання запиту з параметрами
    {
        return $this->pdo->prepare($query)->execute($params); // підготовка запиту та виконання з параметрами
    }

    private function query(string $query, array $params = []): PDOStatement// виконання запиту з параметрами
    {
        $stmt = $this->pdo->prepare($query);// підготовка запиту
        $stmt->execute($params);
        return $stmt;
    }

    private function mapUserToParams(User $user): array // відображення властивостей користувача на параметри запиту
    {
        return [
            ':first_name' => $user->getFirstName(),
            ':last_name'  => $user->getLastName(),
            ':email'      => $user->getEmail(),
            ':phone'      => $user->getPhone(),
            ':password'   => $user->getPassword(),
        ];
    }

    private function mapRowToUser(array $row): User// відображення рядка з бази даних на об'єкт користувача
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