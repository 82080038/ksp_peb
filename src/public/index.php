<?php
// Front controller
require __DIR__ . "/../bootstrap.php";

// Start session for auth
App::getInstance()->startSession();

// Routing sederhana
$request = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$basePath = "/ksp_peb/src/public";
$request = str_replace($basePath, "", $request);

$auth = new Auth();

switch ($request) {
    case "/":
    case "":
        // Redirect to main dashboard
        header("Location: /ksp_peb/dashboard.php");
        exit;
        break;
    case "/login":
        require __DIR__ . "/../../login.php";
        break;
    case "/register":
        require __DIR__ . "/../../register.php";
        break;
    case "/dashboard":
        if (!$auth->isLoggedIn()) {
            header("Location: /ksp_peb/login.php");
            exit;
        }
        require __DIR__ . "/../../dashboard.php";
        break;
    case "/logout":
        $auth->logout();
        header("Location: /ksp_peb/login.php");
        exit;
        break;
    case "/api/auth":
        require __DIR__ . "/api/auth.php";
        break;
    case "/api/anggota":
        require __DIR__ . "/api/anggota.php";
        break;
    case "/api/management":
        require __DIR__ . "/api/management.php";
        break;
    case "/api/akuntansi":
        require __DIR__ . "/api/akuntansi.php";
        break;
    default:
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        echo "<p>The requested page could not be found.</p>";
        echo "<p><a href='/ksp_peb/'>Return to Home</a></p>";
        break;
}
