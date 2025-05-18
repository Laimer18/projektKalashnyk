<?php
require_once 'db.php';

class Image {
    private $url;
    private $type;

    public function __construct($url, $type) {
        $this->url = $url;
        $this->type = $type;
    }

    public function getUrl() {
        return $this->url;
    }

    public function getType() {
        return $this->type;
    }
}

$stmt = $pdo->query("SELECT url, type FROM gallery_images");
$galleryImages = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $galleryImages[] = new Image($row['url'], $row['type']);
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="/css/styles.css" />
    <title>Gallery</title>
</head>
<body>
<div class="gallery">
    <?php foreach ($galleryImages as $img): ?>
        <div class="image-item">
            <img src="<?= htmlspecialchars($img->getUrl()) ?>" alt="Image" />
            <p>Type: <?= htmlspecialchars($img->getType()) ?></p>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
