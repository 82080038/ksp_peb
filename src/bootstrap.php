<?php
// Load konfigurasi
require_once __DIR__ . "/../config/init.php";

// Autoload classes
spl_autoload_register(function ($class) {
    $file = __DIR__ . "/../app/" . str_replace("\\\", "/", $class) . ".php";
    if (file_exists($file)) {
        require $file;
    }
});
