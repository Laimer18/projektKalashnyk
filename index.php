<?php
require_once 'classes/Router.php';


$basePath = ''; // або '/myproject', якщо сайт розміщений не в корені
$router = new Router($basePath);
$router->route();
