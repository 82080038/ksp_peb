<?php
// Seed admin user and member for quick start
require __DIR__ . "/config/db.php";

$peopleDB = getPeopleDB();
$coopDB = getCoopDB();

try {
    // Check if admin user exists
    $stmt = executeQuery($peopleDB, "SELECT id FROM users WHERE email = 'admin@koperasi.com'");
    $user = $stmt->fetch();

    if (!$user) {
        // Create admin user
        $hashed = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = executeQuery($peopleDB, 
            "INSERT INTO users (nama, email, phone, password_hash, status) VALUES (?, ?, ?, ?, 'active')",
            ['Admin Koperasi', 'admin@koperasi.com', '08123456789', $hashed]
        );
        $userId = $peopleDB->lastInsertId();

        // Assign super_admin role
        $stmt = executeQuery($peopleDB, "SELECT id FROM roles WHERE name = 'super_admin'");
        $role = $stmt->fetch();
        if ($role) {
            executeQuery($peopleDB, "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)", [$userId, $role['id']]);
        }
    } else {
        $userId = $user['id'];
    }

    // Check if member exists
    $stmt = executeQuery($coopDB, "SELECT id FROM anggota WHERE user_id = ?", [$userId]);
    if (!$stmt->fetch()) {
        // Create member record
        executeQuery($coopDB, 
            "INSERT INTO anggota (user_id, status_keanggotaan, nomor_anggota) VALUES (?, 'active', ?)",
            [$userId, 'ADM-0001']
        );
    }

    echo "Admin and member seeded successfully! Email: admin@koperasi.com, Password: admin123\n";
} catch (Exception $e) {
    echo "Seeding failed: " . $e->getMessage() . "\n";
}
?>
