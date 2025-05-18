<?php
// Якщо треба запустити PHP код — тут
?>

<!DOCTYPE html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <title>Circle by templatemo</title>
    <meta name="viewport" content="width=device-width">
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/normalize.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/templatemo_misc.css">
    <link rel="stylesheet" href="css/templatemo_style.css">
    <script src="js/vendor/modernizr-2.6.2.min.js"></script>
</head>
<body>
<div class="bg-overlay"></div>
<div class="container-fluid">
    <div class="row">
        <?php require_once 'views/header.php'; ?>
        <div class="col-md-8 col-sm-12">
            <div id="menu-container">

                <section id="home" class="content">
                    <h1 class="animated fadeInDown">Welcome to Circle</h1>
                    <p class="animated fadeInUp">Your best photo experience starts here!</p>
                    <a href="?action=register" class="main-btn">Register</a>
                    <a href="?action=login" class="main-btn">Login</a>
                </section>
            </div>
        </div>
    </div>
</div>
<?php require_once 'views/footer.php'; ?>
<script src="js/vendor/jquery-1.10.1.min.js"></script>
<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.1.min.js"><\/script>')</script>
<script src="js/jquery.easing-1.3.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/plugins.js"></script>
<script src="js/main.js"></script>
<script type="text/javascript">
    jQuery(function ($) {
        $.supersized({
            slide_interval: 3000,
            transition: 1,
            transition_speed: 700,
            slide_links: 'blank',
            slides: [
                {image: 'images/templatemo-slide-1.jpg'},
                {image: 'images/templatemo-slide-2.jpg'},
                {image: 'images/templatemo-slide-3.jpg'}
            ]
        });
    });
</script>
</body>
</html>
