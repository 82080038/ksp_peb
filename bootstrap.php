<?php
// Bootstrap application
require_once __DIR__ . "/Environment.php";
require_once __DIR__ . "/ErrorHandler.php";
require_once __DIR__ . "/Security.php";
require_once __DIR__ . "/InputValidator.php";

// Load environment variables
Environment::load();

// Initialize application
require_once __DIR__ . "/App.php";

// Autoload classes
spl_autoload_register(function ($class) {
    $file = __DIR__ . "/../app/" . str_replace("\\", "/", $class) . ".php";
    if (file_exists($file)) {
        require $file;
    }
});

// Also check in src directory
spl_autoload_register(function ($class) {
    $file = __DIR__ . "/" . str_replace("\\", "/", $class) . ".php";
    if (file_exists($file)) {
        require $file;
    }
});

// Error reporting based on environment
if (Environment::get('APP_DEBUG') === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Start application
$app = App::getInstance();
$app->startSession();
