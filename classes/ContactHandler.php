<?php

class ContactHandler
{
    private PDO $pdo;

    private string $firstName = '';
    private string $lastName = '';
    private string $email = '';
    private string $phone = '';
    private string $question = '';

    private string $errorMessage = '';
    private string $successMessage = '';

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    public function handleForm(array $postData): bool
    {
        $this->firstName = trim($postData['first_name'] ?? '');
        $this->lastName  = trim($postData['last_name'] ?? '');
        $this->email     = trim($postData['email'] ?? '');
        $this->phone     = trim($postData['phone'] ?? '');
        $this->question  = trim($postData['questions'] ?? '');

        if (!$this->validate()) {
            return false;
        }

        if ($this->save()) {
            $this->successMessage = 'Your message has been sent successfully! We will contact you soon.';
            return true;
        } else {
            $this->errorMessage = 'An error occurred while sending the message. Please try again later.';
            return false;
        }
    }


    private function validate(): bool
    {
        if (
            $this->firstName === '' ||
            $this->lastName === '' ||
            $this->email === '' ||
            $this->phone === '' ||
            $this->question === ''
        ) {
            $this->errorMessage = 'All fields are required.';
            return false;
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->errorMessage = 'Invalid email format.';
            return false;
        }

        // Add additional phone validation if needed

        return true;
    }


    private function save(): bool
    {
        $sql = "INSERT INTO contacts (first_name, last_name, email, phone, question, created_at)
                VALUES (:first_name, :last_name, :email, :phone, :question, NOW())";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':first_name' => $this->firstName,
            ':last_name'  => $this->lastName,
            ':email'      => $this->email,
            ':phone'      => $this->phone,
            ':question'   => $this->question,
        ]);
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getSuccessMessage(): string
    {
        return $this->successMessage;
    }

    // Getters can be added if needed (e.g. to repopulate form fields)
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
