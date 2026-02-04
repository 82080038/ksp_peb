<?php
// Admin Tools Page
require_once __DIR__ . '/../../../bootstrap.php';

$auth = new Auth();
if (!$auth->isLoggedIn() || !$auth->hasPermission('admin_access')) {
    header('Location: /ksp_peb/login.php');
    exit;
}

$app = App::getInstance();
$coopDB = $app->getCoopDB();
?>

<div class="row">
    <div class="col-12">
        <h2>Admin Tools</h2>
        <p class="text-muted">Alat administratif dan maintenance sistem</p>
    </div>
</div>

<!-- System Tools -->
<div class="row mb-4">
    <div class="col-12">
        <h4>System Tools</h4>
    </div>
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-database-add fs-1 text-primary mb-3"></i>
                <h5>Database Migration</h5>
                <p class="text-muted">Run database migrations</p>
                <button class="btn btn-primary" onclick="runMigration()">Run Migration</button>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-arrow-repeat fs-1 text-success mb-3"></i>
                <h5>Clear Cache</h5>
                <p class="text-muted">Clear system cache</p>
                <button class="btn btn-success" onclick="clearCache()">Clear Cache</button>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-shield-check fs-1 text-warning mb-3"></i>
                <h5>Security Audit</h5>
                <p class="text-muted">Run security audit</p>
                <button class="btn btn-warning" onclick="runSecurityAudit()">Run Audit</button>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-graph-up fs-1 text-info mb-3"></i>
                <h5>Performance Check</h5>
                <p class="text-muted">Check system performance</p>
                <button class="btn btn-info" onclick="checkPerformance()">Check Now</button>
            </div>
        </div>
    </div>
</div>

<!-- Data Management -->
<div class="row mb-4">
    <div class="col-12">
        <h4>Data Management</h4>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5>Database Backup</h5>
                <p class="text-muted">Create database backup</p>
                <button class="btn btn-primary w-100" onclick="createBackup()">Create Backup</button>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5>Import Data</h5>
                <p class="text-muted">Import data from file</p>
                <input type="file" class="form-control mb-2" id="importFile">
                <button class="btn btn-success w-100" onclick="importData()">Import</button>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5>Export Data</h5>
                <p class="text-muted">Export all data</p>
                <button class="btn btn-info w-100" onclick="exportData()">Export All</button>
            </div>
        </div>
    </div>
</div>

<!-- System Status -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">System Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td>PHP Version</td>
                        <td><?php echo PHP_VERSION; ?></td>
                    </tr>
                    <tr>
                        <td>Memory Limit</td>
                        <td><?php echo ini_get('memory_limit'); ?></td>
                    </tr>
                    <tr>
                        <td>Max Execution Time</td>
                        <td><?php echo ini_get('max_execution_time'); ?>s</td>
                    </tr>
                    <tr>
                        <td>Upload Max Filesize</td>
                        <td><?php echo ini_get('upload_max_filesize'); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Logs</h5>
            </div>
            <div class="card-body">
                <div id="recentLogs">
                    <p class="text-muted">Loading...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function runMigration() {
    if (confirm('Are you sure you want to run database migration?')) {
        fetch('src/public/api/admin.php?action=migration', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Migration completed successfully');
            } else {
                alert(data.message || 'Migration failed');
            }
        })
        .catch(error => {
            alert('Error running migration');
        });
    }
}

function clearCache() {
    if (confirm('Are you sure you want to clear system cache?')) {
        fetch('src/public/api/admin.php?action=clear_cache', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cache cleared successfully');
            } else {
                alert(data.message || 'Failed to clear cache');
            }
        })
        .catch(error => {
            alert('Error clearing cache');
        });
    }
}

function runSecurityAudit() {
    fetch('src/public/api/admin.php?action=security_audit', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Security Score: ${data.score}%\nIssues: ${data.issues.length}`);
        } else {
            alert(data.message || 'Security audit failed');
        }
    })
    .catch(error => {
        alert('Error running security audit');
    });
}

function checkPerformance() {
    fetch('src/public/api/admin.php?action=performance_check', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Performance Score: ${data.score}%\nMemory Usage: ${data.memory_usage}%`);
        } else {
            alert(data.message || 'Performance check failed');
        }
    })
    .catch(error => {
        alert('Error checking performance');
    });
}

function createBackup() {
    if (confirm('Are you sure you want to create database backup?')) {
        fetch('src/public/api/admin.php?action=backup', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Backup created successfully');
                // Download backup file
                window.open(data.backup_url, '_blank');
            } else {
                alert(data.message || 'Backup failed');
            }
        })
        .catch(error => {
            alert('Error creating backup');
        });
    }
}

function importData() {
    const fileInput = document.getElementById('importFile');
    if (!fileInput.files.length) {
        alert('Please select a file to import');
        return;
    }
    
    const formData = new FormData();
    formData.append('file', fileInput.files[0]);
    
    fetch('src/public/api/admin.php?action=import', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Data imported successfully');
            location.reload();
        } else {
            alert(data.message || 'Import failed');
        }
    })
    .catch(error => {
        alert('Error importing data');
    });
}

function exportData() {
    window.open('src/public/api/admin.php?action=export', '_blank');
}

// Load recent logs
fetch('src/public/api/admin.php?action=logs')
.then(response => response.json())
.then(data => {
    if (data.success) {
        const logsHtml = data.logs.slice(0, 5).map(log => `
            <div class="border-bottom py-2">
                <small class="text-muted">${log.timestamp}</small><br>
                <span class="badge bg-${log.level === 'ERROR' ? 'danger' : 'info'}">${log.level}</span>
                ${log.message}
            </div>
        `).join('');
        document.getElementById('recentLogs').innerHTML = logsHtml;
    }
})
.catch(error => {
    document.getElementById('recentLogs').innerHTML = '<p class="text-danger">Failed to load logs</p>';
});
</script>
