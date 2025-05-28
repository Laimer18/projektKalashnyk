<?php
require_once __DIR__ . '/classes/Router.php';

$basePath = '/projekt1';
$router = new Router($basePath);
$router->route();
