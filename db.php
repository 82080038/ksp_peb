<?php
// Database connection functions using PDO
require_once __DIR__ . '/../src/Environment.php';

function getPeopleDB() {
    $host = Environment::get('DB_HOST', 'localhost');
    $user = Environment::get('DB_USER', 'root');
    $pass = Environment::get('DB_PASS', 'root');
    $db = 'people_db';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Connection failed to People DB: " . $e->getMessage());
        throw new Exception("Database connection failed");
    }
}

function getCoopDB() {
    $host = Environment::get('DB_HOST', 'localhost');
    $user = Environment::get('DB_USER', 'root');
    $pass = Environment::get('DB_PASS', 'root');
    $db = Environment::get('DB_NAME', 'coop_db');

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Connection failed to Coop DB: " . $e->getMessage());
        throw new Exception("Database connection failed");
    }
}

function getAddressDB() {
    $host = Environment::get('DB_HOST', 'localhost');
    $user = Environment::get('DB_USER', 'root');
    $pass = Environment::get('DB_PASS', 'root');
    $db = 'alamat_db';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Connection failed to Address DB: " . $e->getMessage());
        throw new Exception("Database connection failed");
    }
}

// Helper function to execute prepared statements
function executeQuery($pdo, $sql, $params = []) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}
?>
