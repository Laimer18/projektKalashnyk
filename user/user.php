<?php

class User {
    private ?int $id; // може бути як число так і NULL
    private string $firstName; // string це рядок, якщо ? то це необовязкове
    private string $lastName;
    private string $email;
    private string $phone;
    private ?string $password;
    private ?string $createdAt;
    public function getName(): string {
        return $this->firstName . ' ' . $this->lastName;
    }
    public function __construct(
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        ?int $id = null,
        ?string $password = null,
        ?string $createdAt = null
    ) {
        $this->setFirstName($firstName); // це сетери і завжди перевірка
        $this->setLastName($lastName);
        $this->setEmail($email);
        $this->setPhone($phone);
        $this->id = $id;
        $this->password = $password;
        $this->createdAt = $createdAt;
    }

    // --- ГЕТТЕРИ ---  отримування приватної інформації
    public function getId(): ?int {
        return $this->id;
    }

    public function getFirstName(): string {
        return $this->firstName;
    }

    public function getLastName(): string {
        return $this->lastName;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getPhone(): string {
        return $this->phone;
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function getCreatedAt(): ?string {
        return $this->createdAt;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setFirstName(string $firstName): void {
        $firstName = trim($firstName); // видаляє пробіли
        if ($firstName === '') { // перевірка чи таке виходе
            throw new InvalidArgumentException("First name is required."); //випише що хиба
        }
        $this->firstName = $firstName;
    }

    public function setLastName(string $lastName): void {
        $lastName = trim($lastName);
        if ($lastName === '') {
            throw new InvalidArgumentException("Last name is required.");
        }
        $this->lastName = $lastName;
    }

    public function setEmail(string $email): void {
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // перевірка що це емеіл
            throw new InvalidArgumentException("Invalid email format.");
        }
        $this->email = $email;
    }

    public function setPhone(string $phone): void { // якщо воід то нічого не повертаємо
        $this->phone = trim($phone);
    }

    public function setPassword(string $password): void {
        $this->password = $password;
    }

    public function setCreatedAt(string $createdAt): void {
        $this->createdAt = $createdAt;
    }
}
