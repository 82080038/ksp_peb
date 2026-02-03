<?php
// Test DB connections
require __DIR__ . "/config/db.php";

echo "Testing DB connections...\n";

try {
    $peopleDB = getPeopleDB();
    $stmt = executeQuery($peopleDB, "SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "People DB: OK (users: " . $result['count'] . ")\n";
} catch (Exception $e) {
    echo "People DB: FAILED - " . $e->getMessage() . "\n";
}

try {
    $coopDB = getCoopDB();
    $stmt = executeQuery($coopDB, "SELECT COUNT(*) as count FROM anggota");
    $result = $stmt->fetch();
    echo "Coop DB: OK (anggota: " . $result['count'] . ")\n";
} catch (Exception $e) {
    echo "Coop DB: FAILED - " . $e->getMessage() . "\n";
}

try {
    $addressDB = getAddressDB();
    $stmt = executeQuery($addressDB, "SELECT COUNT(*) as count FROM provinces");
    $result = $stmt->fetch();
    echo "Address DB: OK (provinces: " . $result['count'] . ")\n";
} catch (Exception $e) {
    echo "Address DB: FAILED - " . $e->getMessage() . "\n";
}

echo "DB test complete.\n";
?>
