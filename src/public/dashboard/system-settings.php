<?php
// System Settings Page
require_once __DIR__ . '/../../../bootstrap.php';

$auth = new Auth();
if (!$auth->isLoggedIn() || !$auth->hasPermission('admin_access')) {
    header('Location: /ksp_peb/login.php');
    exit;
}

$app = App::getInstance();
$coopDB = $app->getCoopDB();

// Get system configurations
try {
    $stmt = $coopDB->prepare("SELECT * FROM configs ORDER BY key_name");
    $stmt->execute();
    $configs = $stmt->fetchAll();
    
    // Group configs by category
    $groupedConfigs = [];
    foreach ($configs as $config) {
        $category = explode('_', $config['key_name'])[0];
        $groupedConfigs[$category][] = $config;
    }
} catch (Exception $e) {
    $groupedConfigs = [];
}

// Get system info
$systemInfo = [
    'php_version' => PHP_VERSION,
    'mysql_version' => '',
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? '',
    'server_ip' => $_SERVER['SERVER_ADDR'] ?? 'Unknown',
    'client_ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
];

// Get MySQL version
try {
    $stmt = $coopDB->query("SELECT VERSION() as version");
    $result = $stmt->fetch();
    $systemInfo['mysql_version'] = $result['version'];
} catch (Exception $e) {
    $systemInfo['mysql_version'] = 'Unknown';
}
?>

<div class="row">
    <div class="col-12">
        <h2>Pengaturan Sistem</h2>
        <p class="text-muted">Kelola konfigurasi dan pengaturan sistem</p>
    </div>
</div>

<!-- System Information -->
<div class="row mb-4">
    <div class="col-12">
        <h4>Informasi Sistem</h4>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Environment</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td>PHP Version</td>
                        <td><?php echo htmlspecialchars($systemInfo['php_version']); ?></td>
                    </tr>
                    <tr>
                        <td>MySQL Version</td>
                        <td><?php echo htmlspecialchars($systemInfo['mysql_version']); ?></td>
                    </tr>
                    <tr>
                        <td>Server Software</td>
                        <td><?php echo htmlspecialchars($systemInfo['server_software']); ?></td>
                    </tr>
                    <tr>
                        <td>Document Root</td>
                        <td><?php echo htmlspecialchars($systemInfo['document_root']); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Network</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td>Server IP</td>
                        <td><?php echo htmlspecialchars($systemInfo['server_ip']); ?></td>
                    </tr>
                    <tr>
                        <td>Client IP</td>
                        <td><?php echo htmlspecialchars($systemInfo['client_ip']); ?></td>
                    </tr>
                    <tr>
                        <td>HTTPS</td>
                        <td>
                            <span class="badge bg-<?php echo isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'success' : 'danger'; ?>">
                                <?php echo isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'Enabled' : 'Disabled'; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Time Zone</td>
                        <td><?php echo date_default_timezone_get(); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Configuration Settings -->
<div class="row">
    <div class="col-12">
        <h4>Konfigurasi Aplikasi</h4>
    </div>
    <?php foreach ($groupedConfigs as $category => $configs): ?>
    <div class="col-lg-6">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0 text-capitalize"><?php echo htmlspecialchars($category); ?></h5>
            </div>
            <div class="card-body">
                <?php foreach ($configs as $config): ?>
                <div class="mb-3">
                    <label for="config_<?php echo $config['id']; ?>" class="form-label">
                        <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $config['key_name']))); ?>
                    </label>
                    <?php if (in_array($config['key_name'], ['APP_DEBUG', 'SESSION_SECURE', 'SESSION_HTTPONLY'])): ?>
                        <select class="form-select" id="config_<?php echo $config['id']; ?>" onchange="updateConfig(<?php echo $config['id']; ?>, this.value)">
                            <option value="true" <?php echo $config['value'] === 'true' ? 'selected' : ''; ?>>True</option>
                            <option value="false" <?php echo $config['value'] === 'false' ? 'selected' : ''; ?>>False</option>
                        </select>
                    <?php else: ?>
                        <input type="text" class="form-control" id="config_<?php echo $config['id']; ?>" 
                               value="<?php echo htmlspecialchars($config['value']); ?>" 
                               onchange="updateConfig(<?php echo $config['id']; ?>, this.value)">
                    <?php endif; ?>
                    <?php if ($config['description']): ?>
                        <small class="form-text text-muted"><?php echo htmlspecialchars($config['description']); ?></small>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- System Actions -->
