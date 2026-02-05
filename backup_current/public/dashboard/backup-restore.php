<?php
// Backup & Restore Page
require_once __DIR__ . '/../../../bootstrap.php';

$auth = new Auth();
if (!$auth->isLoggedIn() || !$auth->hasPermission('admin_access')) {
    header('Location: /ksp_peb/login.php');
    exit;
}

$app = App::getInstance();
$coopDB = $app->getCoopDB();

// Get backup history
try {
    $stmt = $coopDB->prepare("
        SELECT * FROM backup_history 
        ORDER BY created_at DESC 
        LIMIT 20
    ");
    $stmt->execute();
    $backups = $stmt->fetchAll();
} catch (Exception $e) {
    $backups = [];
}

// Get database info
try {
    $stmt = $coopDB->query("SHOW TABLE STATUS");
    $tables = $stmt->fetchAll();
    
    $totalSize = 0;
    foreach ($tables as $table) {
        $totalSize += $table['Data_length'] + $table['Index_length'];
    }
    
    $dbSize = number_format($totalSize / 1024 / 1024, 2);
} catch (Exception $e) {
    $dbSize = 'Unknown';
}
?>

<div class="row">
    <div class="col-12">
        <h2>Backup & Restore</h2>
        <p class="text-muted">Kelola backup dan restore database sistem</p>
    </div>
</div>

<!-- Database Info -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Database Size</h5>
                <p class="card-text fs-3"><?php echo $dbSize; ?> MB</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Total Tables</h5>
                <p class="card-text fs-3"><?php echo count($tables); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5 class="card-title">Last Backup</h5>
                <p class="card-text fs-3">
                    <?php 
                    if (!empty($backups)) {
                        echo date('d/m', strtotime($backups[0]['created_at']));
                    } else {
                        echo 'Never';
                    }
                    ?>
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">Auto Backup</h5>
                <p class="card-text fs-3">OFF</p>
            </div>
        </div>
    </div>
</div>

<!-- Create Backup -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Create New Backup</h5>
            </div>
            <div class="card-body">
                <form id="backupForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="backupType" class="form-label">Backup Type</label>
                                <select class="form-select" id="backupType" name="backupType">
                                    <option value="full">Full Backup</option>
                                    <option value="structure">Structure Only</option>
                                    <option value="data">Data Only</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="backupName" class="form-label">Backup Name</label>
                                <input type="text" class="form-control" id="backupName" name="backupName" 
                                       placeholder="Optional - auto-generated if empty">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="compress" name="compress" checked>
                            <label class="form-check-label" for="compress">
                                Compress backup file
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-download me-2"></i>Create Backup
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Restore Backup -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Restore Backup</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <strong>Warning:</strong> Restore will overwrite current data. Make sure you have a backup before proceeding.
                </div>
                <form id="restoreForm">
                    <div class="mb-3">
                        <label for="backupFile" class="form-label">Select Backup File</label>
                        <input type="file" class="form-control" id="backupFile" name="backupFile" 
                               accept=".sql,.gz,.zip" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="confirmRestore" name="confirmRestore" required>
                            <label class="form-check-label" for="confirmRestore">
                                I understand that this will overwrite current data
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-upload me-2"></i>Restore Backup
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Backup History -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Backup History</h5>
                <button class="btn btn-sm btn-outline-danger" onclick="clearOldBackups()">Clear Old Backups</button>
            </div>
            <div class="card-body">
                <?php if (!empty($backups)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>File Name</th>
                                    <th>Type</th>
                                    <th>Size</th>
                                    <th>Created</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($backups as $backup): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($backup['filename']); ?></td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo htmlspecialchars($backup['type']); ?></span>
                                    </td>
                                    <td><?php echo number_format($backup['file_size'] / 1024 / 1024, 2); ?> MB</td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($backup['created_at'])); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $backup['status'] === 'completed' ? 'success' : 'warning'; ?>">
                                            <?php echo htmlspecialchars($backup['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" onclick="downloadBackup('<?php echo $backup['id']; ?>')">
                                                <i class="bi bi-download"></i>
                                            </button>
                                            <button class="btn btn-outline-info" onclick="verifyBackup('<?php echo $backup['id']; ?>')">
                                                <i class="bi bi-shield-check"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="deleteBackup('<?php echo $backup['id']; ?>')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">No backup history found</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Backup -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Schedule Backup</h5>
            </div>
            <div class="card-body">
                <form id="scheduleForm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="scheduleType" class="form-label">Schedule Type</label>
                                <select class="form-select" id="scheduleType" name="scheduleType">
                                    <option value="">No Schedule</option>
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="scheduleTime" class="form-label">Time</label>
                                <input type="time" class="form-control" id="scheduleTime" name="scheduleTime" value="02:00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="retentionDays" class="form-label">Retention Days</label>
                                <input type="number" class="form-control" id="retentionDays" name="retentionDays" 
                                       value="30" min="1" max="365">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Schedule</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Create backup
document.getElementById('backupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('src/public/api/backup.php?action=create', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Backup created successfully');
            location.reload();
        } else {
            alert(data.message || 'Backup failed');
        }
    })
    .catch(error => {
        alert('Error creating backup');
    });
});

// Restore backup
document.getElementById('restoreForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!confirm('Are you absolutely sure you want to restore this backup? This action cannot be undone.')) {
        return;
    }
    
    const formData = new FormData(this);
    
    fetch('src/public/api/backup.php?action=restore', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Backup restored successfully. System will restart.');
            setTimeout(() => {
                location.reload();
            }, 3000);
        } else {
            alert(data.message || 'Restore failed');
        }
    })
    .catch(error => {
        alert('Error restoring backup');
    });
});

function downloadBackup(backupId) {
    window.open(`src/public/api/backup.php?action=download&id=${backupId}`, '_blank');
}

function verifyBackup(backupId) {
    fetch(`src/public/api/backup.php?action=verify&id=${backupId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Backup verified successfully');
        } else {
            alert(data.message || 'Backup verification failed');
        }
    })
    .catch(error => {
        alert('Error verifying backup');
    });
}

function deleteBackup(backupId) {
    if (confirm('Are you sure you want to delete this backup?')) {
        fetch(`src/public/api/backup.php?action=delete&id=${backupId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Backup deleted successfully');
                location.reload();
            } else {
                alert(data.message || 'Delete failed');
            }
        })
        .catch(error => {
            alert('Error deleting backup');
        });
    }
}

function clearOldBackups() {
    if (confirm('Are you sure you want to delete all old backups?')) {
        fetch('src/public/api/backup.php?action=clear_old', {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Old backups cleared successfully');
                location.reload();
            } else {
                alert(data.message || 'Failed to clear backups');
            }
        })
        .catch(error => {
            alert('Error clearing backups');
        });
    }
}

// Save schedule
document.getElementById('scheduleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('src/public/api/backup.php?action=schedule', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Backup schedule saved successfully');
        } else {
            alert(data.message || 'Failed to save schedule');
        }
    })
    .catch(error => {
        alert('Error saving schedule');
    });
});
</script>
