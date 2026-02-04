<?php
// Enhanced Home Page with Full jQuery + Bootstrap Integration
require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../../../lib/IndonesianHelper.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header('Location: /ksp_peb/login.php');
    exit;
}

$user = $auth->getCurrentUser();
$app = App::getInstance();
$coopDB = $app->getCoopDB();

// Get dashboard statistics
try {
    // Total Anggota
    $stmt = $coopDB->prepare("SELECT COUNT(*) as total FROM anggota WHERE status = 'active'");
    $stmt->execute();
    $totalAnggota = $stmt->fetch()['total'];
    
    // Total Simpanan
    $stmt = $coopDB->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM simpanan_transactions WHERE transaction_type = 'deposit'");
    $stmt->execute();
    $totalSimpanan = $stmt->fetch()['total'];
    
    // Total Pinjaman
    $stmt = $coopDB->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM pinjaman WHERE status = 'active'");
    $stmt->execute();
    $totalPinjaman = $stmt->fetch()['total'];
    
    // SHU Tahun Ini
    $stmt = $coopDB->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM shu_distributions WHERE YEAR(created_at) = YEAR(CURRENT_DATE)");
    $stmt->execute();
    $shuTahunIni = $stmt->fetch()['total'];
    
} catch (Exception $e) {
    $totalAnggota = 0;
    $totalSimpanan = 0;
    $totalPinjaman = 0;
    $shuTahunIni = 0;
}

// Get recent activities
try {
    $stmt = $coopDB->prepare("
        SELECT al.action, al.details, al.created_at, u.nama as user_name 
        FROM audit_logs al 
        LEFT JOIN users u ON al.user_id = u.id 
        ORDER BY al.created_at DESC 
        LIMIT 10
    ");
    $stmt->execute();
    $recentActivities = $stmt->fetchAll();
} catch (Exception $e) {
    $recentActivities = [];
}

// Generate unique IDs
$uniqueId = uniqid('home_');
?>

<!-- Welcome Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="user-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-1">Selamat datang, <?php echo htmlspecialchars($user['nama']); ?>! 👋</h5>
                    <p class="text-muted mb-2">Dashboard overview sistem koperasi Anda</p>
                    <div>
                        <?php foreach ($user['roles'] as $role): ?>
                            <span class="user-role"><?php echo htmlspecialchars($role['name']); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="refreshDashboardData()">
                            <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="exportDashboard()">
                            <i class="bi bi-download me-1"></i> Export
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4" id="statsContainer">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Total Anggota</div>
                        <div class="stat-value" id="totalAnggota_<?php echo $uniqueId; ?>"><?php echo number_format($totalAnggota, 0, ',', '.'); ?></div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Total Simpanan</div>
                        <div class="stat-value" id="totalSimpanan_<?php echo $uniqueId; ?>">Rp <?php echo number_format($totalSimpanan, 0, ',', '.'); ?></div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-piggy-bank"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Total Pinjaman</div>
                        <div class="stat-value" id="totalPinjaman_<?php echo $uniqueId; ?>">Rp <?php echo number_format($totalPinjaman, 0, ',', '.'); ?></div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">SHU Tahun Ini</div>
                        <div class="stat-value" id="shuTahunIni_<?php echo $uniqueId; ?>">Rp <?php echo number_format($shuTahunIni, 0, ',', '.'); ?></div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-graph-up"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <h6 class="mb-3">Quick Actions</h6>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-enhanced btn-primary" onclick="loadPage('anggota')">
                <i class="bi bi-person-plus me-2"></i> Tambah Anggota
            </button>
            <button class="btn btn-enhanced btn-success" onclick="loadPage('simpanan')">
                <i class="bi bi-plus-circle me-2"></i> Simpanan Baru
            </button>
            <button class="btn btn-enhanced btn-warning" onclick="loadPage('pinjaman')">
                <i class="bi bi-cash me-2"></i> Pinjaman Baru
            </button>
            <button class="btn btn-enhanced btn-info" onclick="loadPage('laporan')">
                <i class="bi bi-file-text me-2"></i> Lihat Laporan
            </button>
        </div>
    </div>
</div>

<!-- System Status -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">System Status</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Database Connection</span>
                    <span class="badge bg-success" id="dbStatus_<?php echo $uniqueId; ?>">Online</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>API Services</span>
                    <span class="badge bg-success" id="apiStatus_<?php echo $uniqueId; ?>">Running</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Session Management</span>
                    <span class="badge bg-success" id="sessionStatus_<?php echo $uniqueId; ?>">Active</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Last Updated</span>
                    <span class="badge bg-info" id="lastUpdate_<?php echo $uniqueId; ?>"><?php echo date('H:i:s'); ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Recent Activity</h6>
            </div>
            <div class="card-body">
                <div id="recentActivities_<?php echo $uniqueId; ?>">
                    <?php if (!empty($recentActivities)): ?>
                        <?php foreach (array_slice($recentActivities, 0, 3) as $activity): ?>
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div>
                                <strong><?php echo htmlspecialchars($activity['action']); ?></strong>
                                <br><small class="text-muted"><?php echo htmlspecialchars(substr($activity['details'], 0, 50)); ?>...</small>
                            </div>
                            <small class="text-muted"><?php echo date('H:i', strtotime($activity['created_at'])); ?></small>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center">No recent activities</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities Table -->
<div class="table-enhanced">
    <div class="card-header">
        <h6 class="mb-0">Recent Activities</h6>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0" id="activitiesTable_<?php echo $uniqueId; ?>" data-enhanced>
            <thead>
                <tr>
                    <th>Activity</th>
                    <th>User</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recentActivities)): ?>
                    <?php foreach ($recentActivities as $activity): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($activity['action']); ?></strong>
                            <?php if ($activity['details']): ?>
                            <br><small class="text-muted"><?php echo htmlspecialchars(substr($activity['details'], 0, 100)); ?>...</small>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($activity['user_name'] ?? 'System'); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($activity['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted">No activities found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize page-specific functionality
    initializeHomePage();
});

