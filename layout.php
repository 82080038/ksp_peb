<?php
// Shared layout for admin pages
require_once __DIR__ . '/../../bootstrap.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header('Location: /ksp_peb/login.php');
    exit;
}

$user = $auth->getCurrentUser();
$app = App::getInstance();
$coopName = $app->getConfig('coop_name', 'Koperasi Simpan Pinjam');

// cooperative_id guard (use admin-created cooperative if not set)
if (empty($_SESSION['cooperative_id'])) {
    try {
        $coopStmt = $app->getCoopDB()->prepare("SELECT id FROM cooperatives WHERE created_by = ? ORDER BY id DESC LIMIT 1");
        $coopStmt->execute([$_SESSION['coop_user_id'] ?? $_SESSION['user_id']]);
        $coopRecord = $coopStmt->fetch();
        if ($coopRecord) {
            $_SESSION['cooperative_id'] = $coopRecord['id'];
        }
    } catch (Exception $e) {
        // ignore
    }
}
$cooperativeId = $_SESSION['cooperative_id'] ?? null;

$pageTitle = $pageTitle ?? 'Dashboard';
$content = $content ?? '';

// helper: build nav link with active state
function navLink($href, $icon, $label, $activeRoute) {
    $active = ($activeRoute === $href) ? 'active' : '';
    return "<li class=\"nav-item\"><a class=\"nav-link $active\" href=\"$href\"><i class=\"$icon me-2\"></i>$label</a></li>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle) . ' - ' . htmlspecialchars($coopName); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/ksp_peb/src/public/css/dashboard.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4><?php echo htmlspecialchars($coopName); ?></h4>
                        <p class="text-muted small"><?php echo htmlspecialchars($pageTitle); ?></p>
                    </div>

                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $activeRoute === '/ksp_peb/dashboard.php' ? 'active' : ''; ?>" href="/ksp_peb/dashboard.php">
                                <i class="bi bi-house-door me-2"></i>Dashboard
                            </a>
                        </li>
                        <?php if ($auth->hasPermission('view_members')): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $activeRoute === '/ksp_peb/dashboard/anggota.php' ? 'active' : ''; ?>" href="/ksp_peb/dashboard/anggota.php">
                                    <i class="bi bi-people me-2"></i>Anggota
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ($auth->hasPermission('view_savings')): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $activeRoute === '/ksp_peb/dashboard/simpanan.php' ? 'active' : ''; ?>" href="/ksp_peb/dashboard/simpanan.php">
                                    <i class="bi bi-piggy-bank me-2"></i>Simpanan
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ($auth->hasPermission('view_loans')): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $activeRoute === '/ksp_peb/dashboard/pinjaman.php' ? 'active' : ''; ?>" href="/ksp_peb/dashboard/pinjaman.php">
                                    <i class="bi bi-cash-stack me-2"></i>Pinjaman
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ($auth->hasPermission('view_accounts')): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $activeRoute === '/ksp_peb/dashboard/akuntansi.php' ? 'active' : ''; ?>" href="/ksp_peb/dashboard/akuntansi.php">
                                    <i class="bi bi-journal-text me-2"></i>Akuntansi
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ($auth->hasPermission('view_reports')): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $activeRoute === '/ksp_peb/dashboard/laporan.php' ? 'active' : ''; ?>" href="/ksp_peb/dashboard/laporan.php">
                                    <i class="bi bi-graph-up me-2"></i>Laporan
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ($auth->hasPermission('admin_access')): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $activeRoute === '/ksp_peb/dashboard/rat-management.php' ? 'active' : ''; ?>" href="/ksp_peb/dashboard/rat-management.php">
                                    <i class="bi bi-diagram-3 me-2"></i>RAT Management
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $activeRoute === '/ksp_peb/dashboard/cooperative-settings.php' ? 'active' : ''; ?>" href="/ksp_peb/dashboard/cooperative-settings.php">
                                    <i class="bi bi-gear me-2"></i>Cooperative Settings
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item mt-3">
                            <a class="nav-link" href="#" onclick="logout()">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <?php echo $content; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        async function logout() {
            if (confirm('Apakah Anda yakin ingin logout?')) {
                try { 
                    await fetch('/ksp_peb/src/public/api/auth.php?action=logout', { method: 'POST' }); 
                } catch (e) {}
                window.location.href = '/ksp_peb/login.php';
            }
        }
    </script>
</body>
</html>
