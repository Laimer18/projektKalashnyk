<?php
require_once __DIR__ . '/../contact/db.php'; // This must contain class Database with getInstance()

class ImageRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get a list of images by type
     * @param string $type
     * @return array
     */
    public function getImagesByType(string $type): array
    {
        $stmt = $this->pdo->prepare("SELECT id, image_url, type FROM images WHERE type = ?");
        $stmt->execute([$type]);

        $images = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $images[] = [
                'id' => $row['id'],
                'image_url' => $row['image_url'],
                'type' => $row['type'],
            ];
        }
        return $images;
    }
}
