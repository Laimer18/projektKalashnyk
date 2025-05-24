<?php
// Этот файл теперь является представлением (view), которое подключается из HomeController.
// Предполагается, что HomeController уже инициализировал сессию (через public/index.php -> SessionManager)
// и определил переменные:
// $galleryItems (массив данных для галереи)
// $contact_form_status_message (строка, сообщение о статусе формы)
// $contact_form_status_type ('success' или 'error')

// Убедимся, что переменные существуют, чтобы избежать ошибок, если view вызывается напрямую (не рекомендуется)
$galleryItems = $galleryItems ?? [];
$contact_form_status_message = $contact_form_status_message ?? '';
$contact_form_status_type = $contact_form_status_type ?? '';

// BASE_PATH должен быть определен в index.php (маршрутизаторе) и доступен здесь.
if (!defined('BASE_PATH')) {
    // Фоллбэк, если view вызывается не из HomeController или BASE_PATH не был установлен.
    // Для views/main_page_view.php, __DIR__ это .../views, dirname(__DIR__) это корень проекта.
    define('BASE_PATH', dirname(__DIR__));
}
// NavigationHelper должен быть доступен (автозагружен)
if (!class_exists('NavigationHelper')) {
    $navHelperPath = BASE_PATH . '/classes/NavigationHelper.php';
    if (file_exists($navHelperPath)) {
        require_once $navHelperPath;
    }
}
// $base_project_url_path должен быть доступен, если NavigationHelper его использует по умолчанию или если он передается.
// В NavigationHelper::getAssetUrl мы передаем его явно.
// Для простоты, если он не установлен, установим значение по умолчанию.
$base_project_url_path = $base_project_url_path ?? '/projekt1';

?>
<!DOCTYPE html>
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
    <title>Circle by templatemo</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800" rel="stylesheet">

    <link rel="stylesheet" href="<?= htmlspecialchars(NavigationHelper::getAssetUrl('css/bootstrap.min.css', $base_project_url_path)) ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(NavigationHelper::getAssetUrl('css/normalize.min.css', $base_project_url_path)) ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(NavigationHelper::getAssetUrl('css/font-awesome.min.css', $base_project_url_path)) ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(NavigationHelper::getAssetUrl('css/animate.css', $base_project_url_path)) ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(NavigationHelper::getAssetUrl('css/templatemo_misc.css', $base_project_url_path)) ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(NavigationHelper::getAssetUrl('css/templatemo_style.css', $base_project_url_path)) ?>">

    <script src="<?= htmlspecialchars(NavigationHelper::getAssetUrl('js/vendor/modernizr-2.6.2.min.js', $base_project_url_path)) ?>"></script>
</head>
<body>
<!--[if lt IE 7]>
<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
<![endif]-->

