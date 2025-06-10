<?php

class HomeController {


    public function getGalleryData(): array {
        $galleryData = [];
        $imageFilenames = [];

        // Генеруємо імена файлів зображень (g1.jpg ... g9.jpg)
        for ($i = 1; $i <= 9; $i++) {
            $imageFilenames[] = "g{$i}.jpg";
        }

        // Отримуємо базовий шлях до зображень через хелпер
        $baseImagePath = NavigationHelper::getAssetUrl('images/gallery/');

        foreach ($imageFilenames as $filename) {
            // Отримуємо номер зображення з імені файлу
            $imageNumber = (int) filter_var($filename, FILTER_SANITIZE_NUMBER_INT);

            // Додаємо інформацію про зображення до масиву
            $galleryData[] = [
                'url' => htmlspecialchars($baseImagePath . $filename),            // Повна URL-адреса
                'thumbnail_url' => htmlspecialchars($baseImagePath . $filename), // Мініатюра (та сама картинка)
                'title' => "Image $imageNumber"                                  // Заголовок зображення
            ];
        }

        return $galleryData;
    }


    public function index(): void {
        $galleryItems = $this->getGalleryData();

        $contactFormStatusMessage = '';
        $contactFormStatusType = '';

        // Якщо є повідомлення про стан контактної форми у сесії — обробляємо його
        if (isset($_SESSION['contact_form_status'])) {
            $status = $_SESSION['contact_form_status'];

            if (is_array($status)) {
                // Якщо збережений статус — масив, використовуємо його вміст
                $contactFormStatusMessage = htmlspecialchars($status['message'] ?? 'An unexpected error occurred.');
                $contactFormStatusType = !empty($status['success']) ? 'success' : 'error';
            } else {
                // Якщо це просто success/error рядок
                $contactFormStatusType = $status === 'success' ? 'success' : 'error';
                $contactFormStatusMessage = $status === 'success'
                    ? 'Form submitted successfully.'
                    : 'An error occurred while submitting the form.';
            }

            // Видаляємо статус, щоб не зберігався між оновленнями сторінки
            unset($_SESSION['contact_form_status']);
        }

        // Підключаємо шаблон головної сторінки
        $mainPageViewPath = __DIR__ . '/../views/main_page_view.php';

        if (file_exists($mainPageViewPath)) {
            require $mainPageViewPath;
        } else {
            // Якщо шаблон не знайдено — помилка 500
            http_response_code(500);
            echo "<h1>500 - Server Error</h1>";
            echo "<p>The main page template is missing. Please contact support.</p>";
            error_log("HomeController: Main page view not found at $mainPageViewPath");
        }
    }
}