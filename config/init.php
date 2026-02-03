<?php
// Error reporting
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Timezone
date_default_timezone_set("Asia/Jakarta");

// Load environment variables
if (file_exists(__DIR__ . "/.env")) {
    $lines = file(__DIR__ . "/.env", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), "#") === 0) {
            continue;
        }
        list($name, $value) = explode("=", $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf("%s=%s", $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}
