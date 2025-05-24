<?php
require_once __DIR__ . '/../classes/AuthRedirectController.php';

$authRedirectController = new AuthRedirectController();
$authRedirectController->handleRedirect();
