<?php

// Предполагается, что NavigationHelper будет загружен автозагрузчиком.
// BASE_PATH должен быть определен в точке входа (public/index.php).
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

class HomeController {

    /**
     * Готовит данные для галереи изображений.
     *
     * @return array Массив данных для галереи, содержащий пути к изображениям и их заголовки.
     */
    public function getGalleryData(): array {
        $galleryData = [];
        $imageFilenames = [];
        for ($i = 1; $i <= 9; $i++) {
            $imageFilenames[] = "g{$i}.jpg"; // Предполагаются имена g1.jpg, g2.jpg, ..., g9.jpg
        }
        
        // Базовый путь к изображениям галереи относительно корня проекта.
        // Используем NavigationHelper для формирования корректного URL.
        $baseImagePath = NavigationHelper::getAssetUrl('images/gallery/'); // Удаляем /projekt1, так как getAssetUrl его добавит

        foreach ($imageFilenames as $filename) {
            $galleryData[] = [
                'url' => htmlspecialchars($baseImagePath . $filename),
                'thumbnail_url' => htmlspecialchars($baseImagePath . $filename), // Для простоты используем тот же URL
                'title' => "Image " . substr($filename, 1, strpos($filename, '.') - 1) // Извлекаем номер из имени файла
            ];
        }
        return $galleryData;
    }

    /**
     * Отображает главную страницу.
     *
     * Этот метод будет загружать основное представление (бывший index.php)
     * и передавать в него необходимые данные, такие как данные галереи.
     */
    public function index(): void {
        // Получаем данные для галереи
        $galleryItems = $this->getGalleryData();

        // Получаем статус формы контактов из сессии, если он есть
        $contact_form_status_message = '';
        $contact_form_status_type = ''; // 'success' или 'error'

        if (isset($_SESSION['contact_form_status'])) {
            $status = $_SESSION['contact_form_status'];
            $contact_form_status_message = htmlspecialchars($status['message'] ?? 'An unexpected error occurred.');
            $contact_form_status_type = $status['success'] ? 'success' : 'error';
            unset($_SESSION['contact_form_status']); // Удаляем сообщение из сессии
        }
        
        // Загружаем главный файл представления.
        // Теперь он должен находиться в views/main_page_view.php
        // Переменные $galleryItems, $contact_form_status_message, $contact_form_status_type
        // будут доступны в области видимости подключаемого файла.
        
        // Важно: header.php и footer.php также должны быть доступны и корректно работать.
        // Их логика (например, определение $account_url в header.php) уже была частично рефакторена.

        $mainPageViewPath = BASE_PATH . '/views/main_page_view.php'; // Обновленный путь к представлению
        
        if (file_exists($mainPageViewPath)) {
            // Передаем переменные в область видимости представления
            // Это упрощенный способ. В более сложных системах используются шаблонизаторы.
            require $mainPageViewPath;
        } else {
            // Обработка ошибки, если файл представления не найден
            http_response_code(500);
            echo "<h1>500 - Server Error</h1>";
            echo "<p>The main page template is missing. Please contact support.</p>";
            error_log("HomeController: Main page view file not found at " . $mainPageViewPath);
        }
    }
}