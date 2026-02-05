<?php
// Dashboard Page Template dengan IndonesianHelper
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

// Generate unique ID
$uniqueId = uniqid('page_');

// Get cooperative name
$coopName = 'Koperasi Simpan Pinjam';
try {
    $stmt = $coopDB->prepare("SELECT value FROM configs WHERE key_name = 'coop_name'");
    $stmt->execute();
    $result = $stmt->fetch();
    if ($result) {
        $coopName = $result['value'];
    }
} catch (Exception $e) {
    // Use default name
}

// Page specific variables
$pageTitle = __('savings');
$pageDescription = 'Kelola simpanan anggota koperasi';
$showAddButton = true;
$addButtonText = __('add_savings');
$showExportButton = true;
?>

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1"><?php echo $pageTitle; ?></h4>
            <p class="text-muted mb-0"><?php echo $pageDescription; ?></p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" onclick="alert('Fitur tambah simpanan segera hadir')">
                <i class="bi bi-plus-circle me-2"></i><?php echo $addButtonText; ?>
            </button>
            <button class="btn btn-outline-success" onclick="alert('Fitur export segera hadir')">
                <i class="bi bi-download me-2"></i><?php echo __('export'); ?>
            </button>
        </div>
    </div>
</div>

<!-- Page Content -->
<div class="page-content">
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        <?php echo __('coming_soon'); ?> - Halaman simpanan sedang dalam pengembangan.
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="bi bi-piggy-bank"></i>
                    </div>
                    <div class="stat-value">0</div>
                    <div class="stat-label">Total Simpanan</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-success text-white">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="bi bi-cash"></i>
                    </div>
                    <div class="stat-value">Rp 0</div>
                    <div class="stat-label">Simpanan Wajib</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-warning text-white">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div class="stat-value">Rp 0</div>
                    <div class="stat-label">Simpanan Sukarela</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-info text-white">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="stat-value">0</div>
                    <div class="stat-label">Anggota Menabung</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Placeholder Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Daftar Simpanan</h5>
        </div>
        <div class="card-body">
            <div class="text-center py-5">
                <i class="bi bi-inbox display-4 text-muted"></i>
                <p class="text-muted mt-3">Belum ada data simpanan</p>
                <button class="btn btn-primary" onclick="alert('Fitur tambah simpanan segera hadir')">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Simpanan Pertama
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('Page loaded: <?php echo $pageTitle; ?>');
});
</script>
