<?php
session_start();
require_once __DIR__ . '/../../app/bootstrap.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['cooperative_id'])) {
    header('Location: /ksp_peb/login.php');
    exit;
}

$app = new App();
$cooperativeId = $_SESSION['cooperative_id'];

// Get cooperative information
$cooperative = new Cooperative();
$coopData = $cooperative->getCooperative($cooperativeId);

if (!$coopData) {
    $_SESSION['error'] = 'Koperasi tidak ditemukan';
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Koperasi - KSP Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../src/public/css/dashboard.css" rel="stylesheet">
    <style>
        .settings-section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .section-title {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .form-label small {
            font-size: 0.75em;
            font-weight: normal;
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 500;
        }
        .status-belum {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-terdaftar {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-badan_hukum {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navigation -->
        <?php include 'includes/topnav.php'; ?>

        <!-- Page Content -->
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="page-title">Pengaturan Koperasi</h2>
                <div>
                    <span class="status-badge <?php echo 'status-' . $coopData['badan_hukum']; ?>">
                        <?php echo ucfirst(str_replace('_', ' ', $coopData['badan_hukum'])); ?>
                    </span>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Basic Information -->
            <div class="settings-section">
                <h4 class="section-title">
                    <i class="bi bi-building me-2"></i>Informasi Dasar
                </h4>
                <form id="basicInfoForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_koperasi" class="form-label">Nama Koperasi</label>
                                <input type="text" class="form-control" id="nama_koperasi" name="nama_koperasi" 
                                       value="<?php echo htmlspecialchars($coopData['nama']); ?>" required tabindex="1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jenis_koperasi" class="form-label">Jenis Koperasi <small class="text-muted">(PP No. 7/2021)</small></label>
                                <select class="form-control" id="jenis_koperasi" name="jenis_koperasi" required tabindex="2">
                                    <option value="">Pilih Jenis Koperasi</option>
                                    <!-- Options will be loaded from database -->
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="badan_hukum" class="form-label">Status Badan Hukum <small class="text-muted">(UU No. 25/1992 & UU No. 6/2023)</small></label>
                                <select class="form-control" id="badan_hukum" name="badan_hukum" required tabindex="3">
                                    <option value="belum_terdaftar" <?php echo $coopData['badan_hukum'] == 'belum_terdaftar' ? 'selected' : ''; ?>>Belum Terdaftar</option>
                                    <option value="terdaftar" <?php echo $coopData['badan_hukum'] == 'terdaftar' ? 'selected' : ''; ?>>Terdaftar (SABH)</option>
                                    <option value="badan_hukum" <?php echo $coopData['badan_hukum'] == 'badan_hukum' ? 'selected' : ''; ?>>Badan Hukum (SABH)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_pendirian" class="form-label">Tanggal Pendirian</label>
                                <input type="date" class="form-control" id="tanggal_pendirian" name="tanggal_pendirian" 
                                       value="<?php echo $coopData['tanggal_pendirian']; ?>" required tabindex="4">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="npwp" class="form-label">NPWP <small class="text-muted">(PMK No. 112/2022)</small></label>
                                <input type="text" class="form-control" id="npwp" name="npwp" 
                                       value="<?php echo htmlspecialchars($coopData['npwp']); ?>" 
                                       placeholder="16 digit NPWP" tabindex="5">
                                <div class="form-text text-muted small">Format: 3201234567890001 (16 digit tanpa separator)</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kontak_resmi" class="form-label">Kontak Resmi</label>
                                <input type="tel" class="form-control" id="kontak_resmi" name="kontak_resmi" 
                                       value="<?php echo htmlspecialchars($coopData['kontak_resmi']); ?>" required tabindex="6">
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary" tabindex="7">
                            <i class="bi bi-save me-2"></i>Simpan Informasi Dasar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Legal Information -->
            <div class="settings-section">
                <h4 class="section-title">
                    <i class="bi bi-shield-check me-2"></i>Informasi Legal
                </h4>
                <form id="legalInfoForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nomor_bh" class="form-label">Nomor Badan Hukum (SABH)</label>
                                <input type="text" class="form-control" id="nomor_bh" name="nomor_bh" 
                                       value="<?php echo htmlspecialchars($coopData['nomor_bh'] ?? ''); ?>" 
                                       placeholder="AHU-XXXXXXX.AH.01.01.Tahun" tabindex="8">
                                <div class="form-text text-muted small">Nomor SABH (jika sudah terdaftar)</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nib" class="form-label">NIB <small class="text-muted">(OSS)</small></label>
                                <input type="text" class="form-control" id="nib" name="nib" 
                                       value="<?php echo htmlspecialchars($coopData['nib'] ?? ''); ?>" 
                                       placeholder="13 digit NIB" tabindex="9">
                                <div class="form-text text-muted small">Nomor Induk Berusaha (jika ada)</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nik_koperasi" class="form-label">NIK Koperasi</label>
                                <input type="text" class="form-control" id="nik_koperasi" name="nik_koperasi" 
                                       value="<?php echo htmlspecialchars($coopData['nik_koperasi'] ?? ''); ?>" 
                                       placeholder="16 digit NIK">
                                <div class="form-text text-muted small">Nomor Induk Koperasi (jika ada)</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="modal_pokok" class="form-label">Modal Pokok</label>
                                <input type="text" class="form-control" id="modal_pokok" name="modal_pokok" 
                                       value="<?php echo $coopData['modal_pokok'] ? number_format($coopData['modal_pokok'], 0, ',', '.') : ''; ?>" 
                                       placeholder="Rp 0">
                                <div class="form-text text-muted small">Modal pokok sesuai anggaran dasar (format otomatis Rupiah)</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="status_notes" class="form-label">Catatan Status</label>
                                <textarea class="form-control" id="status_notes" name="status_notes" rows="3" 
                                          placeholder="Catatan mengenai status badan hukum atau informasi lainnya"><?php echo htmlspecialchars($coopData['status_notes'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Simpan Informasi Legal
                        </button>
                    </div>
                </form>
            </div>

            <!-- Status History -->
            <div class="settings-section">
                <h4 class="section-title">
                    <i class="bi bi-clock-history me-2"></i>Riwayat Status Badan Hukum
                </h4>
                <div class="table-responsive">
                    <table class="table table-striped" id="statusHistoryTable">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Status Sebelumnya</th>
                                <th>Status Baru</th>
                                <th>Alasan Perubahan</th>
                                <th>Diubah Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Memuat riwayat status...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Document History -->
            <div class="settings-section">
                <h4 class="section-title">
                    <i class="bi bi-file-alt me-2"></i>Riwayat Perubahan Dokumen Legal
                </h4>
                <div class="table-responsive">
                    <table class="table table-striped" id="documentHistoryTable">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis Dokumen</th>
                                <th>Nilai Lama</th>
                                <th>Nilai Baru</th>
                                <th>Alasan Perubahan</th>
                                <th>Diubah Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Memuat riwayat dokumen...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../src/public/js/form-helper.js"></script>
    <script src="../src/public/js/avoid-next-error.js"></script>
    <script>
        // Load cooperative types
        async function loadCooperativeTypes() {
            try {
                const response = await fetch('../src/public/api/cooperative.php?action=types');
                const result = await response.json();
                
                if (result.success && result.data) {
                    const jenisSelect = document.getElementById('jenis_koperasi');
                    jenisSelect.innerHTML = '<option value="">Pilih Jenis Koperasi</option>';
                    
                    result.data.forEach(type => {
                        const option = document.createElement('option');
                        option.value = type.code;
                        option.textContent = type.name;
                        // Select current jenis
                        <?php if ($coopData['jenis']): ?>
                        const currentJenis = <?php echo json_encode($coopData['jenis']); ?>;
                        if (typeof currentJenis === 'object' && currentJenis.code === type.code) {
                            option.selected = true;
                        } else if (currentJenis === type.code) {
                            option.selected = true;
                        }
                        <?php endif; ?>
                        jenisSelect.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error loading cooperative types:', error);
                // Show user-friendly error message
                const jenisSelect = document.getElementById('jenis_koperasi');
                if (jenisSelect) {
                    jenisSelect.innerHTML = '<option value="">Gagal memuat jenis koperasi</option>';
                }
            }
        }

        // Setup currency formatting for modal_pokok
        FormHelper.setupCurrencyFormatting('modal_pokok');

        // Setup NPWP formatting
        FormHelper.setupNPWPFormatting('npwp');

        // Load status history
        async function loadStatusHistory() {
            try {
                const response = await fetch(`../src/public/api/cooperative-settings.php?action=status_history&id=<?php echo $cooperativeId; ?>`);
                const result = await response.json();
                
                if (result.success && result.data) {
                    const tbody = document.querySelector('#statusHistoryTable tbody');
                    tbody.innerHTML = '';
                    
                    result.data.forEach(history => {
                        const row = tbody.insertRow();
                        row.innerHTML = `
                            <td>${history.tanggal_efektif || '-'}</td>
                            <td>${history.status_sebelumnya || '-'}</td>
                            <td><span class="status-badge status-${history.status_baru}">${history.status_baru}</span></td>
                            <td>${history.change_reason || '-'}</td>
                            <td>${history.user_name || '-'}</td>
                        `;
                    });
                }
            } catch (error) {
                console.error('Error loading status history:', error);
                const tbody = document.querySelector('#statusHistoryTable tbody');
                if (tbody) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Gagal memuat riwayat status</td></tr>';
                }
            }
        }

        // Load document history
        async function loadDocumentHistory() {
            try {
                const response = await fetch(`../src/public/api/cooperative-settings.php?action=document_history&id=<?php echo $cooperativeId; ?>`);
                const result = await response.json();
                
                if (result.success && result.data) {
                    const tbody = document.querySelector('#documentHistoryTable tbody');
                    tbody.innerHTML = '';
                    
                    result.data.forEach(history => {
                        const row = tbody.insertRow();
                        const oldValue = history.document_number_lama || (history.document_value_lama ? formatCurrency(history.document_value_lama) : '-');
                        const newValue = history.document_number_baru || (history.document_value_baru ? formatCurrency(history.document_value_baru) : '-');
                        
                        row.innerHTML = `
                            <td>${history.tanggal_efektif || '-'}</td>
                            <td>${getDocumentTypeLabel(history.document_type)}</td>
                            <td>${oldValue}</td>
                            <td>${newValue}</td>
                            <td>${history.change_reason || '-'}</td>
                            <td>${history.user_name || '-'}</td>
                        `;
                    });
                }
            } catch (error) {
                console.error('Error loading document history:', error);
                const tbody = document.querySelector('#documentHistoryTable tbody');
                if (tbody) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Gagal memuat riwayat dokumen</td></tr>';
                }
            }
        }

        // Get document type label
        function getDocumentTypeLabel(type) {
            const labels = {
                'nomor_bh': 'Nomor Badan Hukum',
                'nib': 'NIB',
                'nik_koperasi': 'NIK Koperasi',
                'modal_pokok': 'Modal Pokok'
            };
            return labels[type] || type;
        }

        // Format currency
        function formatCurrency(amount) {
            if (!amount) return '-';
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(amount);
        }

        // Form submissions
        document.getElementById('basicInfoForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            // Clean NPWP
            if (data.npwp) {
                data.npwp = data.npwp.replace(/[^0-9]/g, '');
            }
            
            try {
                const response = await fetch(`../src/public/api/cooperative-settings.php?action=update&id=<?php echo $cooperativeId; ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        });

        document.getElementById('legalInfoForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            // Clean currency formatting
            if (data.modal_pokok) {
                data.modal_pokok = data.modal_pokok.replace(/[^0-9]/g, '');
            }
            
            try {
                const response = await fetch(`../src/public/api/cooperative.php?action=update_legal&id=<?php echo $cooperativeId; ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadCooperativeTypes();
            loadStatusHistory();
            
            // Reset forms on page load (keep current values)
            // Note: Forms are pre-filled with existing data, so we don't reset them
            // Instead, we just clear any validation states
            const basicInfoForm = document.getElementById('basicInfoForm');
            const legalInfoForm = document.getElementById('legalInfoForm');
            
            if (basicInfoForm) {
                basicInfoForm.querySelectorAll('.is-invalid, .is-valid').forEach(el => {
                    el.classList.remove('is-invalid', 'is-valid');
                });
            }
            
            if (legalInfoForm) {
                legalInfoForm.querySelectorAll('.is-invalid, .is-valid').forEach(el => {
                    el.classList.remove('is-invalid', 'is-valid');
                });
            }
            
            // Setup ENTER key navigation for forms
            FormHelper.setupEnterKeyNavigation('basicInfoForm');
            FormHelper.setupEnterKeyNavigation('legalInfoForm');
        });
    </script>
</body>
</html>
