<?php

require_once __DIR__ . '/ContactMessage.php';
require_once __DIR__ . '/ContactMessageRepository.php';
require_once __DIR__ . '/../contact/db.php'; // Для Database::getInstance()

class ContactController {

    private $pdo;
    private $contactMessageRepository;

    public function __construct() {
        $this->pdo = Database::getInstance(); // Обратите внимание, что db.php должен быть доступен
        $this->contactMessageRepository = new ContactMessageRepository($this->pdo);
    }

    /**
     * Обрабатывает отправку формы обратной связи.
     *
     * @param array $postData Данные из $_POST.
     * @return array Массив с ключами 'success' (bool) и 'message' (string).
     */
    public function submitForm(array $postData): array {
        $firstName = trim($postData["first_name"] ?? '');
        $lastName  = trim($postData["last_name"] ?? '');
        $email     = trim($postData["email"] ?? '');
        $phone     = trim($postData["phone"] ?? '');
        $question  = trim($postData["questions"] ?? '');

        if (!$firstName || !$lastName || !$email || !$phone || !$question) {
            // В реальном приложении здесь лучше выбрасывать исключение или возвращать более структурированный ответ об ошибке
            return ['success' => false, 'message' => 'All fields are required. Please fill in all fields and try again.'];
        }

        // Здесь можно добавить более сложную валидацию (например, формат email, телефона)

        $message = new ContactMessage($firstName, $lastName, $email, $phone, $question);

        if ($this->contactMessageRepository->add($message)) {
            return ['success' => true, 'message' => 'Your message has been sent successfully! We will get back to you shortly.'];
        } else {
            return ['success' => false, 'message' => 'Error: Could not send your message due to a server error. Please try again later.'];
        }
    }
}