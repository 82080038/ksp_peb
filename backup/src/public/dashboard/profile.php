<?php
// Profile Page
require_once __DIR__ . '/../../../bootstrap.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header('Location: /ksp_peb/login.php');
    exit;
}

$user = $auth->getCurrentUser();
$app = App::getInstance();
$coopDB = $app->getCoopDB();

// Get user's recent activities
try {
    $stmt = $coopDB->prepare("
        SELECT action, details, created_at 
        FROM audit_logs 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $stmt->execute([$user['id']]);
    $recentActivities = $stmt->fetchAll();
} catch (Exception $e) {
    $recentActivities = [];
}
?>

<div class="row">
    <div class="col-12">
        <h2>Profil Saya</h2>
        <p class="text-muted">Kelola informasi profil dan pengaturan akun Anda</p>
    </div>
</div>

<div class="row">
    <!-- Profile Information -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informasi Profil</h5>
            </div>
            <div class="card-body">
                <form id="profileForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama" name="nama" 
                                       value="<?php echo htmlspecialchars($user['nama']); ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($user['phone']); ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nik" class="form-label">NIK</label>
                                <input type="text" class="form-control" id="nik" name="nik" 
                                       value="<?php echo htmlspecialchars($user['nik'] ?? ''); ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status Akun</label>
                                <input type="text" class="form-control" id="status" name="status" 
                                       value="<?php echo htmlspecialchars($user['status']); ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="registered" class="form-label">Terdaftar Sejak</label>
                                <input type="text" class="form-control" id="registered" name="registered" 
                                       value="<?php echo date('d/m/Y', strtotime($user['created_at'] ?? 'now')); ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Roles</label>
                        <div>
                            <?php foreach ($user['roles'] as $role): ?>
                                <span class="badge bg-primary me-1"><?php echo htmlspecialchars($role['name']); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Ubah Password</h5>
            </div>
            <div class="card-body">
                <form id="changePasswordForm">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password Saat Ini</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                        <div class="progress mt-2" style="height: 5px;">
                            <div class="progress-bar" id="passwordStrength" role="progressbar" style="width: 0%"></div>
                        </div>
                        <small class="form-text text-muted">Password harus mengandung huruf besar, kecil, angka, dan karakter khusus</small>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Ubah Password</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Profile Picture -->
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-3">
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" 
                         style="width: 120px; height: 120px; font-size: 48px;">
                        <?php echo strtoupper(substr($user['nama'], 0, 1)); ?>
                    </div>
                </div>
                <h5><?php echo htmlspecialchars($user['nama']); ?></h5>
                <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                <div class="mt-3">
                    <?php foreach ($user['roles'] as $role): ?>
                        <span class="badge bg-primary"><?php echo htmlspecialchars($role['name']); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Account Statistics -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Statistik Akun</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Login Terakhir</span>
                    <span class="badge bg-success">Aktif</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Total Aktivitas</span>
                    <span class="badge bg-info"><?php echo count($recentActivities); ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Session Status</span>
                    <span class="badge bg-success">Online</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary" onclick="exportData()">
                        <i class="bi bi-download me-2"></i>Export Data Saya
                    </button>
                    <button class="btn btn-outline-info" onclick="viewHelp()">
                        <i class="bi bi-question-circle me-2"></i>Bantuan
                    </button>
                    <button class="btn btn-outline-warning" onclick="reportIssue()">
                        <i class="bi bi-bug me-2"></i>Laporkan Masalah
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Aktivitas Terkini</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($recentActivities)): ?>
                    <div class="timeline">
                        <?php foreach ($recentActivities as $activity): ?>
                        <div class="d-flex align-items-start mb-3">
                            <div class="me-3">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px; font-size: 16px;">
                                    <i class="bi bi-activity"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1"><?php echo htmlspecialchars($activity['action']); ?></h6>
                                <p class="text-muted mb-1">
                                    <?php if ($activity['details']): ?>
                                        <?php echo htmlspecialchars(substr($activity['details'], 0, 100)); ?>...
                                    <?php else: ?>
                                        Tidak ada detail
                                    <?php endif; ?>
                                </p>
                                <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($activity['created_at'])); ?></small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">Belum ada aktivitas</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Password strength checker
document.getElementById('new_password').addEventListener('input', function() {
    const password = this.value;
    const strengthBar = document.getElementById('passwordStrength');
    
    let strength = 0;
    
    // Length check
    if (password.length >= 8) strength += 25;
    
    // Uppercase check
    if (/[A-Z]/.test(password)) strength += 25;
    
    // Lowercase check
    if (/[a-z]/.test(password)) strength += 25;
    
    // Number check
    if (/[0-9]/.test(password)) strength += 25;
    
    // Special character check
    if (/[^A-Za-z0-9]/.test(password)) strength += 25;
    
    strengthBar.style.width = Math.min(strength, 100) + '%';
    
    if (strength < 25) {
        strengthBar.className = 'progress-bar bg-danger';
    } else if (strength < 50) {
        strengthBar.className = 'progress-bar bg-warning';
    } else if (strength < 75) {
        strengthBar.className = 'progress-bar bg-info';
    } else {
        strengthBar.className = 'progress-bar bg-success';
    }
});

// Change password form
document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const currentPassword = document.getElementById('current_password').value;
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (newPassword !== confirmPassword) {
        alert('Password baru tidak cocok dengan konfirmasi');
        return;
    }
    
    if (newPassword.length < 8) {
        alert('Password minimal 8 karakter');
        return;
    }
    
    fetch('src/public/api/auth.php?action=change_password', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            current_password: currentPassword,
            new_password: newPassword
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Password berhasil diubah');
            this.reset();
            document.getElementById('passwordStrength').style.width = '0%';
        } else {
            alert(data.message || 'Gagal mengubah password');
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan. Silakan coba lagi.');
    });
});

function exportData() {
    alert('Export data functionality coming soon');
}

function viewHelp() {
    window.open('dashboard.php?page=help', '_blank');
}

function reportIssue() {
    alert('Issue reporting functionality coming soon');
}
</script>
