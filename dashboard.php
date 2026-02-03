<?php
// Dashboard page
require_once __DIR__ . '/src/bootstrap.php';

// Check if user is logged in
$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = $auth->getCurrentUser();
$app = App::getInstance();
$coopName = $app->getConfig('coop_name', 'Koperasi Simpan Pinjam');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo htmlspecialchars($coopName); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            border-radius: 8px;
            margin: 2px 0;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }
        .main-content {
            padding: 2rem;
        }
        .stat-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .user-info {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4><?php echo htmlspecialchars($coopName); ?></h4>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="bi bi-house-door me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <?php if ($auth->hasPermission('view_members')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="anggota.php">
                                <i class="bi bi-people me-2"></i>
                                Anggota
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if ($auth->hasPermission('view_savings')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="simpanan.php">
                                <i class="bi bi-piggy-bank me-2"></i>
                                Simpanan
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if ($auth->hasPermission('view_loans')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="pinjaman.php">
                                <i class="bi bi-cash-stack me-2"></i>
                                Pinjaman
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if ($auth->hasPermission('view_accounts')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="akuntansi.php">
                                <i class="bi bi-journal-text me-2"></i>
                                Akuntansi
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if ($auth->hasPermission('view_reports')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="laporan.php">
                                <i class="bi bi-graph-up me-2"></i>
                                Laporan
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if ($auth->hasPermission('vote')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="voting.php">
                                <i class="bi bi-ballot me-2"></i>
                                Voting
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if ($auth->hasPermission('admin_access')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php">
                                <i class="bi bi-gear me-2"></i>
                                Admin
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item mt-3">
                            <a class="nav-link" href="#" onclick="logout()">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Header -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-download me-1"></i> Export
                            </button>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary">
                                <i class="bi bi-bell me-1"></i> Notifikasi
                            </button>
                        </div>
                    </div>
                </div>

                <!-- User Info Card -->
                <div class="user-info mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5>Selamat datang, <?php echo htmlspecialchars($user['nama']); ?>!</h5>
                            <p class="text-muted mb-0">
                                Email: <?php echo htmlspecialchars($user['email']); ?> | 
                                Phone: <?php echo htmlspecialchars($user['phone']); ?>
                                <?php if (!empty($user['nik'])): ?> | NIK: <?php echo htmlspecialchars($user['nik']); ?><?php endif; ?>
                            </p>
                            <div class="mt-2">
                                <?php foreach ($user['roles'] as $role): ?>
                                    <span class="badge bg-primary me-1"><?php echo htmlspecialchars($role['name']); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle me-1"></i> Akun Saya
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="showChangePasswordModal()">
                                        <i class="bi bi-key me-2"></i> Ganti Password
                                    </a></li>
                                    <li><a class="dropdown-item" href="profile.php">
                                        <i class="bi bi-person me-2"></i> Edit Profil
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#" onclick="logout()">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h5 class="card-title mb-0">Total Anggota</h5>
                                        <p class="card-text fs-2 fw-bold" id="totalAnggota">-</p>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-people fs-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h5 class="card-title mb-0">Total Simpanan</h5>
                                        <p class="card-text fs-2 fw-bold" id="totalSimpanan">-</p>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-piggy-bank fs-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card bg-warning text-white">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h5 class="card-title mb-0">Total Pinjaman</h5>
                                        <p class="card-text fs-2 fw-bold" id="totalPinjaman">-</p>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-cash-stack fs-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card bg-info text-white">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h5 class="card-title mb-0">SHU Tahun Ini</h5>
                                        <p class="card-text fs-2 fw-bold" id="shuTahunIni">-</p>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-graph-up fs-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Aktivitas Terkini</h5>
                    </div>
                    <div class="card-body">
                        <div id="recentActivities">
                            <div class="text-center text-muted">
                                <i class="bi bi-hourglass-split fs-1"></i>
                                <p>Memuat data...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ganti Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="changePasswordForm">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <input type="password" class="form-control" id="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_new_password" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="confirm_new_password" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="changePassword()">Ganti Password</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load dashboard statistics
        async function loadStatistics() {
            try {
                // Load total anggota
                const anggotaResponse = await fetch('src/public/api/anggota.php?action=count');
                const anggotaData = await anggotaResponse.json();
                if (anggotaData.success) {
                    document.getElementById('totalAnggota').textContent = anggotaData.count.toLocaleString('id-ID');
                }

                // Load total simpanan
                const simpananResponse = await fetch('src/public/api/simpanan.php?action=total');
                const simpananData = await simpananResponse.json();
                if (simpananData.success) {
                    document.getElementById('totalSimpanan').textContent = 'Rp ' + simpananData.total.toLocaleString('id-ID');
                }

                // Load total pinjaman
                const pinjamanResponse = await fetch('src/public/api/pinjaman.php?action=total');
                const pinjamanData = await pinjamanResponse.json();
                if (pinjamanData.success) {
                    document.getElementById('totalPinjaman').textContent = 'Rp ' + pinjamanData.total.toLocaleString('id-ID');
                }

                // Load SHU
                const shuResponse = await fetch('src/public/api/shu.php?action=current');
                const shuData = await shuResponse.json();
                if (shuData.success) {
                    document.getElementById('shuTahunIni').textContent = 'Rp ' + shuData.amount.toLocaleString('id-ID');
                }

            } catch (error) {
                console.error('Error loading statistics:', error);
            }
        }

        // Load recent activities
        async function loadRecentActivities() {
            try {
                const response = await fetch('src/public/api/audit.php?action=recent&limit=10');
                const data = await response.json();
                
                const container = document.getElementById('recentActivities');
                
                if (data.success && data.activities.length > 0) {
                    container.innerHTML = data.activities.map(activity => `
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div>
                                <strong>${activity.action}</strong>
                                <br>
                                <small class="text-muted">${new Date(activity.created_at).toLocaleString('id-ID')}</small>
                            </div>
                            <small class="text-muted">${activity.user_name || 'System'}</small>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = '<p class="text-muted text-center">Belum ada aktivitas</p>';
                }
            } catch (error) {
                document.getElementById('recentActivities').innerHTML = '<p class="text-danger text-center">Gagal memuat aktivitas</p>';
            }
        }

        // Show change password modal
        function showChangePasswordModal() {
            const modal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
            modal.show();
        }

        // Change password
        async function changePassword() {
            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_new_password').value;

            if (newPassword !== confirmPassword) {
                alert('Password baru tidak cocok');
                return;
            }

            try {
                const response = await fetch('src/public/api/auth.php?action=change_password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        current_password: currentPassword,
                        new_password: newPassword
                    })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Password berhasil diubah');
                    bootstrap.Modal.getInstance(document.getElementById('changePasswordModal')).hide();
                    document.getElementById('changePasswordForm').reset();
                } else {
                    alert(result.message || 'Gagal mengubah password');
                }
            } catch (error) {
                alert('Terjadi kesalahan. Silakan coba lagi.');
            }
        }

        // Logout
        async function logout() {
            try {
                await fetch('src/public/api/auth.php?action=logout', { method: 'POST' });
                window.location.href = 'login.php';
            } catch (error) {
                window.location.href = 'login.php';
            }
        }

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            loadStatistics();
            loadRecentActivities();
        });
    </script>
</body>
</html>