function initializeHomePage() {
    // Auto-refresh every 30 seconds
    setInterval(function() {
        refreshDashboardData();
    }, 30000);
    
    // Check system status periodically
    setInterval(function() {
        checkSystemStatus();
    }, 60000);
    
    // Initialize tooltips
    KSP.initializeTooltips();
    
    // Initialize popovers
    KSP.initializePopovers();
}

function refreshDashboardData() {
    var uniqueId = '<?php echo $uniqueId; ?>';
    
    $.ajax({
        url: 'src/public/api/dashboard.php?action=stats',
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            // Show loading state
            $('#totalAnggota_' + uniqueId).html('<div class="loading-spinner"></div>');
            $('#totalSimpanan_' + uniqueId).html('<div class="loading-spinner"></div>');
            $('#totalPinjaman_' + uniqueId).html('<div class="loading-spinner"></div>');
            $('#shuTahunIni_' + uniqueId).html('<div class="loading-spinner"></div>');
        },
        success: function(data) {
            if (data.success) {
                updateDashboardStats(data.stats, uniqueId);
                updateLastUpdateTime(uniqueId);
                KSP.showNotification('Data dashboard diperbarui', 'success', {autoDismiss: 2000});
            }
        },
        error: function(xhr, status, error) {
            console.error('Kesalahan memperbarui dashboard:', error);
            KSP.showNotification('Kesalahan memperbarui dashboard', 'danger');
        }
    });
}

function updateDashboardStats(stats, uniqueId) {
    $('#totalAnggota_' + uniqueId).text(stats.totalAnggota || 0);
    $('#totalSimpanan_' + uniqueId).text('Rp ' + (stats.totalSimpanan || '0'));
    $('#totalPinjaman_' + uniqueId).text('Rp ' + (stats.totalPinjaman || '0'));
    $('#shuTahunIni_' + uniqueId).text('Rp ' + (stats.shuTahunIni || '0'));
}

function updateLastUpdateTime(uniqueId) {
    $('#lastUpdate_' + uniqueId).text(new Date().toLocaleTimeString());
}

function checkSystemStatus() {
    var uniqueId = '<?php echo $uniqueId; ?>';
    
    // Simulate system status check
    $.ajax({
        url: 'src/public/api/dashboard.php?action=user_info',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                $('#dbStatus_' + uniqueId).removeClass('bg-danger').addClass('bg-success').text('Online');
                $('#apiStatus_' + uniqueId).removeClass('bg-danger').addClass('bg-success').text('Running');
                $('#sessionStatus_' + uniqueId).removeClass('bg-danger').addClass('bg-success').text('Active');
            }
        },
        error: function() {
            $('#dbStatus_' + uniqueId).removeClass('bg-success').addClass('bg-danger').text('Offline');
            $('#apiStatus_' + uniqueId).removeClass('bg-success').addClass('bg-danger').text('Error');
            $('#sessionStatus_' + uniqueId).removeClass('bg-success').addClass('bg-warning').text('Check');
        }
    });
}

function exportDashboard() {
    // Create export data
    var exportData = {
        timestamp: new Date().toISOString(),
        stats: {
            totalAnggota: $('#totalAnggota_<?php echo $uniqueId; ?>').text(),
            totalSimpanan: $('#totalSimpanan_<?php echo $uniqueId; ?>').text(),
            totalPinjaman: $('#totalPinjaman_<?php echo $uniqueId; ?>').text(),
            shuTahunIni: $('#shuTahunIni_<?php echo $uniqueId; ?>').text()
        },
        activities: []
    };
    
    // Get activities from table
    $('#activitiesTable_<?php echo $uniqueId; ?> tbody tr').each(function() {
        var activity = {
            action: $(this).find('td:eq(0) strong').text(),
            user: $(this).find('td:eq(1)').text(),
            time: $(this).find('td:eq(2)').text()
        };
        exportData.activities.push(activity);
    });
    
    // Convert to JSON and download
    var dataStr = JSON.stringify(exportData, null, 2);
    var dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
    
    var exportFileDefaultName = 'dashboard_export_' + new Date().toISOString().slice(0,10) + '.json';
    
    var linkElement = document.createElement('a');
    linkElement.setAttribute('href', dataUri);
    linkElement.setAttribute('download', exportFileDefaultName);
    linkElement.click();
    
    KSP.showNotification('Dashboard berhasil diekspor', 'success');
}

function loadPage(page) {
    // Use parent's loadPage function
    if (typeof window.loadPage === 'function') {
        window.loadPage(page);
    } else {
        window.location.href = '/ksp_peb/dashboard.php?page=' + page;
    }
}
</script>
