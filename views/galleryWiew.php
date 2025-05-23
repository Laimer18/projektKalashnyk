<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Галерея</title>
    <link rel="stylesheet" href="/css/styles.css" />
    <style>
        .gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .image-item {
            width: 200px;
            text-align: center;
        }
        .image-item img {
            max-width: 100%;
            border-radius: 8px;
        }
    </style>
</head>
<body>
<h1>Галерея зображень</h1>

<div class="gallery">
    <?php foreach ($galleryImages as $img): ?>
        <div class="image-item">
            <img src="<?= htmlspecialchars($img->getUrl()) ?>" alt="Image" />
            <p>Тип: <?= htmlspecialchars($img->getType()) ?></p>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
