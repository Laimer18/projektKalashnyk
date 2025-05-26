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

    /**
     * Приймає дані з форми, виконує валідацію і зберігає в базу
     *
     * @param array $postData
     * @return bool true — успішно, false — помилка
     */
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
            $this->successMessage = 'Ваше повідомлення успішно надіслано! Скоро ми з вами зв’яжемося.';
            return true;
        } else {
            $this->errorMessage = 'Сталася помилка під час відправки повідомлення. Спробуйте пізніше.';
            return false;
        }
    }

    /**
     * Валідація введених даних
     */
    private function validate(): bool
    {
        if (
            $this->firstName === '' ||
            $this->lastName === '' ||
            $this->email === '' ||
            $this->phone === '' ||
            $this->question === ''
        ) {
            $this->errorMessage = 'Всі поля є обов’язковими для заповнення.';
            return false;
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->errorMessage = 'Некоректний формат email.';
            return false;
        }

        // Додай додаткову валідацію телефону, якщо потрібно

        return true;
    }

    /**
     * Збереження повідомлення в базу
     */
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

    // За потреби можна додати геттери для полів (наприклад, щоб відновити введені дані у формі)
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
