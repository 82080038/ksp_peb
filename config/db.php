<?php
// Database connection functions using PDO

function getPeopleDB() {
    $host = 'localhost';
    $user = 'root';
    $pass = 'root';
    $db = 'people_db';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed to People DB: " . $e->getMessage());
    }
}

function getCoopDB() {
    $host = 'localhost';
    $user = 'root';
    $pass = 'root';
    $db = 'coop_db';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed to Coop DB: " . $e->getMessage());
    }
}

function getAddressDB() {
    $host = 'localhost';
    $user = 'root';
    $pass = 'root';
    $db = 'alamat_db';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed to Address DB: " . $e->getMessage());
    }
}

// Helper function to execute prepared statements
function executeQuery($pdo, $sql, $params = []) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}
?>
