<?php
class User {
    private ?int $id;
    private string $first_name;
    private string $last_name;
    private string $email;
    private string $phone;
    private ?string $password;
    private ?string $created_at;

    public function __construct(
        string $first_name,
        string $last_name,
        string $email,
        string $phone,
        ?int $id = null,
        ?string $password = null,
        ?string $created_at = null
    ) {
        if (empty($first_name) || empty($last_name) || empty($email)) {
            throw new InvalidArgumentException("Name, last name, and email are required.");
        }
        $this->id = $id;
        $this->first_name = $first_name;
        $this->last_name  = $last_name;
        $this->email      = $email;
        $this->phone      = $phone;
        $this->password   = $password;
        $this->created_at = $created_at;
    }

    // Геттери
    public function getId(): ?int { return $this->id; }
    public function getFirstName(): string { return $this->first_name; }
    public function getLastName(): string { return $this->last_name; }
    public function getEmail(): string { return $this->email; }
    public function getPhone(): string { return $this->phone; }
    public function getPassword(): ?string { return $this->password; }
    public function getCreatedAt(): ?string { return $this->created_at; }

    // Сеттери
    public function setFirstName(string $first_name): void { $this->first_name = $first_name; }
    public function setLastName(string $last_name): void { $this->last_name = $last_name; }
    public function setEmail(string $email): void {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format.");
        }
        $this->email = $email;
    }
    public function setPhone(string $phone): void { $this->phone = $phone; }
    public function setPassword(string $password): void { $this->password = $password; }
    public function setId(int $id): void { $this->id = $id; }
}
?>
