<?php
require_once __DIR__ . '/../classes/UserController.php.php';
require_once __DIR__ . '/../views/UserListView.php';

require_once __DIR__ . '/user.php';


$controller = new UserListController();
$controller->loadUsers();

$users = $controller->getUsers();

$view = new UserListView($users);
$view->render();
