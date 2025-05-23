<?php
require_once 'classes/ImageRepository.php';

$pdo = Database::getInstance();
$imageRepo = new ImageRepository($pdo);

$galleryImages = $imageRepo->getAll();

include 'views/gallery_view.php';
