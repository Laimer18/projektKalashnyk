<?php

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

if (!class_exists('NavigationHelper')) {
    $navHelperPath = BASE_PATH . '/classes/NavigationHelper.php';
    if (file_exists($navHelperPath)) {
        require_once $navHelperPath;
    }
}

$baseProjectPathForUrls = '/projekt1';

$account_url = class_exists('NavigationHelper') ? NavigationHelper::getAccountUrl($baseProjectPathForUrls) : $baseProjectPathForUrls . '/user/register1.php'; // Фоллбэк
$logo_image_url = class_exists('NavigationHelper') ? NavigationHelper::getAssetUrl('images/logo.png', $baseProjectPathForUrls) : $baseProjectPathForUrls . '/images/logo.png';

$home_url = $baseProjectPathForUrls . '/';
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
