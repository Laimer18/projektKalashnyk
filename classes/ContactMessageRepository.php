<?php

class ContactMessage {
    private ?int $id;
    private string $name;
    private string $email;
    private string $subject;
    private string $message;
    private ?string $created_at;

    public function __construct(
        string $name,
        string $email,
        string $subject,
        string $message,
        ?string $created_at = null,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
        $this->created_at = $created_at;
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function getSubject(): string { return $this->subject; }
    public function getMessage(): string { return $this->message; }
    public function getCreatedAt(): ?string { return $this->created_at; }
}