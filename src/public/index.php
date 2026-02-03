<?php
// Front controller
require __DIR__ . "/../bootstrap.php";

// Routing sederhana
$request = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$basePath = "/koperasi/src/public";
$request = str_replace($basePath, "", $request);

switch ($request) {
    case "/":
    case "":
        require __DIR__ . "/../app/views/home.php";
        break;
    default:
        http_response_code(404);
        require __DIR__ . "/../app/views/404.php";
        break;
}
