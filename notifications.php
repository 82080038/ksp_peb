<?php
// Notifications Page
require_once __DIR__ . '/../../../bootstrap.php';

$auth = new Auth();
if (!$auth->isLoggedIn() || !$auth->hasPermission('admin_access')) {
    header('Location: /ksp_peb/login.php');
    exit;
}

$app = App::getInstance();
$coopDB = $app->getCoopDB();

// Get notifications
try {
    $stmt = $coopDB->prepare("
        SELECT * FROM notifications 
        WHERE user_id = ? OR user_id IS NULL 
        ORDER BY created_at DESC 
        LIMIT 50
    ");
    $stmt->execute([$user['id']]);
    $notifications = $stmt->fetchAll();
} catch (Exception $e) {
    $notifications = [];
}

// Get unread count
try {
    $stmt = $coopDB->prepare("
        SELECT COUNT(*) as count 
        FROM notifications 
        WHERE (user_id = ? OR user_id IS NULL) 
        AND is_read = FALSE
    ");
    $stmt->execute([$user['id']]);
    $unreadCount = $stmt->fetch()['count'];
} catch (Exception $e) {
    $unreadCount = 0;
}
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Notifikasi</h2>
            <div>
                <button class="btn btn-outline-primary" onclick="markAllRead()">
                    <i class="bi bi-check-all me-2"></i>Tandai Semua Dibaca
                </button>
                <button class="btn btn-outline-danger" onclick="clearAll()">
                    <i class="bi bi-trash me-2"></i>Hapus Semua
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Notification Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total Notifikasi</h5>
                <p class="card-text fs-3"><?php echo count($notifications); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5 class="card-title">Belum Dibaca</h5>
                <p class="card-text fs-3"><?php echo $unreadCount; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Sudah Dibaca</h5>
                <p class="card-text fs-3"><?php echo count($notifications) - $unreadCount; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">Hari Ini</h5>
                <p class="card-text fs-3">
                    <?php 
                    $todayCount = 0;
                    foreach ($notifications as $notif) {
                        if (date('Y-m-d', strtotime($notif['created_at'])) === date('Y-m-d')) {
                            $todayCount++;
                        }
                    }
                    echo $todayCount;
                    ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Create Notification -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Buat Notifikasi Baru</h5>
            </div>
            <div class="card-body">
                <form id="notificationForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="notifType" class="form-label">Tipe Notifikasi</label>
                                <select class="form-select" id="notifType" name="type">
                                    <option value="info">Info</option>
                                    <option value="success">Success</option>
                                    <option value="warning">Warning</option>
                                    <option value="error">Error</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="notifPriority" class="form-label">Prioritas</label>
                                <select class="form-select" id="notifPriority" name="priority">
                                    <option value="low">Rendah</option>
                                    <option value="medium">Sedang</option>
                                    <option value="high">Tinggi</option>
                                    <option value="urgent">Darurat</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notifTitle" class="form-label">Judul</label>
                        <input type="text" class="form-control" id="notifTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="notifMessage" class="form-label">Pesan</label>
                        <textarea class="form-control" id="notifMessage" name="message" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="notifTarget" class="form-label">Target</label>
                                <select class="form-select" id="notifTarget" name="target">
                                    <option value="all">Semua User</option>
                                    <option value="admin">Admin Only</option>
                                    <option value="members">Anggota Only</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="notifExpiry" class="form-label">Kadaluarsa (hari)</label>
                                <input type="number" class="form-control" id="notifExpiry" name="expiry" value="7" min="1" max="365">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-2"></i>Kirim Notifikasi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Notifications List -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Daftar Notifikasi</h5>
        <div class="btn-group btn-group-sm">
            <button class="btn btn-outline-primary" onclick="filterNotifications('all')">Semua</button>
            <button class="btn btn-outline-warning" onclick="filterNotifications('unread')">Belum Dibaca</button>
            <button class="btn btn-outline-success" onclick="filterNotifications('read')">Sudah Dibaca</button>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($notifications)): ?>
            <div class="notification-list" id="notificationList">
                <?php foreach ($notifications as $notification): ?>
                <div class="notification-item <?php echo $notification['is_read'] ? 'read' : 'unread'; ?>" 
                     data-id="<?php echo $notification['id']; ?>">
                    <div class="d-flex align-items-start">
                        <div class="me-3">
                            <div class="notification-icon bg-<?php echo getNotificationColor($notification['type']); ?> text-white rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 40px; height: 40px;">
                                <i class="bi <?php echo getNotificationIcon($notification['type']); ?>"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1"><?php echo htmlspecialchars($notification['title']); ?></h6>
                                    <p class="mb-1"><?php echo htmlspecialchars($notification['message']); ?></p>
                                    <small class="text-muted">
                                        <?php echo date('d/m/Y H:i', strtotime($notification['created_at'])); ?>
                                        <?php if ($notification['priority'] !== 'low'): ?>
                                        <span class="badge bg-<?php echo getPriorityColor($notification['priority']); ?> ms-2">
                                            <?php echo htmlspecialchars($notification['priority']); ?>
                                        </span>
                                        <?php endif; ?>
                                    </small>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <?php if (!$notification['is_read']): ?>
                                    <button class="btn btn-outline-success" onclick="markRead(<?php echo $notification['id']; ?>)">
                                        <i class="bi bi-check"></i>
                                    </button>
                                    <?php endif; ?>
                                    <button class="btn btn-outline-danger" onclick="deleteNotification(<?php echo $notification['id']; ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted text-center">Tidak ada notifikasi</p>
        <?php endif; ?>
    </div>
</div>

<style>
.notification-item {
    border-bottom: 1px solid #dee2e6;
    padding: 1rem 0;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item.unread {
    background-color: #f8f9fa;
    border-left: 4px solid #007bff;
}

.notification-item.read {
    opacity: 0.7;
}

.notification-icon {
    font-size: 18px;
}
</style>

<?php
function getNotificationColor($type) {
    $colors = [
        'info' => 'info',
        'success' => 'success',
        'warning' => 'warning',
        'error' => 'danger'
    ];
    return $colors[$type] ?? 'secondary';
}

function getNotificationIcon($type) {
    $icons = [
        'info' => 'bi-info-circle',
        'success' => 'bi-check-circle',
        'warning' => 'bi-exclamation-triangle',
        'error' => 'bi-x-circle'
    ];
    return $icons[$type] ?? 'bi-bell';
}

function getPriorityColor($priority) {
    $colors = [
        'low' => 'secondary',
        'medium' => 'info',
        'high' => 'warning',
        'urgent' => 'danger'
    ];
    return $colors[$priority] ?? 'secondary';
}
?>

<script>
// Create notification
document.getElementById('notificationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('src/public/api/notifications.php?action=create', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Notifikasi berhasil dikirim');
            this.reset();
            location.reload();
        } else {
            alert(data.message || 'Gagal mengirim notifikasi');
        }
    })
    .catch(error => {
        alert('Error sending notification');
    });
});

