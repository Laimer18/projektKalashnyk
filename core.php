<?php
// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection and class definitions
require_once __DIR__ . '/contact/db.php';                  // Contains Database class
require_once __DIR__ . '/classes/Image.php';               // Contains Image class
require_once __DIR__ . '/classes/ImageRepository.php';     // Contains ImageRepository class
