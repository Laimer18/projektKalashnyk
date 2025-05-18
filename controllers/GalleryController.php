<?php
require_once __DIR__ . '/classes/imageManager.php';
require_once __DIR__ . '/core.php';

class GalleryController {
    private ImageRepository $imageRepository;

    public function __construct(ImageRepository $imageRepository) {
        $this->imageRepository = $imageRepository;
    }

    public function renderGallery(): void {
        $galleryImages = $this->imageRepository->getImagesByType('gallery');
        require_once __DIR__ . '/views/galleryView.php'; // Передаємо дані у View
    }
}
?>