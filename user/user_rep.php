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

    // CREATE
    public function add(User $user): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (first_name, last_name, email, phone, password) VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $user->first_name,
            $user->last_name,
            $user->email,
            $user->phone,
            $user->password
        ]);
    }

    // READ (all)
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
                $row['id'],
                $row['password'] ?? ''
            );
        }
        return $users;
    }

    // READ (by id)
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
                $row['id'],
                $row['password'] ?? ''
            );
        }
        return null;
    }

    // UPDATE
    public function update(User $user): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE users SET first_name=?, last_name=?, email=?, phone=?, password=? WHERE id=?"
        );
        return $stmt->execute([
            $user->first_name,
            $user->last_name,
            $user->email,
            $user->phone,
            $user->password,
            $user->id
        ]);
    }

    // DELETE
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id=?");
        return $stmt->execute([$id]);
    }
}
?>