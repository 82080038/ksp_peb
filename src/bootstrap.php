<?php
// Load konfigurasi
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../config/db.php";

// Load App class explicitly
require_once __DIR__ . "/App.php";

// Autoload classes
spl_autoload_register(function ($class) {
    $file = __DIR__ . "/../app/" . str_replace("\\", "/", $class) . ".php";
    if (file_exists($file)) {
        require $file;
    }
});
