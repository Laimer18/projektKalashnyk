<?php
require_once '../classes/SessionManager.php';

SessionManager::logout();
header("Location: register1.php");
exit;