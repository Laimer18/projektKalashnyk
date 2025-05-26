<?php

class PhotoSession
{
    private ?int $id;
    private string $name;
    private string $email;
    private string $phone;
    private string $date;
    private string $details;
    private ?string $created_at;

    public function __construct(
        string $name,
        string $email,
        string $phone,
        string $date,
        string $details = '',
        ?int $id = null,
        ?string $created_at = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->date = $date;
        $this->details = $details;
        $this->created_at = $created_at;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getDetails(): string
    {
        return $this->details;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }
}
