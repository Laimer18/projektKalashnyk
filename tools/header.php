<?php
// session_start() теперь должен вызываться в public/index.php или через SessionManager.
// Убедитесь, что BASE_PATH определен в точке входа (public/index.php)
if (!defined('BASE_PATH')) {
    // Попытка определить BASE_PATH, если он не установлен (например, при прямом доступе к файлу, что не рекомендуется)
    // Это очень упрощенное определение и может потребовать корректировки в зависимости от структуры.
    // В идеале, этот файл должен всегда подключаться из public/index.php, где BASE_PATH уже определен.
    define('BASE_PATH', dirname(__DIR__));
}

// Подключаем NavigationHelper, если он еще не загружен автозагрузчиком
// Автозагрузчик в public/index.php должен справиться с этим, если класс назван корректно.
// Но для надежности, если этот файл может вызываться отдельно (не рекомендуется):
if (!class_exists('NavigationHelper')) {
    $navHelperPath = BASE_PATH . '/classes/NavigationHelper.php';
    if (file_exists($navHelperPath)) {
        require_once $navHelperPath;
    }
}

// Определяем базовый URL проекта. Это значение должно быть консистентным по всему проекту.
// В public/index.php мы использовали $base_project_path. Здесь можно использовать константу или передавать как параметр.
// Для простоты пока оставим так, но в будущем это лучше унифицировать.
$baseProjectPathForUrls = '/projekt1'; // Это должно совпадать с $base_project_path в public/index.php

$account_url = class_exists('NavigationHelper') ? NavigationHelper::getAccountUrl($baseProjectPathForUrls) : $baseProjectPathForUrls . '/user/register1.php'; // Фоллбэк
$logo_image_url = class_exists('NavigationHelper') ? NavigationHelper::getAssetUrl('images/logo.png', $baseProjectPathForUrls) : $baseProjectPathForUrls . '/images/logo.png';
// Для других ссылок, если они должны быть абсолютными от корня сайта + $baseProjectPathForUrls
$home_url = $baseProjectPathForUrls . '/'; // Или $baseProjectPathForUrls, если главная не в корне
$facebook_url = "https://www.facebook.com/profile.php?id=61572448198509"; // Внешняя ссылка остается как есть

?>

<div class="col-md-4 col-sm-12">
    <div class="sidebar-menu">
        <div class="logo-wrapper">
            <h1 class="logo">
                <a href="<?php echo htmlspecialchars($home_url); ?>"><img src="<?php echo htmlspecialchars($logo_image_url); ?>" alt="Circle Template">
                    <span>Responsive Mobile Template</span></a>
            </h1>
        </div> <!-- /.logo-wrapper -->
        <div class="menu-wrapper">
            <ul class="menu">
                <li><a class="homebutton" href="<?php echo htmlspecialchars($home_url); ?>">Home</a></li>
                <li><a class="show-1" href="#menu-1">About</a></li>
                <li><a class="show-2" href="#menu-2">Services</a></li>
                <li><a class="show-3" href="#menu-3">Gallery</a></li>
                <li><a class="show-4" href="#menu-4" onclick="templatemo_map();">Contact</a></li>
                <li><a rel="nofollow" href="<?php echo htmlspecialchars($facebook_url); ?>" target="_parent">External Link</a></li>
            </ul> <!-- /.menu -->
            <a href="#" class="toggle-menu"><i class="fa fa-bars"></i></a>
        </div> <!-- /.menu-wrapper -->

        <!-- ACCOUNT BUTTON -->
        <a href="<?php echo htmlspecialchars($account_url); ?>" class="account-btn">ACCOUNT</a>

        <!--Arrow Navigation-->
        <a id="prevslide" class="load-item"><i class="fa fa-angle-left"></i></a>
        <a id="nextslide" class="load-item"><i class="fa fa-angle-right"></i></a>

    </div>
</div>