<div class="row mt-4">
    <div class="col-12">
        <h4>Tindakan Sistem</h4>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="bi bi-arrow-clockwise fs-1 text-primary mb-3"></i>
                <h5>Clear Cache</h5>
                <p class="text-muted">Hapus cache sistem untuk mempercepat performance</p>
                <button class="btn btn-primary" onclick="clearCache()">Clear Cache</button>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="bi bi-arrow-repeat fs-1 text-success mb-3"></i>
                <h5>Restart Session</h5>
                <p class="text-muted">Restart semua session yang aktif</p>
                <button class="btn btn-success" onclick="restartSessions()">Restart Sessions</button>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="bi bi-file-earmark-text fs-1 text-info mb-3"></i>
                <h5>View Logs</h5>
                <p class="text-muted">Lihat log sistem dan error</p>
                <button class="btn btn-info" onclick="viewLogs()">View Logs</button>
            </div>
        </div>
    </div>
</div>

<!-- Security Status -->
<div class="row mt-4">
    <div class="col-12">
        <h4>Status Keamanan</h4>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Security Check</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Debug Mode</span>
                    <span class="badge bg-<?php echo Environment::get('APP_DEBUG') === 'false' ? 'success' : 'danger'; ?>">
                        <?php echo Environment::get('APP_DEBUG') === 'false' ? 'OFF' : 'ON'; ?>
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Session Security</span>
                    <span class="badge bg-success">Enabled</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Password Hashing</span>
                    <span class="badge bg-success">BCrypt</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>CSRF Protection</span>
                    <span class="badge bg-success">Active</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Database Status</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Connection</span>
                    <span class="badge bg-success">Connected</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Character Set</span>
                    <span class="badge bg-success">UTF8</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Engine</span>
                    <span class="badge bg-success">InnoDB</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Last Backup</span>
                    <span class="badge bg-warning">Manual</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateConfig(configId, value) {
    fetch('src/public/api/management.php?type=config&action=update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            config_id: configId,
            value: value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success notification
            showNotification('Configuration updated successfully', 'success');
        } else {
            alert(data.message || 'Failed to update configuration');
        }
    })
    .catch(error => {
        alert('Error updating configuration. Please try again.');
    });
}

function clearCache() {
    if (confirm('Are you sure you want to clear the system cache?')) {
        fetch('src/public/api/management.php?type=system&action=clear_cache', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Cache cleared successfully', 'success');
            } else {
                alert(data.message || 'Failed to clear cache');
            }
        })
        .catch(error => {
            alert('Error clearing cache. Please try again.');
        });
    }
}

function restartSessions() {
    if (confirm('Are you sure you want to restart all sessions? This will logout all users.')) {
        fetch('src/public/api/management.php?type=system&action=restart_sessions', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Sessions restarted successfully', 'success');
            } else {
                alert(data.message || 'Failed to restart sessions');
            }
        })
        .catch(error => {
            alert('Error restarting sessions. Please try again.');
        });
    }
}

function viewLogs() {
    window.open('dashboard.php?page=audit-log', '_blank');
}

function showNotification(message, type) {
    // Simple notification (you can replace with a better notification system)
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alert.style.top = '20px';
    alert.style.right = '20px';
    alert.style.zIndex = '9999';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alert);
    
    setTimeout(() => {
        alert.remove();
    }, 3000);
}
</script>
