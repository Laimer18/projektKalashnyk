<?php
require_once __DIR__ . '/Image.php';
require_once __DIR__ . '/../core.php';

class ImageRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function getImagesByType(string $type): array {
        $stmt = $this->pdo->prepare("SELECT id, image_url, type FROM images WHERE type = ?");
        $stmt->execute([$type]);
        $images = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $images[] = new Image($row['id'], $row['image_url'], $row['type']);
        }
        return $images;
    }
}
