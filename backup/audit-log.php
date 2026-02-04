<?php
// Audit Log Page
require_once __DIR__ . '/../../../bootstrap.php';

$auth = new Auth();
if (!$auth->isLoggedIn() || !$auth->hasPermission('admin_access')) {
    header('Location: /ksp_peb/login.php');
    exit;
}

$app = App::getInstance();
$coopDB = $app->getCoopDB();

// Get filters from request
$actionFilter = $_GET['action'] ?? '';
$userFilter = $_GET['user'] ?? '';
$dateFilter = $_GET['date'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 50;
$offset = ($page - 1) * $limit;

// Build query conditions
$conditions = [];
$params = [];

if ($actionFilter) {
    $conditions[] = "al.action LIKE ?";
    $params[] = "%$actionFilter%";
}

if ($userFilter) {
    $conditions[] = "(u.nama LIKE ? OR u.email LIKE ?)";
    $params[] = "%$userFilter%";
    $params[] = "%$userFilter%";
}

if ($dateFilter) {
    $conditions[] = "DATE(al.created_at) = ?";
    $params[] = $dateFilter;
}

$whereClause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

// Get total count
try {
    $countQuery = "
        SELECT COUNT(*) as total
        FROM audit_logs al
        LEFT JOIN users u ON al.user_id = u.id
        $whereClause
    ";
    $stmt = $coopDB->prepare($countQuery);
    $stmt->execute($params);
    $total = $stmt->fetch()['total'];
} catch (Exception $e) {
    $total = 0;
}

// Get audit logs
try {
    $query = "
        SELECT al.*, u.nama as user_name, u.email as user_email
        FROM audit_logs al
        LEFT JOIN users u ON al.user_id = u.id
        $whereClause
        ORDER BY al.created_at DESC
        LIMIT ? OFFSET ?
    ";
    $stmt = $coopDB->prepare($query);
    $params[] = $limit;
    $params[] = $offset;
    $stmt->execute($params);
    $logs = $stmt->fetchAll();
} catch (Exception $e) {
    $logs = [];
}

// Get unique actions for filter
try {
    $stmt = $coopDB->prepare("SELECT DISTINCT action FROM audit_logs ORDER BY action");
    $stmt->execute();
    $actions = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $actions = [];
}

$totalPages = ceil($total / $limit);
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Audit Log</h2>
            <div>
                <button class="btn btn-outline-primary" onclick="exportLogs()">
                    <i class="bi bi-download me-2"></i>Export
                </button>
                <button class="btn btn-outline-danger" onclick="clearLogs()">
                    <i class="bi bi-trash me-2"></i>Clear Logs
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="action" class="form-label">Action</label>
                <select class="form-select" id="action" name="action">
                    <option value="">All Actions</option>
                    <?php foreach ($actions as $action): ?>
                        <option value="<?php echo htmlspecialchars($action); ?>" <?php echo $actionFilter === $action ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($action); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="user" class="form-label">User</label>
                <input type="text" class="form-control" id="user" name="user" 
                       value="<?php echo htmlspecialchars($userFilter); ?>" placeholder="Name or Email">
            </div>
            <div class="col-md-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" class="form-control" id="date" name="date" 
                       value="<?php echo htmlspecialchars($dateFilter); ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Filter</button>
                <a href="/ksp_peb/dashboard.php?page=audit-log" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total Logs</h5>
                <p class="card-text fs-3"><?php echo number_format($total, 0, ',', '.'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Today</h5>
                <p class="card-text fs-3">
                    <?php 
                    try {
                        $stmt = $coopDB->prepare("SELECT COUNT(*) as count FROM audit_logs WHERE DATE(created_at) = CURDATE()");
                        $stmt->execute();
                        echo number_format($stmt->fetch()['count'], 0, ',', '.');
                    } catch (Exception $e) {
                        echo '0';
                    }
                    ?>
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5 class="card-title">This Week</h5>
                <p class="card-text fs-3">
                    <?php 
                    try {
                        $stmt = $coopDB->prepare("SELECT COUNT(*) as count FROM audit_logs WHERE WEEK(created_at) = WEEK(CURDATE())");
                        $stmt->execute();
                        echo number_format($stmt->fetch()['count'], 0, ',', '.');
                    } catch (Exception $e) {
                        echo '0';
                    }
                    ?>
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">This Month</h5>
                <p class="card-text fs-3">
                    <?php 
                    try {
                        $stmt = $coopDB->prepare("SELECT COUNT(*) as count FROM audit_logs WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
                        $stmt->execute();
                        echo number_format($stmt->fetch()['count'], 0, ',', '.');
                    } catch (Exception $e) {
                        echo '0';
                    }
                    ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Logs Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Audit Logs (<?php echo number_format($total, 0, ',', '.'); ?> records)</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($logs)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td>
                                <?php echo date('d/m/Y H:i:s', strtotime($log['created_at'])); ?>
                                <br><small class="text-muted"><?php echo time_ago(strtotime($log['created_at'])); ?></small>
                            </td>
                            <td>
                                <?php if ($log['user_name']): ?>
                                    <strong><?php echo htmlspecialchars($log['user_name']); ?></strong>
                                    <br><small class="text-muted"><?php echo htmlspecialchars($log['user_email']); ?></small>
                                <?php else: ?>
                                    <span class="text-muted">System</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-primary"><?php echo htmlspecialchars($log['action']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                            <td>
                                <small class="text-muted"><?php echo htmlspecialchars(substr($log['user_agent'], 0, 50)); ?>...</small>
                            </td>
                            <td>
                                <?php if ($log['details']): ?>
                                    <button class="btn btn-sm btn-outline-info" onclick="showDetails(<?php echo $log['id']; ?>)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&action=<?php echo urlencode($actionFilter); ?>&user=<?php echo urlencode($userFilter); ?>&date=<?php echo urlencode($dateFilter); ?>">Previous</a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&action=<?php echo urlencode($actionFilter); ?>&user=<?php echo urlencode($userFilter); ?>&date=<?php echo urlencode($dateFilter); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&action=<?php echo urlencode($actionFilter); ?>&user=<?php echo urlencode($userFilter); ?>&date=<?php echo urlencode($dateFilter); ?>">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        <?php else: ?>
            <p class="text-muted text-center">No audit logs found</p>
        <?php endif; ?>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="detailsContent">
                    <!-- Details will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<?php
function time_ago($timestamp) {
    $difference = time() - $timestamp;
    $periods = ["detik", "menit", "jam", "hari", "minggu", "bulan", "tahun", "dekade"];
    $lengths = [60, 60, 24, 7, 4.35, 12, 10];

    for ($i = 0; $difference >= $lengths[$i] && $i < count($lengths)-1; $i++) {
        $difference /= $lengths[$i];
    }

    $difference = round($difference);
    
    if ($difference != 1) {
        $periods[$i] .= " yang lalu";
    } else {
        $periods[$i] .= " yang lalu";
    }

    return "$difference $periods[$i]";
}
?>

<script>
function showDetails(logId) {
    fetch(`src/public/api/audit.php?action=details&id=${logId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const log = data.log;
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <strong>ID:</strong> ${log.id}<br>
                        <strong>Action:</strong> ${log.action}<br>
                        <strong>User:</strong> ${log.user_name || 'System'}<br>
                        <strong>IP Address:</strong> ${log.ip_address}<br>
                        <strong>Timestamp:</strong> ${new Date(log.created_at).toLocaleString()}
                    </div>
                    <div class="col-md-6">
                        <strong>User Agent:</strong><br>
                        <small>${log.user_agent}</small>
                    </div>
                </div>
                <hr>
                <strong>Details:</strong><br>
                <pre>${JSON.stringify(JSON.parse(log.details || '{}'), null, 2)}</pre>
            `;
            
            document.getElementById('detailsContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('detailsModal')).show();
        } else {
            alert(data.message || 'Failed to load log details');
        }
    })
    .catch(error => {
        alert('Error loading log details. Please try again.');
    });
}

function exportLogs() {
    const params = new URLSearchParams({
        action: '<?php echo $actionFilter; ?>',
        user: '<?php echo $userFilter; ?>',
        date: '<?php echo $dateFilter; ?>',
        export: '1'
    });
    
    window.open(`/ksp_peb/dashboard.php?page=audit-log&${params.toString()}`, '_blank');
}

function clearLogs() {
    if (confirm('Are you sure you want to clear all audit logs? This action cannot be undone.')) {
        fetch('src/public/api/audit.php?action=clear', {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Audit logs cleared successfully');
                location.reload();
            } else {
                alert(data.message || 'Failed to clear logs');
            }
        })
        .catch(error => {
            alert('Error clearing logs. Please try again.');
        });
    }
}
</script>
