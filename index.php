<?php
require_once __DIR__ . '/classes/Router.php';

$basePath = '/projekt1'; // це ВАЖЛИВО!
$router = new Router($basePath);
$router->route();
