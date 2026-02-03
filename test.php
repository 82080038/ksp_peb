<?php
// Test page to verify application setup
require_once __DIR__ . '/src/bootstrap.php';

echo "<h1>Koperasi Application Test</h1>";

try {
    // Test database connections
    $app = App::getInstance();
    
    echo "<h2>Database Connections:</h2>";
    
    // Test People DB
    $peopleDB = $app->getPeopleDB();
    $stmt = $peopleDB->query("SELECT COUNT(*) as count FROM users");
    $userCount = $stmt->fetch()['count'];
    echo "<p>✅ People DB connected - Users: $userCount</p>";
    
    // Test Coop DB
    $coopDB = $app->getCoopDB();
    $stmt = $coopDB->query("SELECT COUNT(*) as count FROM roles");
    $roleCount = $stmt->fetch()['count'];
    echo "<p>✅ Coop DB connected - Roles: $roleCount</p>";
    
    // Test Address DB
    $addressDB = $app->getAddressDB();
    $stmt = $addressDB->query("SELECT COUNT(*) as count FROM provinces");
    $provinceCount = $stmt->fetch()['count'];
    echo "<p>✅ Address DB connected - Provinces: $provinceCount</p>";
    
    echo "<h2>RBAC System:</h2>";
    $rbac = new RBAC();
    $roles = $rbac->getRoles();
    echo "<p>✅ RBAC loaded - " . count($roles) . " roles available</p>";
    
    echo "<h2>Available Roles:</h2>";
    echo "<ul>";
    foreach ($roles as $role) {
        echo "<li>" . htmlspecialchars($role['name']) . " - " . htmlspecialchars($role['description'] ?? 'No description') . "</li>";
    }
    echo "</ul>";
    
    echo "<h2>Navigation:</h2>";
    echo "<p><a href='login.php'>🔐 Login Page</a></p>";
    echo "<p><a href='register.php'>📝 Register Page</a></p>";
    echo "<p><a href='dashboard.php'>📊 Dashboard (requires login)</a></p>";
    
    echo "<h2>API Endpoints:</h2>";
    echo "<p><a href='src/public/api/auth.php?action=check'>🔗 Auth API Check</a></p>";
    
    echo "<h2>Next Steps:</h2>";
    echo "<ol>";
    echo "<li>Create a test user via registration page</li>";
    echo "<li>Login with test credentials</li>";
    echo "<li>Access dashboard and features</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error:</h2>";
    echo "<p style='color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
}
