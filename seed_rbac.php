<?php
// Initialize RBAC system
require_once __DIR__ . '/src/bootstrap.php';

$rbac = new RBAC();
$result = $rbac->initializeDefaults();

echo "RBAC Initialization Result:\n";
print_r($result);

if ($result['success']) {
    echo "\nRBAC system has been initialized successfully!\n";
    echo "\nDefault roles created:\n";
    echo "- super_admin: Full administrative access\n";
    echo "- admin: Administrative access for daily operations\n";
    echo "- pengawas: Supervisor access (read/approve only)\n";
    echo "- anggota: Regular member access\n";
    echo "- calon_anggota: Prospective member (limited access)\n";
} else {
    echo "\nError: " . $result['message'] . "\n";
}