function markRead(notificationId) {
    fetch(`src/public/api/notifications.php?action=mark_read&id=${notificationId}`, {
        method: 'PUT'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const element = document.querySelector(`[data-id="${notificationId}"]`);
            element.classList.remove('unread');
            element.classList.add('read');
            location.reload();
        } else {
            alert(data.message || 'Failed to mark as read');
        }
    })
    .catch(error => {
        alert('Error marking as read');
    });
}

function deleteNotification(notificationId) {
    if (confirm('Are you sure you want to delete this notification?')) {
        fetch(`src/public/api/notifications.php?action=delete&id=${notificationId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Notification deleted successfully');
                location.reload();
            } else {
                alert(data.message || 'Failed to delete notification');
            }
        })
        .catch(error => {
            alert('Error deleting notification');
        });
    }
}

function markAllRead() {
    fetch('src/public/api/notifications.php?action=mark_all_read', {
        method: 'PUT'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('All notifications marked as read');
            location.reload();
        } else {
            alert(data.message || 'Failed to mark all as read');
        }
    })
    .catch(error => {
        alert('Error marking all as read');
    });
}

function clearAll() {
    if (confirm('Are you sure you want to delete all notifications?')) {
        fetch('src/public/api/notifications.php?action=clear_all', {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('All notifications cleared');
                location.reload();
            } else {
                alert(data.message || 'Failed to clear notifications');
            }
        })
        .catch(error => {
            alert('Error clearing notifications');
        });
    }
}

function filterNotifications(type) {
    const items = document.querySelectorAll('.notification-item');
    
    items.forEach(item => {
        if (type === 'all') {
            item.style.display = 'block';
        } else if (type === 'unread') {
            item.style.display = item.classList.contains('unread') ? 'block' : 'none';
        } else if (type === 'read') {
            item.style.display = item.classList.contains('read') ? 'block' : 'none';
        }
    });
}
</script>
