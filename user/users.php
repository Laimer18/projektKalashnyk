<?php
// user/users.php

// All session checks and data loading are handled by the controller.
require_once __DIR__ . '/../classes/UserListController.php';
require_once __DIR__ . '/../views/UserListView.php';
// Ensure User class is available if not already autoloaded or included by controller/repository
require_once __DIR__ . '/user.php';


$controller = new UserListController();
$controller->loadUsers(); // This also handles the session check and redirect.

$users = $controller->getUsers();

$view = new UserListView($users);
$view->render();
