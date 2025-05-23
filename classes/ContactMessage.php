<?php
class ContactMessage
{
    private string $firstName;
    private string $lastName;
    private string $email;
    private string $phone;
    private string $question;  // Однакове ім'я з БД

    public function __construct(string $firstName, string $lastName, string $email, string $phone, string $question)
    {
        $this->firstName = $firstName;
        $this->lastName  = $lastName;
        $this->email     = $email;
        $this->phone     = $phone;
        $this->question  = $question;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getQuestion(): string
    {
        return $this->question;
    }
}