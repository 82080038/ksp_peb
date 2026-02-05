<?php
// Complete Dashboard with Navigation for Super Admin
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mock user data for testing (Super Admin)
$user = [
    'id' => 1,
    'nama' => 'Super Admin',
    'email' => 'admin@koperasi.com',
    'roles' => ['super_admin', 'admin'],
    'permissions' => ['all']
];

$coopName = 'Koperasi Simpan Pinjam';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo htmlspecialchars($coopName); ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        
        .sidebar-logo {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .sidebar.collapsed .sidebar-logo {
            font-size: 1.2rem;
        }
        
        .sidebar-subtitle {
            font-size: 0.8rem;
            opacity: 0.8;
        }
        
        .sidebar.collapsed .sidebar-subtitle {
            display: none;
        }
        
        .user-info {
            padding: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        
        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 auto 0.5rem;
        }
        
        .sidebar.collapsed .user-avatar {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
        
        .user-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .sidebar.collapsed .user-name {
            font-size: 0.8rem;
        }
        
        .user-role {
            font-size: 0.8rem;
            opacity: 0.8;
        }
        
        .sidebar.collapsed .user-role {
            display: none;
        }
        
        .nav-section {
            padding: 1rem 0;
        }
        
        .nav-section-title {
            padding: 0.5rem 1.5rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.1rem;
            opacity: 0.6;
            margin-bottom: 0.5rem;
        }
        
        .sidebar.collapsed .nav-section-title {
            display: none;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .nav-link:hover,
        .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.18);
            border-left-color: red;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.08);
        }
        
        .nav-link i {
            font-size: 1.2rem;
            margin-right: 1rem;
            width: 20px;
            text-align: center;
            color: inherit;
            opacity: 0.9;
        }
        
        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }
        
        .nav-link-text {
            flex: 1;
        }
        
        .sidebar.collapsed .nav-link-text {
            display: none;
        }
        
        .nav-badge {
            background: #dc3545;
            color: white;
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
        }
        
        .sidebar.collapsed .nav-badge {
            display: none;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
            padding: 0;
        }
        
        .main-content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }
        
        /* Mobile Header */
        .mobile-header {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem;
            display: none;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        
        .mobile-menu-toggle {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--primary-color);
        }
        
        /* Dashboard Container */
        .dashboard-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            margin: 2rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }
        
        /* Statistics Cards */
        .stat-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
            height: 100%;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }
        
        .stat-card .card-body {
            padding: 1.5rem;
        }
        
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
            margin-bottom: 1rem;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin: 0.5rem 0;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
            margin: 0;
        }
        
        /* Buttons */
        .btn-gradient {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            color: white;
        }
        
        .sidebar-toggle {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1001;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 1.2rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        
        .sidebar-toggle:hover {
            transform: scale(1.1);
        }
        
        /* Alerts */
        .alert {
            border-radius: 10px;
            border: none;
            padding: 1rem;
        }
        
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .mobile-header {
                display: flex;
            }
            
            .sidebar-toggle {
                display: none;
            }
            
            .dashboard-container {
                margin: 1rem;
                padding: 1rem;
            }
        }
        
        /* Page Content */
        .page-content {
            display: none;
        }
        
        .page-content.active {
            display: block;
        }
        
        .page-header {
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .page-description {
            color: #6c757d;
            margin-bottom: 1rem;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="bi bi-bank"></i> Koperasi
            </div>
            <div class="sidebar-subtitle">Simpan Pinjam</div>
        </div>
        
        <div class="user-info">
            <div class="user-avatar">SA</div>
            <div class="user-name">Super Admin</div>
            <div class="user-role">Administrator</div>
        </div>
        
        <!-- Main Navigation -->
        <div class="nav-section">
            <div class="nav-section-title">Main Menu</div>
            <a href="#" class="nav-link active" data-page="home">
                <i class="bi bi-speedometer2"></i>
                <span class="nav-link-text">Dashboard</span>
            </a>
            <a href="#" class="nav-link" data-page="members">
                <i class="bi bi-people"></i>
                <span class="nav-link-text">Anggota</span>
                <span class="nav-badge">New</span>
            </a>
            <a href="#" class="nav-link" data-page="savings">
                <i class="bi bi-piggy-bank"></i>
                <span class="nav-link-text">Simpanan</span>
            </a>
            <a href="#" class="nav-link" data-page="loans">
                <i class="bi bi-cash-stack"></i>
                <span class="nav-link-text">Pinjaman</span>
            </a>
            <a href="#" class="nav-link" data-page="reports">
                <i class="bi bi-file-text"></i>
                <span class="nav-link-text">Laporan</span>
            </a>
        </div>
        
        <!-- Admin Tools -->
        <div class="nav-section">
            <div class="nav-section-title">Admin Tools</div>
            <a href="#" class="nav-link" data-page="users">
                <i class="bi bi-person-gear"></i>
                <span class="nav-link-text">User Management</span>
            </a>
            <a href="#" class="nav-link" data-page="roles">
                <i class="bi bi-shield-check"></i>
                <span class="nav-link-text">Role Management</span>
            </a>
            <a href="#" class="nav-link" data-page="permissions">
                <i class="bi bi-key"></i>
                <span class="nav-link-text">Permissions</span>
            </a>
            <a href="#" class="nav-link" data-page="settings">
                <i class="bi bi-gear"></i>
                <span class="nav-link-text">System Settings</span>
            </a>
            <a href="#" class="nav-link" data-page="audit">
                <i class="bi bi-clipboard-data"></i>
                <span class="nav-link-text">Audit Log</span>
            </a>
        </div>
        
        <!-- Cooperative Management -->
        <div class="nav-section">
            <div class="nav-section-title">Cooperative</div>
            <a href="#" class="nav-link" data-page="cooperative">
                <i class="bi bi-building"></i>
                <span class="nav-link-text">Cooperative Settings</span>
            </a>
            <a href="#" class="nav-link" data-page="voting">
                <i class="bi bi-ui-checks"></i>
                <span class="nav-link-text">Voting System</span>
            </a>
            <a href="#" class="nav-link" data-page="backup">
                <i class="bi bi-cloud-download"></i>
                <span class="nav-link-text">Backup & Restore</span>
            </a>
        </div>
        
        <!-- User Menu -->
        <div class="nav-section">
            <div class="nav-section-title">Account</div>
            <a href="#" class="nav-link" data-page="profile">
                <i class="bi bi-person"></i>
                <span class="nav-link-text">My Profile</span>
            </a>
            <a href="#" class="nav-link" onclick="logout()">
                <i class="bi bi-box-arrow-right"></i>
                <span class="nav-link-text">Logout</span>
            </a>
        </div>
    </div>
    
    <!-- Mobile Header -->
    <div class="mobile-header">
        <button class="mobile-menu-toggle" onclick="toggleSidebar()">
            <i class="bi bi-list"></i>
        </button>
        <h5 class="mb-0">Koperasi Dashboard</h5>
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#">Profile</a></li>
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" onclick="logout()">Logout</a></li>
            </ul>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Sidebar Toggle Button -->
        <button class="sidebar-toggle" onclick="toggleSidebar()" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        
        <!-- Dashboard Container -->
        <div class="dashboard-container">
            <!-- Home Page Content -->
            <div id="home" class="page-content active">
                <div class="page-header">
                    <h1 class="page-title">Dashboard Overview</h1>
                    <p class="page-description">Selamat datang di dashboard Koperasi Simpan Pinjam</p>
                </div>
                
                <!-- Success Message -->
                <div class="alert alert-success">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>Selamat Datang, Super Admin!</strong> - Sistem Koperasi Simpan Pinjam siap digunakan.
                </div>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <div class="stat-icon">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="stat-value">0</div>
                                <div class="stat-label">Total Anggota</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <div class="stat-icon">
                                    <i class="bi bi-piggy-bank"></i>
                                </div>
                                <div class="stat-value">Rp 0</div>
                                <div class="stat-label">Total Simpanan</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card stat-card bg-warning text-white">
                            <div class="card-body">
                                <div class="stat-icon">
                                    <i class="bi bi-cash-stack"></i>
                                </div>
                                <div class="stat-value">Rp 0</div>
                                <div class="stat-label">Total Pinjaman</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card stat-card bg-info text-white">
                            <div class="card-body">
                                <div class="stat-icon">
                                    <i class="bi bi-graph-up"></i>
                                </div>
                                <div class="stat-value">Rp 0</div>
                                <div class="stat-label">SHU Tahun Ini</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h4 class="mb-3">Quick Actions</h4>
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-gradient" onclick="navigateToPage('members')">
                                <i class="bi bi-person-plus me-2"></i> Tambah Anggota
                            </button>
                            <button class="btn btn-gradient" onclick="navigateToPage('savings')">
                                <i class="bi bi-plus-circle me-2"></i> Simpanan Baru
                            </button>
                            <button class="btn btn-gradient" onclick="navigateToPage('loans')">
                                <i class="bi bi-cash me-2"></i> Pinjaman Baru
                            </button>
                            <button class="btn btn-gradient" onclick="navigateToPage('reports')">
                                <i class="bi bi-file-text me-2"></i> Lihat Laporan
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- System Status -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">System Status</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Database Connection</span>
                                    <span class="badge bg-success">Online</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>API Services</span>
                                    <span class="badge bg-success">Running</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Session Management</span>
                                    <span class="badge bg-success">Active</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Last Updated</span>
                                    <span class="badge bg-info" id="currentTime"><?php echo date('H:i:s'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Recent Activity</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted text-center">Tidak ada aktivitas terkini</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Members Page Content -->
            <div id="members" class="page-content">
                <div class="page-header">
                    <h1 class="page-title">Manajemen Anggota</h1>
                    <p class="page-description">Kelola data anggota koperasi</p>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header"><h6 class="mb-0">Ringkasan</h6></div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li><strong>Total Anggota:</strong> 0</li>
                                    <li><strong>Aktif:</strong> 0</li>
                                    <li><strong>Menunggu:</strong> 0</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Daftar Anggota</h6>
                                <button class="btn btn-sm btn-gradient" data-bs-toggle="modal" data-bs-target="#modalAddMember"><i class="bi bi-person-plus me-1"></i>Tambah</button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nama</th><th>Email</th><th>Status</th><th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr><td colspan="4" class="text-center text-muted">Data anggota akan tampil di sini.</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Savings Page Content -->
            <div id="savings" class="page-content">
                <div class="page-header">
                    <h1 class="page-title">Manajemen Simpanan</h1>
                    <p class="page-description">Kelola simpanan anggota koperasi</p>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header"><h6 class="mb-0">Ringkasan</h6></div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li><strong>Total Simpanan:</strong> Rp 0</li>
                                    <li><strong>Simpanan Wajib:</strong> Rp 0</li>
                                    <li><strong>Simpanan Sukarela:</strong> Rp 0</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Transaksi Simpanan</h6>
                                <button class="btn btn-sm btn-gradient" data-bs-toggle="modal" data-bs-target="#modalAddSaving"><i class="bi bi-plus-circle me-1"></i>Tambah</button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead><tr><th>Tanggal</th><th>Anggota</th><th>Jenis</th><th>Jumlah</th></tr></thead>
                                        <tbody><tr><td colspan="4" class="text-center text-muted">Transaksi akan tampil di sini.</td></tr></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Loans Page Content -->
            <div id="loans" class="page-content">
                <div class="page-header">
                    <h1 class="page-title">Manajemen Pinjaman</h1>
                    <p class="page-description">Kelola pinjaman anggota koperasi</p>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header"><h6 class="mb-0">Ringkasan</h6></div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li><strong>Outstanding:</strong> Rp 0</li>
                                    <li><strong>Disetujui:</strong> 0</li>
                                    <li><strong>Menunggu:</strong> 0</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Daftar Pinjaman</h6>
                                <button class="btn btn-sm btn-gradient" data-bs-toggle="modal" data-bs-target="#modalAddLoan"><i class="bi bi-cash me-1"></i>Ajukan</button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead><tr><th>Anggota</th><th>Jumlah</th><th>Status</th><th>Jatuh Tempo</th></tr></thead>
                                        <tbody><tr><td colspan="4" class="text-center text-muted">Data pinjaman akan tampil di sini.</td></tr></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Reports Page Content -->
            <div id="reports" class="page-content">
                <div class="page-header">
                    <h1 class="page-title">Laporan</h1>
                    <p class="page-description">Laporan keuangan dan operasional koperasi</p>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h6 class="mb-0">Laporan Keuangan</h6></div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li><i class="bi bi-file-earmark-text me-1"></i> Neraca</li>
                                    <li><i class="bi bi-file-earmark-text me-1"></i> Laba Rugi</li>
                                    <li><i class="bi bi-file-earmark-text me-1"></i> Arus Kas</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h6 class="mb-0">Laporan Operasional</h6></div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li><i class="bi bi-people me-1"></i> Pertumbuhan Anggota</li>
                                    <li><i class="bi bi-piggy-bank me-1"></i> Tren Simpanan</li>
                                    <li><i class="bi bi-cash-stack me-1"></i> Tren Pinjaman</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Users Page Content -->
            <div id="users" class="page-content">
                <div class="page-header">
                    <h1 class="page-title">User Management</h1>
                    <p class="page-description">Kelola pengguna sistem</p>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header"><h6 class="mb-0">Ringkasan</h6></div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li><strong>Total User:</strong> 0</li>
                                    <li><strong>Aktif:</strong> 0</li>
                                    <li><strong>Role Terbanyak:</strong> -</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Daftar User</h6>
                                <button class="btn btn-sm btn-gradient" data-bs-toggle="modal" data-bs-target="#modalAddUser"><i class="bi bi-person-plus me-1"></i>Tambah</button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Status</th></tr></thead>
                                        <tbody><tr><td colspan="4" class="text-center text-muted">Data user akan tampil di sini.</td></tr></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Roles Page Content -->
            <div id="roles" class="page-content">
                <div class="page-header">
                    <h1 class="page-title">Role Management</h1>
                    <p class="page-description">Kelola peran pengguna</p>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Daftar Role</h6>
                                <button class="btn btn-sm btn-gradient" data-bs-toggle="modal" data-bs-target="#modalAddRole"><i class="bi bi-plus me-1"></i>Tambah</button>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0 text-muted">
                                    <li>super_admin</li>
                                    <li>pengawas</li>
                                    <li>anggota</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h6 class="mb-0">Role Default</h6></div>
                            <div class="card-body">
                                <p class="text-muted mb-0">Atur role default untuk user baru dan mapping perizinan.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Permissions Page Content -->
            <div id="permissions" class="page-content">
                <div class="page-header">
                    <h1 class="page-title">Permissions</h1>
                    <p class="page-description">Kelola hak akses pengguna</p>
                </div>
                
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Daftar Permissions</h6></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled mb-0 text-muted">
                                    <li>view_members</li>
                                    <li>manage_members</li>
                                    <li>view_savings</li>
                                    <li>manage_savings</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled mb-0 text-muted">
                                    <li>view_loans</li>
                                    <li>approve_loans</li>
                                    <li>manage_votes</li>
                                    <li>admin_access</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Settings Page Content -->
            <div id="settings" class="page-content">
                <div class="page-header">
                    <h1 class="page-title">System Settings</h1>
                    <p class="page-description">Pengaturan sistem koperasi</p>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h6 class="mb-0">Pengaturan Umum</h6></div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0 text-muted">
                                    <li>Nama Koperasi</li>
                                    <li>Timezone</li>
                                    <li>Bahasa Default</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h6 class="mb-0">Keamanan</h6></div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0 text-muted">
                                    <li>MFA & Password Policy</li>
                                    <li>Session Timeout</li>
                                    <li>IP Whitelist</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Audit Page Content -->
            <div id="audit" class="page-content">
                <div class="page-header">
                    <h1 class="page-title">Audit Log</h1>
                    <p class="page-description">Log aktivitas sistem</p>
                </div>
                
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Log Aktivitas</h6></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead><tr><th>Waktu</th><th>User</th><th>Aksi</th><th>Status</th></tr></thead>
                                <tbody><tr><td colspan="4" class="text-center text-muted">Catatan audit akan tampil di sini.</td></tr></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Cooperative Page Content -->
            <div id="cooperative" class="page-content">
                <div class="page-header">
                    <h1 class="page-title">Cooperative Settings</h1>
                    <p class="page-description">Pengaturan koperasi</p>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h6 class="mb-0">Informasi Dasar</h6></div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0 text-muted">
                                    <li>Nama Koperasi</li>
                                    <li>Jenis Koperasi</li>
                                    <li>Kontak Resmi</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h6 class="mb-0">Alamat & Legalitas</h6></div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0 text-muted">
                                    <li>Alamat Legal</li>
                                    <li>Nomor Badan Hukum</li>
                                    <li>NPWP</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Voting Page Content -->
            <div id="voting" class="page-content">
                <div class="page-header">
                    <h1 class="page-title">Voting System</h1>
                    <p class="page-description">Sistem voting koperasi</p>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Voting Aktif</h6>
                                <button class="btn btn-sm btn-gradient" data-bs-toggle="modal" data-bs-target="#modalCreateVoting"><i class="bi bi-plus me-1"></i>Buat</button>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0 text-muted">
                                    <li>Belum ada voting aktif.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h6 class="mb-0">Hasil Terakhir</h6></div>
                            <div class="card-body">
                                <p class="text-muted mb-0">Hasil voting terakhir akan tampil di sini.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Backup Page Content -->
            <div id="backup" class="page-content">
                <div class="page-header">
                    <h1 class="page-title">Backup & Restore</h1>
                    <p class="page-description">Backup dan restore data</p>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h6 class="mb-0">Backup</h6></div>
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <span class="text-muted">Buat backup penuh</span>
                                <button class="btn btn-sm btn-gradient" data-bs-toggle="modal" data-bs-target="#modalBackup"><i class="bi bi-cloud-arrow-down me-1"></i>Backup</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h6 class="mb-0">Restore</h6></div>
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <span class="text-muted">Unggah file backup</span>
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalRestore"><i class="bi bi-upload me-1"></i>Restore</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Profile Page Content -->
            <div id="profile" class="page-content">
                <div class="page-header">
                    <h1 class="page-title">My Profile</h1>
                    <p class="page-description">Profil pengguna</p>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Profil</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-center">Fitur profile sedang dalam pengembangan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleBtn = document.getElementById('sidebarToggle');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
            // Change icon
            if (sidebar.classList.contains('collapsed')) {
                toggleBtn.innerHTML = '<i class="bi bi-chevron-right"></i>';
            } else {
                toggleBtn.innerHTML = '<i class="bi bi-list"></i>';
            }
        }
        
        // Mobile Sidebar Toggle
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        }
        
        // Enhanced page navigation dengan error handling dan debugging
        function navigateToPage(pageId) {
            console.log('Navigating to page:', pageId);
            
            // Debug: Check if pageId is valid
            if (!pageId) {
                console.error('No pageId provided');
                return;
            }
            
            // Hide all pages
            const pages = document.querySelectorAll('.page-content');
            console.log('Found pages:', pages.length);
            pages.forEach(page => page.classList.remove('active'));
            
            // Show selected page
            const selectedPage = document.getElementById(pageId);
            console.log('Selected page:', selectedPage);
            
            if (selectedPage) {
                selectedPage.classList.add('active');
                
                // Initialize page-specific functionality
                initializePage(pageId);
                
                // Show success message
                showSuccess(`Halaman ${pageId} berhasil dimuat`);
            } else {
                // Try to load page via AJAX if not found
                console.log('Page not found, trying AJAX load...');
                loadPageContent(pageId);
            }
            
            // Update active nav link
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => link.classList.remove('active'));
            
            const activeLink = document.querySelector(`[data-page="${pageId}"]`);
            if (activeLink) {
                activeLink.classList.add('active');
                console.log('Active link updated:', pageId);
            } else {
                console.error('Active link not found for page:', pageId);
            }
        }

        let occupationsData = [];

        // Load occupations for Add Member modal
        async function loadOccupations() {
            const select = document.getElementById('pekerjaan');
            if (!select) return;
            select.innerHTML = '<option value="">Memuat...</option>';
            try {
                const res = await fetch('src/public/api/anggota.php?action=occupations');
                const data = await res.json();
                if (data.success && Array.isArray(data.data)) {
                    occupationsData = data.data;
                    const count = data.data.length;
                    select.innerHTML = `<option value="">-(${count}) Pilihan-</option>`;
                    data.data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.name;
                        opt.textContent = item.name;
                        select.appendChild(opt);
                    });
                } else {
                    select.innerHTML = '<option value="">-(0) Pilihan-</option>';
                }
            } catch (e) {
                select.innerHTML = '<option value="">-(0) Pilihan-</option>';
            }
        }

        function applyRanksForOccupation(selectedName) {
            const rankSelect = document.getElementById('pangkat');
            if (!rankSelect) return;
            rankSelect.innerHTML = '';
            const occ = occupationsData.find(o => (o.name || '').toLowerCase() === (selectedName || '').toLowerCase());
            if (occ && occ.requires_rank && Array.isArray(occ.ranks) && occ.ranks.length) {
                rankSelect.disabled = false;
                rankSelect.innerHTML = `<option value="">-(${occ.ranks.length}) Pangkat-</option>`;
                occ.ranks.forEach(r => {
                    const opt = document.createElement('option');
                    opt.value = r;
                    opt.textContent = (r || '').toUpperCase();
                    rankSelect.appendChild(opt);
                });
            } else {
                rankSelect.disabled = true;
                rankSelect.innerHTML = '<option value="">Tidak memerlukan pangkat</option>';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadOccupations();
            const jobSelect = document.getElementById('pekerjaan');
            const rankSelect = document.getElementById('pangkat');
            if (rankSelect) {
                rankSelect.disabled = true;
                rankSelect.innerHTML = '<option value="">Tidak memerlukan pangkat</option>';
            }
            if (jobSelect) {
                jobSelect.addEventListener('change', function(e) {
                    applyRanksForOccupation(e.target.value);
                });
            }
        });

        // Load page content via AJAX
        function loadPageContent(pageId) {
            const pageContent = document.getElementById('pageContent');
            if (!pageContent) return;
            
            // Show loading state
            pageContent.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary mb-3" role="status"></div><p>Memuat halaman...</p></div>';
            
            // Try to load page content
            fetch(`/ksp_peb/src/public/dashboard/${pageId}.php`)
                .then(response => response.text())
                .then(html => {
                    pageContent.innerHTML = html;
                    initializePage(pageId);
                    showSuccess(`Halaman ${pageId} berhasil dimuat`);
                })
                .catch(error => {
                    console.error('Error loading page:', error);
                    showError(`Gagal memuat halaman ${pageId}. Silakan coba lagi.`);
                });
        }

        // Initialize page-specific functionality
        function initializePage(pageId) {
            console.log('Initializing page:', pageId);
            
            switch(pageId) {
                case 'home':
                    initializeDashboard();
                    break;
                case 'members':
                    initializeAnggota();
                    break;
                case 'savings':
                    initializeSimpanan();
                    break;
                case 'loans':
                    initializePinjaman();
                    break;
                case 'reports':
                    initializeLaporan();
                    break;
                case 'users':
                    initializeUsers();
                    break;
                case 'roles':
                    initializeRoles();
                    break;
                case 'permissions':
                    initializePermissions();
                    break;
                case 'settings':
                    initializeSettings();
                    break;
                case 'audit':
                    initializeAudit();
                    break;
                case 'cooperative':
                    initializeCooperative();
                    break;
                case 'voting':
                    initializeVoting();
                    break;
                case 'backup':
                    initializeBackup();
                    break;
                case 'profile':
                    initializeProfile();
                    break;
                case 'help':
                    initializeHelp();
                    break;
                default:
                    console.log('No specific initialization for page:', pageId);
            }
        }

        // Initialize Dashboard
        function initializeDashboard() {
            console.log('Initializing dashboard...');
            
            // Auto-refresh dashboard data every 30 seconds
            setInterval(function() {
                const currentPage = getCurrentPage();
                if (currentPage === 'home') {
                    refreshDashboardData();
                }
            }, 30000);
        }

        function refreshDashboardData() {
            console.log('Refreshing dashboard data...');
            // Add dashboard data refresh logic here
        }

        // Initialize Anggota Management
        function initializeAnggota() {
            console.log('Initializing anggota management...');
            // Add anggota management initialization here
        }

        // Initialize Simpanan Management
        function initializeSimpanan() {
            console.log('Initializing simpanan management...');
            // Add simpanan management initialization here
        }

        // Initialize Pinjaman Management
        function initializePinjaman() {
            console.log('Initializing pinjaman management...');
            // Add pinjaman management initialization here
        }

        // Initialize Laporan
        function initializeLaporan() {
            console.log('Initializing laporan...');
            // Add laporan initialization here
        }

        // Initialize User Management
        function initializeUsers() {
            console.log('Initializing user management...');
            // Add user management initialization here
        }

        // Initialize Role Management
        function initializeRoles() {
            console.log('Initializing role management...');
            // Add role management initialization here
        }

        // Initialize Permissions
        function initializePermissions() {
            console.log('Initializing permissions...');
            // Add permissions initialization here
        }

        // Initialize System Settings
        function initializeSettings() {
            console.log('Initializing system settings...');
            // Add system settings initialization here
        }

        // Initialize Audit Log
        function initializeAudit() {
            console.log('Initializing audit log...');
            // Add audit log initialization here
        }

        // Initialize Cooperative Settings
        function initializeCooperative() {
            console.log('Initializing cooperative settings...');
            // Add cooperative settings initialization here
        }

        // Initialize Voting System
        function initializeVoting() {
            console.log('Initializing voting system...');
            // Add voting system initialization here
        }

        // Initialize Backup & Restore
        function initializeBackup() {
            console.log('Initializing backup & restore...');
            // Add backup & restore initialization here
        }

        // Initialize Profile
        function initializeProfile() {
            console.log('Initializing profile...');
            // Add profile initialization here
        }

        // Initialize Help
        function initializeHelp() {
            console.log('Initializing help...');
            // Add help initialization here
        }

        // Get current page
        function getCurrentPage() {
            try {
                const activePage = document.querySelector('.page-content.active');
                return activePage ? activePage.id : 'home';
            } catch (error) {
                console.error('Error in getCurrentPage:', error);
                return 'home';
            }
        }

        // Logout
        function logout() {
            try {
                if (confirm('Apakah Anda yakin ingin logout?')) {
                    window.location.href = '/ksp_peb/login.php';
                }
            } catch (error) {
                console.error('Error in logout:', error);
                window.location.href = '/ksp_peb/login.php';
            }
        }
        
        // Enhanced success handling dengan bahasa Indonesia
        function showSuccess(message) {
            console.log('SUCCESS: ' + message);
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success';
            alertDiv.innerHTML = `<i class="bi-check-circle me-2"></i>${message}</div>`;
            
            // Find page content container
            const pageContent = document.getElementById('pageContent');
            if (pageContent) {
                pageContent.innerHTML = alertDiv.outerHTML;
            }
            
            // Auto-hide success setelah 3 detik
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert-success');
                alerts.forEach(alert => {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                });
            }, 3000);
        }

        // Enhanced error handling dengan bahasa Indonesia
        function showError(message, type = 'danger') {
            try {
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type}`;
                alertDiv.innerHTML = `<i class="bi bi-exclamation-triangle me-2"></i>${message}</div>`;
                
                // Find page content container
                const pageContent = document.getElementById('pageContent');
                if (pageContent) {
                    pageContent.innerHTML = alertDiv.outerHTML;
                }
                
                // Auto-hide alert setelah 3 detik
                setTimeout(function() {
                    const alerts = document.querySelectorAll('.alert-' + type);
                    alerts.forEach(function(alert) {
                        if (alert.parentNode) {
                            alert.parentNode.removeChild(alert);
                        }
                    });
                }, 3000);
            } catch (error) {
                console.error('Error in showError:', error);
                alert('Error: ' + message);
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded');
            
            // Navigation click handlers
            const navLinks = document.querySelectorAll('.nav-link[data-page]');
            console.log('Found nav links:', navLinks.length);
            
            navLinks.forEach(link => {
                console.log('Adding click listener to:', link);
                link.addEventListener('click', function(e) {
                    console.log('Nav link clicked:', e.target);
                    e.preventDefault();
                    
                    const pageId = this.getAttribute('data-page');
                    console.log('Page ID from data-page:', pageId);
                    
                    // Remove hash from URL
                    if (window.location.hash) {
                        window.history.replaceState(null, '', window.location.pathname);
                    }
                    
                    navigateToPage(pageId);
                });
            });
            
            // Mobile menu toggle
            const mobileToggle = document.querySelector('.mobile-menu-toggle');
            if (mobileToggle) {
                mobileToggle.addEventListener('click', toggleMobileSidebar);
            }
            
            // Debug: Check if all pages exist
            const pages = document.querySelectorAll('.page-content');
            console.log('Available pages:');
            pages.forEach(page => {
                console.log('- ' + page.id);
            });
            
            // Debug: Check if all nav links have data-page
            navLinks.forEach(link => {
                const pageId = link.getAttribute('data-page');
                console.log('Nav link data-page:', pageId);
            });
        });
        
        // Auto-update time
        setInterval(function() {
            const timeElement = document.getElementById('currentTime');
            if (timeElement) {
                timeElement.textContent = new Date().toLocaleTimeString();
            }
        }, 1000);
        
        console.log('Dashboard initialized successfully');
    </script>
    
    <script>
        // Responsive handling
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                const sidebar = document.getElementById('sidebar');
                sidebar.classList.remove('show');
            }
        });
    </script>

    <!-- Cascading dropdown Tambah Anggota -->
    <script>
        // Data statis placeholder; ganti ke API backend bila tersedia
        const INSTANSI_OPTIONS = [
            { id: 'PEMERINTAH', name: 'Pemerintah' },
            { id: 'BUMN', name: 'BUMN' },
            { id: 'BUMD', name: 'BUMD' },
            { id: 'SWASTA', name: 'Swasta' },
            { id: 'LAINNYA', name: 'Lainnya' }
        ];

        const PEKERJAAN_BY_INSTANSI = {
            PEMERINTAH: [
                { id: 'PNS', name: 'Pegawai Negeri Sipil', hasRank: true },
                { id: 'TNI', name: 'Tentara Nasional Indonesia', hasRank: true },
                { id: 'POLRI', name: 'Kepolisian Negara RI', hasRank: true }
            ],
            BUMN: [
                { id: 'BUMN_STAF', name: 'Pegawai BUMN', hasRank: false }
            ],
            BUMD: [
                { id: 'BUMD_STAF', name: 'Pegawai BUMD', hasRank: false }
            ],
            SWASTA: [
                { id: 'KARYAWAN', name: 'Karyawan Swasta', hasRank: false },
                { id: 'WIRASWASTA', name: 'Wiraswasta', hasRank: false }
            ],
            LAINNYA: [
                { id: 'LAIN', name: 'Lainnya', hasRank: false }
            ]
        };

        const GOLONGAN_BY_PEKERJAAN = {
            PNS: [
                { id: 'JURU', name: 'Juru (I)' },
                { id: 'PENGATUR', name: 'Pengatur (II)' },
                { id: 'PENATA', name: 'Penata (III)' },
                { id: 'PEMBINA', name: 'Pembina (IV)' }
            ],
            TNI: [
                { id: 'TAMTAMA', name: 'Tamtama' },
                { id: 'BINTARA', name: 'Bintara' },
                { id: 'PERWIRA', name: 'Perwira' }
            ],
            POLRI: [
                { id: 'TAMTAMA', name: 'Tamtama' },
                { id: 'BINTARA', name: 'Bintara' },
                { id: 'PERWIRA', name: 'Perwira' }
            ]
        };

        const PANGKAT_BY_GOLONGAN = {
            JURU: [
                { id: 'I_A', name: 'Juru Muda (I/a)' },
                { id: 'I_B', name: 'Juru Muda Tingkat I (I/b)' },
                { id: 'I_C', name: 'Juru (I/c)' },
                { id: 'I_D', name: 'Juru Tingkat I (I/d)' }
            ],
            PENGATUR: [
                { id: 'II_A', name: 'Pengatur Muda (II/a)' },
                { id: 'II_B', name: 'Pengatur Muda Tingkat I (II/b)' },
                { id: 'II_C', name: 'Pengatur (II/c)' },
                { id: 'II_D', name: 'Pengatur Tingkat I (II/d)' }
            ],
            PENATA: [
                { id: 'III_A', name: 'Penata Muda (III/a)' },
                { id: 'III_B', name: 'Penata Muda Tingkat I (III/b)' },
                { id: 'III_C', name: 'Penata (III/c)' },
                { id: 'III_D', name: 'Penata Tingkat I (III/d)' }
            ],
            PEMBINA: [
                { id: 'IV_A', name: 'Pembina (IV/a)' },
                { id: 'IV_B', name: 'Pembina Tingkat I (IV/b)' },
                { id: 'IV_C', name: 'Pembina Utama Muda (IV/c)' },
                { id: 'IV_D', name: 'Pembina Utama Madya (IV/d)' },
                { id: 'IV_E', name: 'Pembina Utama (IV/e)' }
            ],
            TAMTAMA: [
                { id: 'T1', name: 'Tamtama Muda' },
                { id: 'T2', name: 'Tamtama' }
            ],
            BINTARA: [
                { id: 'B1', name: 'Bintara Muda' },
                { id: 'B2', name: 'Bintara' }
            ],
            PERWIRA: [
                { id: 'P1', name: 'Perwira Muda' },
                { id: 'P2', name: 'Perwira' },
                { id: 'P3', name: 'Perwira Tinggi' }
            ]
        };

        function resetPekerjaan() {
            const pekerjaan = document.getElementById('pekerjaan');
            if (pekerjaan) {
                pekerjaan.innerHTML = '<option value="">Pilih Pekerjaan</option>';
                pekerjaan.disabled = true;
            }
        }

        function resetGolonganPangkat() {
            const gol = document.getElementById('golongan');
            const pangkat = document.getElementById('pangkat');
            const wrap = document.getElementById('wrap_golongan');
            if (gol) {
                gol.innerHTML = '<option value="">Pilih Golongan Pangkat</option>';
                gol.disabled = true;
            }
            if (pangkat) {
                pangkat.innerHTML = '<option value="">Pilih Pangkat</option>';
                pangkat.disabled = true;
            }
            if (wrap) wrap.style.display = 'none';
        }

        function loadInstansi() {
            const instansi = document.getElementById('instansi');
            if (!instansi) return;
            instansi.innerHTML = '<option value="">Pilih Jenis Instansi</option>';
            INSTANSI_OPTIONS.forEach((opt) => {
                const o = document.createElement('option');
                o.value = opt.id;
                o.textContent = opt.name;
                instansi.appendChild(o);
            });
        }

        function loadPekerjaan() {
            const instansiVal = document.getElementById('instansi')?.value;
            const pekerjaan = document.getElementById('pekerjaan');
            resetPekerjaan();
            resetGolonganPangkat();
            if (!pekerjaan || !instansiVal) return;

            const list = PEKERJAAN_BY_INSTANSI[instansiVal] || [];
            if (list.length === 0) return;

            pekerjaan.disabled = false;
            list.forEach((item) => {
                const o = document.createElement('option');
                o.value = item.id;
                o.textContent = item.name;
                o.dataset.hasRank = item.hasRank ? '1' : '0';
                pekerjaan.appendChild(o);
            });
        }

        function loadGolongan() {
            const pekerjaan = document.getElementById('pekerjaan');
            const selected = pekerjaan?.options[pekerjaan.selectedIndex];
            const hasRank = selected && selected.dataset.hasRank === '1';
            const gol = document.getElementById('golongan');
            const wrapGol = document.getElementById('wrap_golongan');
            const pangkat = document.getElementById('pangkat');

            if (!gol || !pangkat || !wrapGol) return;

            gol.innerHTML = '<option value="">Pilih Golongan Pangkat</option>';
            pangkat.innerHTML = '<option value="">Pilih Pangkat</option>';
            gol.disabled = true;
            pangkat.disabled = true;
            wrapGol.style.display = 'none';

            if (!selected || !hasRank) {
                pangkat.innerHTML = '<option value="">Tidak memerlukan pangkat</option>';
                return;
            }

            const list = GOLONGAN_BY_PEKERJAAN[selected.value] || [];
            if (list.length === 0) return;

            wrapGol.style.display = 'block';
            gol.disabled = false;
            list.forEach((item) => {
                const o = document.createElement('option');
                o.value = item.id;
                o.textContent = item.name;
                gol.appendChild(o);
            });
        }

        function loadPangkat() {
            const gol = document.getElementById('golongan');
            const pangkat = document.getElementById('pangkat');
            if (!gol || !pangkat) return;

            pangkat.innerHTML = '<option value="">Pilih Pangkat</option>';
            pangkat.disabled = true;

            if (!gol.value) return;
            const list = PANGKAT_BY_GOLONGAN[gol.value] || [];
            if (list.length === 0) return;

            pangkat.disabled = false;
            list.forEach((item) => {
                const o = document.createElement('option');
                o.value = item.id;
                o.textContent = item.name;
                pangkat.appendChild(o);
            });
        }

        // Reset setiap kali modal Tambah Anggota dibuka
        const modalAdd = document.getElementById('modalAddMember');
        if (modalAdd) {
            modalAdd.addEventListener('shown.bs.modal', () => {
                const form = document.getElementById('formAddMember');
                if (form) form.reset();
                loadInstansi();
                resetPekerjaan();
                resetGolonganPangkat();
            });
        }
    </script>
</body>
</html>

<!-- Modal: Tambah Anggota -->
<div class="modal fade" id="modalAddMember" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Anggota</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formAddMember">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Jenis Instansi</label>
              <select class="form-select" id="instansi" name="instansi" onchange="loadPekerjaan()">
                <option value="">Pilih Jenis Instansi</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Pekerjaan</label>
              <select class="form-select" id="pekerjaan" name="pekerjaan" onchange="loadGolongan()" disabled>
                <option value="">Pilih Pekerjaan</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Pangkat</label>
              <select class="form-select" id="pangkat" name="pangkat" disabled>
                <option value="">Pilih Pangkat</option>
              </select>
            </div>
            <div class="col-md-6" id="wrap_golongan" style="display:none;">
              <label class="form-label">Golongan Pangkat</label>
              <select class="form-select" id="golongan" name="golongan" onchange="loadPangkat()" disabled>
                <option value="">Pilih Golongan Pangkat</option>
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label">NRP</label>
              <input type="text" class="form-control" id="nrp" name="nrp">
            </div>
            <div class="col-md-3">
              <label class="form-label">NIK</label>
              <input type="text" class="form-control" id="nik" name="nik">
            </div>
            <div class="col-md-6">
              <label class="form-label">Nama</label>
              <input type="text" class="form-control" id="nama" name="nama" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Gender</label>
              <select class="form-select" id="gender" name="gender">
                <option value="male">Laki-laki</option>
                <option value="female">Perempuan</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Agama</label>
              <select class="form-select" id="agama" name="agama">
                <option value="islam">Islam</option>
                <option value="kristen">Kristen</option>
                <option value="katolik">Katolik</option>
                <option value="hindu">Hindu</option>
                <option value="budha">Budha</option>
                <option value="konghucu">Konghucu</option>
                <option value="lainnya">Lainnya</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Status Kawin</label>
              <select class="form-select" id="status_kawin" name="status_kawin">
                <option value="belum_kawin">Belum Kawin</option>
                <option value="kawin">Kawin</option>
                <option value="cerai_hidup">Cerai Hidup</option>
                <option value="cerai_mati">Cerai Mati</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Telepon</label>
              <input type="text" class="form-control" id="phone" name="phone">
            </div>

            <div class="col-md-4">
              <label class="form-label">Status</label>
              <select class="form-select" id="status" name="status">
                <option value="active">Aktif</option>
                <option value="pending">Pending</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-gradient" onclick="submitMember()">Simpan</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Tambah Simpanan -->
<div class="modal fade" id="modalAddSaving" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Simpanan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formAddSaving">
          <div class="mb-3">
            <label class="form-label">Anggota</label>
            <input type="text" class="form-control" id="saving_member" name="member" placeholder="Cari anggota">
          </div>
          <div class="mb-3">
            <label class="form-label">Jenis Simpanan</label>
            <select class="form-select" id="saving_jenis" name="jenis">
              <option value="wajib">Wajib</option>
              <option value="sukarela">Sukarela</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Jumlah</label>
            <input type="number" class="form-control" id="saving_jumlah" name="jumlah" min="0" step="1000">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-gradient" onclick="submitSaving()">Simpan</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Tambah Pinjaman -->
<div class="modal fade" id="modalAddLoan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ajukan Pinjaman</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formAddLoan">
          <div class="mb-3">
            <label class="form-label">Anggota</label>
            <input type="text" class="form-control" id="loan_member" name="member" placeholder="Cari anggota">
          </div>
          <div class="mb-3">
            <label class="form-label">Jumlah</label>
            <input type="number" class="form-control" id="loan_amount" name="amount" min="0" step="100000">
          </div>
          <div class="mb-3">
            <label class="form-label">Tenor (bulan)</label>
            <input type="number" class="form-control" id="loan_tenor" name="tenor" min="1" max="60">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-gradient" onclick="submitLoan()">Ajukan</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Tambah User -->
<div class="modal fade" id="modalAddUser" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formAddUser">
          <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" class="form-control" id="user_nama" name="nama" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" id="user_email" name="email" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Role</label>
            <select class="form-select" id="user_role" name="role">
              <option value="super_admin">super_admin</option>
              <option value="pengawas">pengawas</option>
              <option value="anggota">anggota</option>
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-gradient" onclick="submitUser()">Simpan</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Tambah Role -->
<div class="modal fade" id="modalAddRole" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Role</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formAddRole">
          <div class="mb-3">
            <label class="form-label">Nama Role</label>
            <input type="text" class="form-control" id="role_name" name="role" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-gradient" onclick="submitRole()">Simpan</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Buat Voting -->
<div class="modal fade" id="modalCreateVoting" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Buat Voting</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formCreateVoting">
          <div class="mb-3">
            <label class="form-label">Judul Voting</label>
            <input type="text" class="form-control" id="voting_title" name="title" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea class="form-control" id="voting_description" name="description" rows="2"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Jenis Voting</label>
            <select class="form-select" id="voting_type" name="type">
              <option value="yesno">Ya/Tidak</option>
              <option value="options">Pilihan Ganda</option>
              <option value="candidate">Pilih Kandidat</option>
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-gradient" onclick="submitVoting()">Simpan</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Backup -->
<div class="modal fade" id="modalBackup" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Backup Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="mb-0 text-muted">Konfirmasi pembuatan backup penuh.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-gradient" onclick="submitBackup()">Backup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Restore -->
<div class="modal fade" id="modalRestore" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Restore Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formRestore">
          <div class="mb-3">
            <label class="form-label">File Backup</label>
            <input type="file" class="form-control" id="restore_backup_file" name="backup_file" accept=".sql,.zip">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-gradient" onclick="submitRestore()">Restore</button>
      </div>
    </div>
  </div>
</div>
