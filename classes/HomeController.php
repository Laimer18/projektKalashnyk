<?php

class HomeController {

    public function getGalleryData(): array {
        $galleryData = [];
        $imageFilenames = [];

        for ($i = 1; $i <= 9; $i++) {
            $imageFilenames[] = "g{$i}.jpg";
        }

        $baseImagePath = NavigationHelper::getAssetUrl('images/gallery/');

        foreach ($imageFilenames as $filename) {
            $imageNumber = (int) filter_var($filename, FILTER_SANITIZE_NUMBER_INT);

            $galleryData[] = [
                'url' => htmlspecialchars($baseImagePath . $filename),
                'thumbnail_url' => htmlspecialchars($baseImagePath . $filename),
                'title' => "Image $imageNumber"
            ];
        }

        return $galleryData;
    }

    public function index(): void {
        $galleryItems = $this->getGalleryData();

        $contact_form_status_message = '';
        $contact_form_status_type = '';

        if (isset($_SESSION['contact_form_status'])) {
            $status = $_SESSION['contact_form_status'];

            if (is_array($status)) {
                $contact_form_status_message = htmlspecialchars($status['message'] ?? 'An unexpected error occurred.');
                $contact_form_status_type = !empty($status['success']) ? 'success' : 'error';
            } else {
                // Старий варіант, якщо просто 'success' чи 'error'
                $contact_form_status_type = $status === 'success' ? 'success' : 'error';
                $contact_form_status_message = $status === 'success'
                    ? 'Форма успішно надіслана.'
                    : 'Сталася помилка при надсиланні форми.';
            }

            unset($_SESSION['contact_form_status']);
        }

        $mainPageViewPath = BASE_PATH . '../views/main_page_view.php';

        if (file_exists($mainPageViewPath)) {
            require $mainPageViewPath;
        } else {
            http_response_code(500);
            echo "<h1>500 - Server Error</h1>";
            echo "<p>The main page template is missing. Please contact support.</p>";
            error_log("HomeController: Main page view not found at $mainPageViewPath");
        }
    }
}