<div class="bg-overlay"></div>
<div class="container-fluid">
    <div class="row">

        <?php
        // header.php должен использовать NavigationHelper для своих URL и быть доступным
        $headerPath = BASE_PATH . '/tools/header.php';
        if (file_exists($headerPath)) {
            require $headerPath;
        } else {
            echo "<p>Error: Header file not found.</p>";
        }
        ?>
        <div class="col-md-8 col-sm-12">
            <div id="menu-container">
                <div id="menu-1" class="about content">
                    <div class="row">
                        <ul class="tabs">
                            <li class="col-md-4 col-sm-4">
                                <a href="#tab1" class="icon-item"><i class="fa fa-umbrella"></i></a>
                            </li>
                            <li class="col-md-4 col-sm-4">
                                <a href="#tab2" class="icon-item"><i class="fa fa-camera"></i></a>
                            </li>
                            <li class="col-md-4 col-sm-4">
                                <a href="#tab3" class="icon-item"><i class="fa fa-coffee"></i></a>
                            </li>
                        </ul>
                        <div class="col-md-12 col-sm-12">
                            <div class="toggle-content text-center" id="tab1">
                                <h3>Our History</h3>
                                <p>Circle is free responsive website template for you. Please tell your friends about <span class="blue">template</span><span class="green">mo</span> website. Feel free to download, modify and use this template for your websites. You can easily change icons by <a rel="nofollow" href="http://fontawesome.info/font-awesome-icon-world-map/">Font Awesome</a>. Example: <strong><i class="fa fa-camera"></i></strong>
                                    <br><br>
                                    Credit goes to <a rel="nofollow" href="http://unsplash.com">Unsplash</a> for photos used in this template. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, repellat, aspernatur nihil quasi commodi laboriosam cumque est minus minima sit dicta adipisci possimus magnam. Sit, repudiandae, ut, error, voluptates aspernatur inventore quo earum reiciendis dolorum amet perspiciatis adipisci itaque voluptatum iste laboriosam sapiente hic autem blanditiis doloribus nihil.</p>
                            </div>
                            <div class="toggle-content text-center" id="tab2">
                                <h3>What We Do</h3>
                                <p>Donec quis orci nisl. Integer euismod lacus nec risus sollicitudin molestie vel semper turpis. In varius imperdiet enim quis iaculis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Mauris ac mauris aliquam magna molestie posuere in id elit. Integer semper metus felis, fringilla congue elit commodo a. Donec eget rutrum libero.
                                    <br><br>Nunc dui elit, vulputate vitae nunc sed, accumsan condimentum nisl. Vestibulum a dui lectus. Vivamus in justo hendrerit est cursus semper sed id nibh. Donec ut dictum lorem, eu molestie nisi. Quisque vulputate quis leo lobortis fermentum. Ut sit amet consectetur dui, vitae porttitor lectus.</p>
                            </div>
                            <div class="toggle-content text-center" id="tab3">
                                <h3>Our Team</h3>
                                <p>Aliquam erat volutpat. Vivamus tempus, nisi varius imperdiet molestie, velit mi feugiat felis, sit amet fringilla mi massa sit amet arcu. Mauris dictum nisl id felis lacinia congue. Aliquam lectus nisi, sodales in lacinia quis, lobortis vel sem. Vestibulum elit nisi, placerat eget auctor ut, dictum at libero.
                                    <br><br>Proin enim odio, eleifend eget euismod vitae, pharetra sed lacus. Donec at sapien nunc. Mauris vehicula quis diam nec dignissim. Nulla consequat nibh mattis metus sodales, at eleifend tortor tempor. Sed auctor lacus felis. </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-sm-4">
                            <div class="member-item">
                                <div class="thumb">
                                    <img src="<?= htmlspecialchars(NavigationHelper::getAssetUrl('images/team/member-1.jpg', $base_project_url_path)) ?>" alt="Tanya - Web Designer">
                                </div>
                                <h4>Tanya</h4>
                                <span>Web Designer</span>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="member-item">
                                <div class="thumb">
                                    <img src="<?= htmlspecialchars(NavigationHelper::getAssetUrl('images/team/member-2.jpg', $base_project_url_path)) ?>" alt="Candy - Web Developer">
                                </div>
                                <h4>Candy</h4>
                                <span>Web Developer</span>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="member-item">
                                <div class="thumb">
                                    <img src="<?= htmlspecialchars(NavigationHelper::getAssetUrl('images/team/member-3.jpg', $base_project_url_path)) ?>" alt="Julia - Creative Director">
                                </div>
                                <h4>Julia</h4>
                                <span>Creative Director</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="menu-2" class="services content">
                    <div class="row">
                         <ul class="tabs">
                            <li class="col-md-4 col-sm-4"><a href="#tab4" class="icon-item"><i class="fa fa-cogs"></i></a></li>
                            <li class="col-md-4 col-sm-4"><a href="#tab5" class="icon-item"><i class="fa fa-leaf"></i></a></li>
                            <li class="col-md-4 col-sm-4"><a href="#tab6" class="icon-item"><i class="fa fa-users"></i></a></li>
                        </ul>
                        <div class="col-md-12 col-sm-12">
                            <div class="toggle-content text-center" id="tab4">
                                <h3>Our Services</h3>
                                <p>In varius eros ac est interdum, quis scelerisque elit semper. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.
                                    <br><br>Donec mattis enim sit amet nisl faucibus, eu pulvinar nibh facilisis. Aliquam erat volutpat. Vivamus tempus, nisi varius imperdiet molestie, velit mi feugiat felis, sit amet fringilla mi massa sit amet arcu. Mauris dictum nisl id felis lacinia congue. Aliquam lectus nisi, sodales in lacinia quis, lobortis vel sem. Vestibulum elit nisi, placerat eget auctor ut, dictum at libero.</p>
                            </div>
                            <div class="toggle-content text-center" id="tab5">
                                <h3>Our Support</h3>
                                <p>Nulla consequat nibh mattis metus sodales, at eleifend tortor tempor. Sed auctor lacus felis. Maecenas auctor enim libero, vel viverra nulla fringilla quis. Sed eget aliquet arcu. Suspendisse ac dignissim nunc, id pretium elit. Nunc id neque vel leo semper gravida non ut enim. Cras sed posuere magna.
                                    <br><br>Morbi eget ante sed felis tristique interdum. In varius eros ac est interdum, quis scelerisque elit semper. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p>
                            </div>
                            <div class="toggle-content text-center" id="tab6">
                                <h3>Testimonials</h3>
                                <p>Etiam dictum, quam quis pharetra tincidunt, enim nunc faucibus ipsum, vitae condimentum ligula est eu dui. Sed tincidunt tincidunt sapien non feugiat. Aenean lacinia tempor leo, et euismod ligula porta non. Quisque lectus ante, rutrum eu neque volutpat, euismod lobortis velit. Suspendisse felis risus, tempor ac vehicula eu, volutpat volutpat sem. Donec quis orci nisl. Integer euismod lacus nec risus sollicitudin molestie vel semper turpis.
                                    <br><br>In varius imperdiet enim quis iaculis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Mauris ac mauris aliquam magna molestie posuere in id elit. Integer semper metus felis, fringilla congue elit commodo a. Donec eget rutrum libero.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="menu-3" class="gallery content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="gallery-display" style="display: flex; flex-wrap: wrap; gap: 15px; justify-content: center; padding: 20px; margin-top: 20px;">
                                <?php if (!empty($galleryItems)): ?>
                                    <?php foreach ($galleryItems as $item): ?>
                                    <div class="image-item" style="width: 220px; text-align: center; border: 1px solid #ddd; border-radius: 8px; padding: 10px; background-color: #f9f9f9; box-shadow: 0 2px 4px rgba(0,0,0,0.05); transition: transform 0.2s ease-in-out;">
                                        <a href="<?= $item['url'] ?>" data-lightbox="gallery" data-title="<?= $item['title'] ?>">
                                            <img src="<?= $item['thumbnail_url'] ?>" alt="Gallery Image <?= $item['title'] ?>" style="max-width: 100%; height: 180px; object-fit: cover; border-radius: 4px; margin-bottom: 10px;"/>
                                        </a>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>No images in the gallery.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="menu-4" class="contact content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="toggle-content text-center spacing">
                                <h3>Contact Us</h3>
                                <p>We are always happy to hear from you! If you have any questions, suggestions, or wishes, write to us and we will definitely respond.
                                    <br><br><strong>Address:</strong> 2/3 drazovska Street, Nitra 94901, Slovakia
                                    <br><strong>Company:</strong> KalashnykKP
                                    <br><strong>Email:</strong> info@kalashnyk.com | <strong>Tel:</strong> +4210952082426</p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="contact-form">
                                <?php if ($contact_form_status_message): ?>
                                    <div id="contact-form-status" style="
                                        padding: 15px;
                                        margin-bottom: 20px;
                                        border: 1px solid transparent;
                                        border-radius: 4px;
                                        color: #fff;
                                        background-color: <?php echo $contact_form_status_type === 'success' ? '#2ecc71' : '#e74c3c'; ?>;
                                        text-align: center;
                                    ">
                                        <?php echo $contact_form_status_message; ?>
                                    </div>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const statusDiv = document.getElementById('contact-form-status');
                                            if (statusDiv) {
                                                if (window.location.hash === '#menu-4') {
                                                    statusDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                                }
                                                setTimeout(function() {
                                                    statusDiv.style.transition = 'opacity 0.5s ease';
                                                    statusDiv.style.opacity = '0';
                                                    setTimeout(function() {
                                                        statusDiv.style.display = 'none';
                                                    }, 500);
                                                }, 5000);
                                            }
                                        });
                                    </script>
                                <?php endif; ?>
                                <?php
                                $contact_submit_url = NavigationHelper::getAssetUrl('contact/submit', $base_project_url_path);
                                ?>
                                <form action="<?= htmlspecialchars($contact_submit_url); ?>" method="post" style="flex-direction: column; align-items: center; display: flex;">
                                    <div style="display:flex; gap:10px; width:100%;">
                                        <input id="first_name" type="text" name="first_name" placeholder="Name" style="flex:1; min-width:120px; max-width:220px;" required>
                                        <input type="text" name="last_name" id="last_name" placeholder="Last name" style="flex:1; min-width:120px; max-width:220px;" required>
                                        <input type="email" name="email" id="email" placeholder="Email" style="flex:1; min-width:180px; max-width:260px;" required>
                                        <input type="text" name="phone" id="phone" placeholder="Phone" style="flex:1; min-width:120px; max-width:180px;" required>
                                    </div>
                                    <textarea name="questions" id="questions" placeholder="Your questions or comments" style="width: 100%; max-width: 600px; min-height: 100px; margin-top: 20px; padding: 10px; border-radius: 5px; border: 1px solid #ccc;" required></textarea>
                                    <input type="submit" name="send" value="Send Message" id="submit" class="button" style="min-width:180px; border-radius: 10px; margin:20px auto 0 auto; display:block;">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// footer.php должен использовать NavigationHelper для своих URL и быть доступным
