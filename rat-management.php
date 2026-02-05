<?php
require_once __DIR__ . '/../../../app/bootstrap.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['cooperative_id'])) {
    header('Location: /ksp_peb/login.php');
    exit;
}

// Get cooperative data
$cooperative = new Cooperative();
$coopData = $cooperative->getCooperativeById($_SESSION['cooperative_id']);
$modalPokokHistory = $cooperative->getModalPokokHistory($_SESSION['cooperative_id']);
$ratSessions = $cooperative->getRATSessions($_SESSION['cooperative_id']);

if (!$coopData) {
    header('Location: ../dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAT Management - <?php echo htmlspecialchars($coopData['nama']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .section-title {
            border-left: 4px solid #667eea;
            padding-left: 1rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .badge-status {
            font-size: 0.8em;
            padding: 0.25rem 0.5rem;
        }
        .badge-scheduled {
            background-color: #ffc107;
            color: #000;
        }
        .badge-in_progress {
            background-color: #17a2b8;
            color: #fff;
        }
        .badge-completed {
            background-color: #28a745;
            color: #fff;
        }
        .badge-cancelled {
            background-color: #dc3545;
            color: #fff;
        }
        .change-positive {
            color: #28a745;
            font-weight: bold;
        }
        .change-negative {
            color: #dc3545;
            font-weight: bold;
        }
        .modal-pokok-display {
            font-size: 1.2rem;
            font-weight: bold;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    RAT Management
                    <small class="text-muted ms-2">- <?php echo htmlspecialchars($coopData['nama']); ?></small>
                </h2>
            </div>
        </div>

        <!-- Current Modal Pokok Stats -->
        <div class="stats-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-0">Modal Pokok Saat Ini</h4>
                    <p class="mb-0 opacity-75">Nilai modal pokok koperasi berdasarkan hasil RAT terakhir</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="modal-pokok-display">
                        Rp <?php echo number_format($coopData['modal_pokok'], 0, ',', '.'); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Quick Actions</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <button class="btn btn-primary w-100" onclick="showCreateRATModal()">
                                    <i class="fas fa-plus me-2"></i>
                                    Buat Sesi RAT Baru
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-warning w-100" onclick="showUpdateModalPokokModal()">
                                    <i class="fas fa-edit me-2"></i>
                                    Update Modal Pokok Manual
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-info w-100" onclick="refreshData()">
                                    <i class="fas fa-sync me-2"></i>
                                    Refresh Data
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RAT Sessions -->
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="section-title">
                    <i class="fas fa-calendar-alt me-2"></i>Sesi RAT
                </h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tahun</th>
                                <th>Tanggal Rapat</th>
                                <th>Tempat</th>
                                <th>Status</th>
                                <th>Modal Pokok Sebelum</th>
                                <th>Modal Pokok Sesudah</th>
                                <th>% Perubahan</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ratSessions)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">Belum ada sesi RAT</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($ratSessions as $session): ?>
                                <tr>
                                    <td><?php echo $session['tahun']; ?></td>
                                    <td><?php echo date('d M Y', strtotime($session['tanggal_rapat'])); ?></td>
                                    <td><?php echo htmlspecialchars($session['tempat']); ?></td>
                                    <td>
                                        <span class="badge badge-status badge-<?php echo $session['status']; ?>">
                                            <?php echo ucfirst($session['status']); ?>
                                        </span>
                                    </td>
                                    <td>Rp <?php echo number_format($session['modal_pokok_sebelum'], 0, ',', '.'); ?></td>
                                    <td>
                                        <?php 
                                        if ($session['status'] === 'completed'): 
                                            echo 'Rp ' . number_format($session['modal_pokok_setelah'], 0, ',', '.');
                                        else: 
                                            echo '-';
                                        endif; 
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if ($session['status'] === 'completed'): 
                                            $change = $session['modal_pokok_setelah'] - $session['modal_pokok_sebelum'];
                                            $percent = ($change / $session['modal_pokok_sebelum']) * 100;
                                            echo '<span class="change-' . ($change >= 0 ? 'positive' : 'negative') . '">';
                                            echo number_format($percent, 2) . '%';
                                            echo '</span>';
                                        else: 
                                            echo '-';
                                        endif; 
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($session['status'] === 'completed'): ?>
                                            <button class="btn btn-sm btn-success" onclick="viewRATDetails(<?php echo $session['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        <?php elseif ($session['status'] === 'in_progress'): ?>
                                            <button class="btn btn-sm btn-warning" onclick="completeRATSession(<?php echo $session['id']; ?>)">
                                                <i class="fas fa-check"></i> Complete
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-primary" onclick="startRATSession(<?php echo $session['id']; ?>)">
                                                <i class="fas fa-play"></i> Start
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Pokok History -->
        <div class="row">
            <div class="col-12">
                <h4 class="section-title">
                    <i class="fas fa-history me-2"></i>Riwayat Perubahan Modal Pokok
                </h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Modal Pokok Lama</th>
                                <th>Modal Pokok Baru</th>
                                <th>% Perubahan</th>
                                <th>Sumber Perubahan</th>
                                <th>Alasan</th>
                                <th>Diubah Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($modalPokokHistory)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">Belum ada perubahan modal pokok</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($modalPokokHistory as $history): ?>
                                <tr>
                                    <td><?php echo date('d M Y', strtotime($history['tanggal_efektif'])); ?></td>
                                    <td>Rp <?php echo number_format($history['modal_pokok_lama'], 0, ',', '.'); ?></td>
                                    <td>Rp <?php echo number_format($history['modal_pokok_baru'], 0, ',', '.'); ?></td>
                                    <td>
                                        <span class="change-<?php echo ($history['persentase_perubahan'] >= 0 ? 'positive' : 'negative'); ?>">
                                            <?php echo number_format($history['persentase_perubahan'], 2); ?>%
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($history['perubahan_sumber']); ?></td>
                                    <td><?php echo htmlspecialchars($history['alasan_perubahan']); ?></td>
                                    <td><?php echo htmlspecialchars($history['user_name']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create RAT Modal -->
    <div class="modal fade" id="createRATModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Buat Sesi RAT Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createRATForm">
                        <div class="mb-3">
                            <label for="tahun" class="form-label">Tahun RAT</label>
                            <input type="number" class="form-control" id="tahun" name="tahun" required min="2020" max="2030" tabindex="1">
                            <div class="form-text">Tahun pelaksanaan RAT</div>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_rapat" class="form-label">Tanggal Rapat</label>
                            <input type="date" class="form-control" id="tanggal_rapat" name="tanggal_rapat" required tabindex="2">
                        </div>
                        <div class="mb-3">
                            <label for="tempat" class="form-label">Tempat</label>
                            <input type="text" class="form-control" id="tempat" name="tempat" required tabindex="3">
                        </div>
                        <div class="mb-3">
                            <label for="agenda" class="form-label">Agenda</label>
                            <textarea class="form-control" id="agenda" name="agenda" rows="3" required tabindex="4"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="createRATSession()">Buat Sesi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Modal Pokok Modal -->
    <div class="modal fade" id="updateModalPokokModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Modal Pokok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="updateModalPokokForm">
                        <div class="mb-3">
                            <label for="modal_pokok_baru" class="form-label">Modal Pokok Baru</label>
                            <input type="text" class="form-control" id="modal_pokok_baru" name="modal_pokok_baru" required tabindex="5">
                            <div class="form-text">Format: 10000000 (tanpa format)</div>
                        </div>
                        <div class="mb-3">
                            <label for="alasan_perubahan" class="form-label">Alasan Perubahan</label>
                            <textarea class="form-control" id="alasan_perubahan" name="alasan_perubahan" rows="3" required tabindex="6"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="updateModalPokokManual()">Update Modal Pokok</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../src/public/js/form-helper.js"></script>
    <script src="../src/public/js/avoid-next-error.js"></script>
    <script>
        const cooperativeId = <?php echo $_SESSION['cooperative_id']; ?>;
        
        document.addEventListener('DOMContentLoaded', function() {
            // Setup ENTER key navigation for modal forms
            FormHelper.setupEnterKeyNavigation('createRATForm');
            FormHelper.setupEnterKeyNavigation('updateModalPokokForm');
        });
        
        // Show create RAT modal
        function showCreateRATModal() {
            const modal = new bootstrap.Modal(document.getElementById('createRATModal'));
            modal.show();
            
            // Reset form and set default values
            FormHelper.resetFormFields('createRATForm');
            document.getElementById('tahun').value = new Date().getFullYear();
        }
        
        // Show update modal pokok modal
        function showUpdateModalPokokModal() {
            const modal = new bootstrap.Modal(document.getElementById('updateModalPokokModal'));
            modal.show();
            
            // Reset form and set current modal pokok
            FormHelper.resetFormFields('updateModalPokokForm');
            const currentModalPokok = <?php echo $coopData['modal_pokok']; ?>;
            document.getElementById('modal_pokok_baru').value = currentModalPokok;
        }
        
        // Create RAT session
        async function createRATSession() {
            const form = document.getElementById('createRATForm');
            const formData = new FormData(form);
            
            try {
                const response = await fetch('../src/public/api/rat.php?action=create_rat_session&id=' + cooperativeId, {
                    method: 'POST',
                    body: JSON.stringify(Object.fromEntries(formData))
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Sesi RAT berhasil dibuat!');
                    bootstrap.Modal.getInstance(document.getElementById('createRATModal')).hide();
                    refreshData();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }
        
        // Update modal pokok manual
        async function updateModalPokokManual() {
            const form = document.getElementById('updateModalPokokForm');
            const formData = new FormData(form);
            
            // Clean modal pokok value
            const modalPokokBaru = formData.get('modal_pokok_baru').replace(/[^0-9]/g, '');
            
            try {
                const response = await fetch('../src/public/api/rat.php?action=update_modal_pokok_manual&id=' + cooperativeId, {
                    method: 'POST',
                    body: JSON.stringify({
                        modal_pokok_baru: parseFloat(modalPokokBaru),
                        alasan_perubahan: formData.get('alasan_perubahan')
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Modal pokok berhasil diperbarui!');
                    bootstrap.Modal.getInstance(document.getElementById('updateModalPokokModal')).hide();
                    refreshData();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }
        
        // Update modal pokok from RAT
        async function updateModalPokokFromRAT(sessionId) {
            if (!confirm('Apakah Anda yakin ingin memperbarui modal pokok berdasarkan hasil RAT ini?')) {
                try {
                    const response = await fetch('../src/public/api/rat.php?action=update_modal_pokok_rat&id=' + cooperativeId, {
                        method: 'POST',
                        body: JSON.stringify({
                            tahun: document.getElementById('tahun_' + sessionId).value,
                            modal_pokok_baru: parseFloat(document.getElementById('modal_pokok_' + sessionId).value.replace(/[^0-9]/g, '')),
                            alasan: 'Perubahan modal pokok dari hasil RAT'
                        })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Modal pokok berhasil diperbarui dari hasil RAT!');
                        refreshData();
                    } else {
                        alert('Error: ' + result.message);
                    }
                } catch (error) {
                    alert('Error: ' + error.message);
                }
            }
        }
        
        // Start RAT session
        async function startRATSession(sessionId) {
            if (!confirm('Apakah Anda yakin ingin memulai sesi RAT ini?')) {
                // Update status to in_progress
                try {
                    const response = await fetch('../src/public/api/rat.php?action=update_status&id=' + cooperativeId, {
                        method: 'POST',
                        body: JSON.stringify({
                            session_id: sessionId,
                            status: 'in_progress'
                        })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Sesi RAT dimulai!');
                        refreshData();
                    } else {
                        alert('Error: ' + result.message);
                    }
                } catch (error) {
                    alert('Error: ' + error.message);
                }
            }
        }
        
        // Complete RAT session
        async function completeRATSession(sessionId) {
            // Get modal pokok from form
            const modalPokokInput = document.getElementById('modal_pokok_' + sessionId);
            const modalPokokBaru = parseFloat(modalPokokInput.value.replace(/[^0-9]/g, ''));
            
            if (!confirm('Apakah Anda yakin ingin menyelesaikan modal pokok menjadi Rp ' + formatCurrency(modalPokokBaru) + '?')) {
                return;
            }
            
            try {
                const response = await fetch('../src/public/api/rat.php?action=update_modal_pokok_rat&id=' + cooperativeId, {
                    method: 'POST',
                    body: JSON.stringify({
                        tahun: document.getElementById('tahun_' + sessionId).value,
                        modal_pokok_baru: modalPokokBaru,
                        alasan: 'Penyelesaian modal pokok dari hasil RAT'
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Modal pokok berhasil diperbarui dari hasil RAT!');
                    refreshData();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }
        
        // View RAT details
        function viewRATDetails(sessionId) {
            // Implement view details modal
            alert('Fitur detail RAT akan segera hadir!');
        }
        
        // Refresh data
        function refreshData() {
            location.reload();
        }
        
        // Format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(amount);
        }
        
        // Auto-refresh every 30 seconds
        setInterval(refreshData, 30000);
    </script>
</body>
</html>
