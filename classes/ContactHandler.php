<?php
class ContactHandler
{
    private PDO $pdo;

    // Властивості для збереження даних з форми
    private string $firstName = '';
    private string $lastName = '';
    private string $email = '';
    private string $phone = '';
    private string $question = '';

    // Повідомлення про помилку або успіх
    private string $errorMessage = '';
    private string $successMessage = '';

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo; // Збереження підключення до бази даних
    }

    // Обробка форми: збереження даних POST, валідація і запис у БД
    public function handleForm(array $postData): bool
    {
        // Отримання і очистка вхідних даних
        $this->firstName = trim($postData['first_name'] ?? '');
        $this->lastName  = trim($postData['last_name'] ?? '');
        $this->email     = trim($postData['email'] ?? '');
        $this->phone     = trim($postData['phone'] ?? '');
        $this->question  = trim($postData['questions'] ?? '');

        // Перевірка правильності введених даних
        if (!$this->validate()) {
            return false;
        }

        // Збереження даних у БД
        if ($this->save()) {
            $this->successMessage = 'Your message has been sent successfully! We will contact you soon.';
            return true;
        } else {
            $this->errorMessage = 'An error occurred while sending the message. Please try again later.';
            return false;
        }
    }

    // Валідація форми: перевірка заповнення та формату email
    private function validate(): bool
    {
        if (
            $this->firstName === '' ||
            $this->lastName === '' ||
            $this->email === '' ||
            $this->phone === '' ||
            $this->question === ''
        ) {
            $this->errorMessage = 'All fields are required.'; // Обов’язкові поля
            return false;
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->errorMessage = 'Invalid email format.'; // Неправильний формат email
            return false;
        }

        return true;
    }

    // Збереження даних у таблицю contacts
    private function save(): bool
    {
        $sql = "INSERT INTO contacts (first_name, last_name, email, phone, question, created_at)
                VALUES (:first_name, :last_name, :email, :phone, :question, NOW())";

        $stmt = $this->pdo->prepare($sql);

        // Повертає true при успішному виконанні SQL-запиту
        return $stmt->execute([
            ':first_name' => $this->firstName,
            ':last_name'  => $this->lastName,
            ':email'      => $this->email,
            ':phone'      => $this->phone,
            ':question'   => $this->question,
        ]);
    }

    // Отримання повідомлень для відображення у в’юшці
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getSuccessMessage(): string
    {
        return $this->successMessage;
    }

    // Гетери для попередньо введених значень (для повторного відображення у формі)
    public function getFirstName(): string { return $this->firstName; }
    public function getLastName(): string { return $this->lastName; }
    public function getEmail(): string { return $this->email; }
    public function getPhone(): string { return $this->phone; }
    public function getQuestion(): string { return $this->question; }
}