$footerPath = BASE_PATH . '/tools/footer.php';
if (file_exists($footerPath)) {
    require $footerPath;
} else {
    echo "<p>Error: Footer file not found.</p>";
}
?>

<script src="<?= htmlspecialchars(NavigationHelper::getAssetUrl('js/vendor/jquery-1.10.1.min.js', $base_project_url_path)) ?>"></script>
<script>window.jQuery || document.write('<script src="<?= htmlspecialchars(NavigationHelper::getAssetUrl('js/vendor/jquery-1.10.1.min.js', $base_project_url_path)) ?>"><\/script>')</script>
<script src="<?= htmlspecialchars(NavigationHelper::getAssetUrl('js/jquery.easing-1.3.js', $base_project_url_path)) ?>"></script>
<script src="<?= htmlspecialchars(NavigationHelper::getAssetUrl('js/bootstrap.js', $base_project_url_path)) ?>"></script>
<script src="<?= htmlspecialchars(NavigationHelper::getAssetUrl('js/plugins.js', $base_project_url_path)) ?>"></script>
<script src="<?= htmlspecialchars(NavigationHelper::getAssetUrl('js/main.js', $base_project_url_path)) ?>"></script>
<script type="text/javascript">
    jQuery(function ($) {
        $.supersized({
            slide_interval: 3000,
            transition: 1,
            transition_speed: 700,
            slide_links: 'blank',
            slides: [
                {image: '<?= htmlspecialchars(NavigationHelper::getAssetUrl('images/templatemo-slide-1.jpg', $base_project_url_path)) ?>'},
                {image: '<?= htmlspecialchars(NavigationHelper::getAssetUrl('images/templatemo-slide-2.jpg', $base_project_url_path)) ?>'},
                {image: '<?= htmlspecialchars(NavigationHelper::getAssetUrl('images/templatemo-slide-3.jpg', $base_project_url_path)) ?>'},
                {image: '<?= htmlspecialchars(NavigationHelper::getAssetUrl('images/templatemo-slide-4.jpg', $base_project_url_path)) ?>'}
            ]
        });
    });
</script>

<!-- Google Map -->
<script src="http://maps.google.com/maps/api/js?sensor=true"></script>
<script src="<?= htmlspecialchars(NavigationHelper::getAssetUrl('js/vendor/jquery.gmap3.min.js', $base_project_url_path)) ?>"></script>
<!-- Google Map Init-->
<script type="text/javascript">
    function templatemo_map() {
        $('.google-map').gmap3({
            marker:{
                address: '16.8496189,96.1288854' // Оставьте как есть или сделайте настраиваемым
            },
            map:{
                options:{
                    zoom: 15,
                    scrollwheel: false,
                    streetViewControl : true
                }
            }
        });
    }
</script>
</body>
</html>