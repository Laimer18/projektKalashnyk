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

        $contactFormStatusMessage = '';
        $contactFormStatusType = '';

        if (isset($_SESSION['contact_form_status'])) {
            $status = $_SESSION['contact_form_status'];

            if (is_array($status)) {
                $contactFormStatusMessage = htmlspecialchars($status['message'] ?? 'An unexpected error occurred.');
                $contactFormStatusType = !empty($status['success']) ? 'success' : 'error';
            } else {
                $contactFormStatusType = $status === 'success' ? 'success' : 'error';
                $contactFormStatusMessage = $status === 'success'
                    ? 'Form submitted successfully.'
                    : 'An error occurred while submitting the form.';
            }

            unset($_SESSION['contact_form_status']);
        }
        $mainPageViewPath = __DIR__ . '/../views/main_page_view.php';

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
