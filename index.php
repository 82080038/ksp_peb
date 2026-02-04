<?php
// Gateway Index - Clean Redirects
session_start();

// Check if user is already logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Check if user has required role
function hasRequiredRole() {
    if (!isLoggedIn()) return false;
    
    // For now, any logged in user can proceed
    return true;
}

// Determine where to redirect user
function getRedirectTarget() {
    // If user is logged in and has required role
    if (isLoggedIn() && hasRequiredRole()) {
        return '/ksp_peb/dashboard.php';
    }
    
    // If not logged in, redirect to login
    return '/ksp_peb/login.php';
}

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Cache control headers
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Check for maintenance mode
$maintenanceFile = __DIR__ . '/.maintenance';
if (file_exists($maintenanceFile)) {
    http_response_code(503);
    include __DIR__ . '/maintenance.php';
    exit;
}

// Perform the redirect
$target = getRedirectTarget();

// Clean redirect without parameters
header("Location: $target");
exit;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koperasi Simpan Pinjam - Gateway</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .gateway-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            text-align: center;
        }
        .logo {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .title {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        .status {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        .status.logged-in {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status.not-logged-in {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .btn-redirect {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.3s ease;
        }
        .btn-redirect:hover {
            transform: translateY(-2px);
            color: white;
        }
        .footer-text {
            margin-top: 2rem;
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="gateway-container">
        <div class="logo">🏦</div>
        <h1 class="title">Koperasi Simpan Pinjam</h1>
        
        <?php if (isLoggedIn()): ?>
            <div class="status logged-in">
                <strong>✅ Status: Terautentikasi</strong><br>
                User ID: <?php echo htmlspecialchars($_SESSION['user_id']); ?>
            </div>
            <p>Anda akan dialihkan ke dashboard dalam beberapa saat...</p>
            <a href="dashboard.php" class="btn-redirect">Menuju Dashboard →</a>
        <?php else: ?>
            <div class="status not-logged-in">
                <strong>🔒 Status: Belum Login</strong><br>
                Silakan login terlebih dahulu
            </div>
            <p>Anda akan dialihkan ke halaman login dalam beberapa saat...</p>
            <a href="login.php" class="btn-redirect">Menuju Login →</a>
        <?php endif; ?>
        
        <div class="footer-text">
            <small>Jika tidak teralihkan otomatis, klik tombol di atas</small><br>
            <small>Koperasi Simpan Pinjam © 2026</small>
        </div>
    </div>

    <script>
        // Auto redirect after 3 seconds
        setTimeout(function() {
            <?php if (isLoggedIn()): ?>
                window.location.href = 'dashboard.php';
            <?php else: ?>
                window.location.href = 'login.php';
            <?php endif; ?>
        }, 3000);

        // Prevent back button after logout
        window.onload = function() {
            if (window.history.forward) {
                window.history.forward();
            }
        };
    </script>
</body>
</html>
