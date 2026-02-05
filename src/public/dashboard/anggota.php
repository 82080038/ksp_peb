<?php
// Enhanced Anggota Management with Full jQuery + Bootstrap Integration
require_once __DIR__ . '/../../../bootstrap.php';

$auth = new Auth();
if (!$auth->isLoggedIn() || (!$auth->hasPermission('view_members') && !$auth->hasPermission('admin_access'))) {
    header('Location: /ksp_peb/login.php');
    exit;
}

$app = App::getInstance();
$coopDB = $app->getCoopDB();

// Get all members
try {
    $stmt = $coopDB->prepare("
        SELECT a.*, u.nama, u.email, u.phone 
        FROM anggota a 
        LEFT JOIN people_db.users u ON a.user_id = u.id 
        ORDER BY a.created_at DESC
    ");
    $stmt->execute();
    $members = $stmt->fetchAll();
} catch (Exception $e) {
    $members = [];
}

// Generate unique IDs
$uniqueId = uniqid('anggota_');
?>

<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Manajemen Anggota</h2>
                <p class="text-muted mb-0">Kelola data anggota koperasi</p>
            </div>
            <div>
                <button class="btn btn-enhanced btn-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                    <i class="bi bi-person-plus me-2"></i>Tambah Anggota
                </button>
                <button class="btn btn-enhanced btn-outline-secondary" onclick="exportMembers()">
                    <i class="bi bi-download me-2"></i>Export
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="input-group">
            <input type="text" class="form-control" id="searchInput_<?php echo $uniqueId; ?>" placeholder="Cari anggota...">
            <button class="btn btn-outline-secondary" type="button" onclick="searchMembers()">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </div>
    <div class="col-md-3">
        <select class="form-select" id="statusFilter_<?php echo $uniqueId; ?>" onchange="filterMembers()">
            <option value="">Semua Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="blacklist">Blacklist</option>
        </select>
    </div>
    <div class="col-md-3">
        <select class="form-select" id="sortBy_<?php echo $uniqueId; ?>" onchange="sortMembers()">
            <option value="created_at">Tanggal Daftar</option>
            <option value="nama">Nama</option>
            <option value="email">Email</option>
        </select>
    </div>
    <div class="col-md-2">
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-funnel"></i>
            </span>
            <select class="form-select" id="perPage_<?php echo $uniqueId; ?>" onchange="changePerPage()">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Total Anggota</div>
                        <div class="stat-value" id="totalMembers_<?php echo $uniqueId; ?>"><?php echo count($members); ?></div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Active</div>
                        <div class="stat-value" id="activeMembers_<?php echo $uniqueId; ?>">
                            <?php echo count(array_filter($members, function($m) { return $m['status'] === 'active'; })); ?>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Inactive</div>
                        <div class="stat-value" id="inactiveMembers_<?php echo $uniqueId; ?>">
                            <?php echo count(array_filter($members, function($m) { return $m['status'] === 'inactive'; })); ?>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-pause-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Blacklist</div>
                        <div class="stat-value" id="blacklistMembers_<?php echo $uniqueId; ?>">
                            <?php echo count(array_filter($members, function($m) { return $m['status'] === 'blacklist'; })); ?>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Members Table -->
<div class="table-enhanced">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Daftar Anggota</h6>
        <div class="btn-group btn-group-sm">
            <button class="btn btn-outline-secondary" onclick="refreshTable()">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
            <button class="btn btn-outline-primary" onclick="toggleBulkActions()">
                <i class="bi bi-check2-square"></i>
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0" id="membersTable_<?php echo $uniqueId; ?>" data-enhanced>
            <thead>
                <tr>
                    <th width="50">
                        <input type="checkbox" class="form-check-input bulk-select-all" id="selectAll_<?php echo $uniqueId; ?>">
                    </th>
                    <th>No</th>
                    <th data-sort="nik">NIK</th>
                    <th data-sort="nama">Nama</th>
                    <th data-sort="email">Email</th>
                    <th data-sort="phone">Phone</th>
                    <th data-sort="status">Status</th>
                    <th data-sort="created_at">Tanggal Daftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="membersTableBody_<?php echo $uniqueId; ?>">
                <?php if (!empty($members)): ?>
                    <?php $no = 1; foreach ($members as $member): ?>
                    <tr data-id="<?php echo $member['id']; ?>">
                        <td>
                            <input type="checkbox" class="form-check-input bulk-select" value="<?php echo $member['id']; ?>">
                        </td>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($member['nik'] ?? '-'); ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($member['nama'] ?? '-'); ?></strong>
                        </td>
                        <td><?php echo htmlspecialchars($member['email'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($member['phone'] ?? '-'); ?></td>
                        <td>
                            <span class="badge bg-<?php echo getStatusColor($member['status']); ?>">
                                <?php echo htmlspecialchars($member['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($member['created_at'])); ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="editMember(<?php echo $member['id']; ?>)" data-bs-toggle="tooltip" title="Ubah">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-outline-info" onclick="viewMember(<?php echo $member['id']; ?>)" data-bs-toggle="tooltip" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-outline-warning" onclick="toggleStatus(<?php echo $member['id']; ?>, '<?php echo $member['status']; ?>')" data-bs-toggle="tooltip" title="Ubah Status">
                                    <i class="bi bi-toggle-on"></i>
                                </button>
                                <button class="btn btn-outline-danger" onclick="deleteMember(<?php echo $member['id']; ?>)" data-bs-toggle="tooltip" title="Hapus" data-confirm="Apakah Anda yakin ingin menghapus anggota ini?">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted">Tidak ada data anggota</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bulk Actions (hidden by default) -->
<div class="row mt-3" id="bulkActions_<?php echo $uniqueId; ?>" style="display: none;">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span id="selectedCount_<?php echo $uniqueId; ?>">0</span> anggota dipilih
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary" onclick="bulkEdit()">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-outline-warning" onclick="bulkToggleStatus()">
                            <i class="bi bi-toggle-on me-1"></i> Toggle Status
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="bulkDelete()" data-confirm="Apakah Anda yakin ingin menghapus anggota yang dipilih?">
                            <i class="bi bi-trash me-1"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Anggota Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addMemberForm_<?php echo $uniqueId; ?>" class="form-enhanced" data-validate data-ajax>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add_nama_<?php echo $uniqueId; ?>" class="form-label">Nama Lengkap *</label>
                                <input type="text" class="form-control" id="add_nama_<?php echo $uniqueId; ?>" name="nama" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add_nik_<?php echo $uniqueId; ?>" class="form-label">NIK *</label>
                                <input type="text" class="form-control" id="add_nik_<?php echo $uniqueId; ?>" name="nik" required maxlength="16">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add_email_<?php echo $uniqueId; ?>" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="add_email_<?php echo $uniqueId; ?>" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add_phone_<?php echo $uniqueId; ?>" class="form-label">Phone *</label>
                                <input type="tel" class="form-control phone" id="add_phone_<?php echo $uniqueId; ?>" name="phone" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add_alamat_<?php echo $uniqueId; ?>" class="form-label">Alamat</label>
                                <textarea class="form-control auto-resize" id="add_alamat_<?php echo $uniqueId; ?>" name="alamat" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add_tempat_lahir_<?php echo $uniqueId; ?>" class="form-label">Tempat Lahir</label>
                                <input type="text" class="form-control" id="add_tempat_lahir_<?php echo $uniqueId; ?>" name="tempat_lahir">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add_tanggal_lahir_<?php echo $uniqueId; ?>" class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="add_tanggal_lahir_<?php echo $uniqueId; ?>" name="tanggal_lahir">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add_jenis_kelamin_<?php echo $uniqueId; ?>" class="form-label">Jenis Kelamin</label>
                                <select class="form-select" id="add_jenis_kelamin_<?php echo $uniqueId; ?>" name="jenis_kelamin">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add_institution_type_<?php echo $uniqueId; ?>" class="form-label">Jenis Instansi *</label>
                                <select class="form-select" id="add_institution_type_<?php echo $uniqueId; ?>" name="institution_type_id" required onchange="loadOccupations_<?php echo $uniqueId; ?>()">
                                    <option value="">Memuat...</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add_occupation_<?php echo $uniqueId; ?>" class="form-label">Pekerjaan *</label>
                                <select class="form-select" id="add_occupation_<?php echo $uniqueId; ?>" name="occupation_id" required onchange="loadRankGroups_<?php echo $uniqueId; ?>()" disabled>
                                    <option value="">Pilih Pekerjaan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="rankFields_<?php echo $uniqueId; ?>" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add_rank_group_<?php echo $uniqueId; ?>" class="form-label">Golongan Pangkat</label>
                                    <select class="form-select" id="add_rank_group_<?php echo $uniqueId; ?>" name="rank_group_id" onchange="loadRanks_<?php echo $uniqueId; ?>()" disabled>
                                        <option value="">Pilih Golongan Pangkat</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add_rank_<?php echo $uniqueId; ?>" class="form-label">Pangkat</label>
                                    <select class="form-select" id="add_rank_<?php echo $uniqueId; ?>" name="rank_id" disabled>
                                        <option value="">Pilih Pangkat</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="addMemberForm_<?php echo $uniqueId; ?>" class="btn btn-enhanced btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Anggota
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Member Modal -->
<div class="modal fade" id="editMemberModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Anggota</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editMemberForm_<?php echo $uniqueId; ?>" class="form-enhanced" data-validate data-ajax>
                    <input type="hidden" id="edit_member_id_<?php echo $uniqueId; ?>" name="member_id">
                    <!-- Form fields will be populated by JavaScript -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="editMemberForm_<?php echo $uniqueId; ?>" class="btn btn-enhanced btn-primary">
                    <i class="bi bi-pencil me-2"></i>Update
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Member Modal -->
<div class="modal fade" id="viewMemberModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Anggota</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="viewMemberContent_<?php echo $uniqueId; ?>">
                    <!-- Member details will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<?php
function getStatusColor($status) {
    $colors = [
        'active' => 'success',
        'inactive' => 'warning',
        'blacklist' => 'danger'
    ];
    return $colors[$status] ?? 'secondary';
}
?>

<script>
$(document).ready(function() {
    initializeAnggotaPage();
});

function initializeAnggotaPage() {
    // Initialize table enhancements
    $('#membersTable_<?php echo $uniqueId; ?>').tableEnhanced({
        selectable: true,
        rowClick: function() {
            var memberId = $(this).data('id');
            viewMember(memberId);
        }
    });
    
    // Initialize form validation
    setupFormValidation();
    
    // Initialize bulk selection
    setupBulkSelection();
    
    // Initialize tooltips
    KSP.initializeTooltips();
    
    // Initialize popovers
    KSP.initializePopovers();
    
    // Auto-format inputs
    $('.phone').autoFormat();
    $('.auto-resize').autoResize();

    // Load reference data untuk form tambah anggota
    loadInstitutionTypes_<?php echo $uniqueId; ?>();
}

// ---------- Cascading dropdown Tambah Anggota ----------
function loadInstitutionTypes_<?php echo $uniqueId; ?>() {
    const select = $('#add_institution_type_<?php echo $uniqueId; ?>');
    select.html('<option value="">Memuat...</option>').prop('disabled', true);

    // TODO: ganti data statis ini dengan panggilan API jika endpoint siap
    const institutionTypes = [
        { id: 'PEMERINTAH', name: 'Pemerintah' },
        { id: 'BUMN', name: 'BUMN' },
        { id: 'BUMD', name: 'BUMD' },
        { id: 'SWASTA', name: 'Swasta' },
        { id: 'LAINNYA', name: 'Lainnya' }
    ];

    let options = '<option value="">Pilih Jenis Instansi</option>';
    institutionTypes.forEach((type) => {
        options += `<option value="${type.id}">${type.name}</option>`;
    });

    select.html(options).prop('disabled', false);
}

function loadOccupations_<?php echo $uniqueId; ?>() {
    const institutionTypeId = $('#add_institution_type_<?php echo $uniqueId; ?>').val();
    const occupationSelect = $('#add_occupation_<?php echo $uniqueId; ?>');

    occupationSelect.html('<option value="">Memuat...</option>').prop('disabled', true);
    $('#add_rank_group_<?php echo $uniqueId; ?>').html('<option value="">Pilih Golongan Pangkat</option>').prop('disabled', true);
    $('#add_rank_<?php echo $uniqueId; ?>').html('<option value="">Pilih Pangkat</option>').prop('disabled', true);
    $('#rankFields_<?php echo $uniqueId; ?>').hide();

    if (!institutionTypeId) {
        occupationSelect.html('<option value="">Pilih Pekerjaan</option>').prop('disabled', true);
        return;
    }

    // TODO: ganti data statis ini dengan panggilan API jika endpoint siap
    const occupationsMap = {
        PEMERINTAH: [
            { id: 'PNS', name: 'Pegawai Negeri Sipil', has_rank: true },
            { id: 'TNI', name: 'Tentara Nasional Indonesia', has_rank: true },
            { id: 'POLRI', name: 'Kepolisian Negara RI', has_rank: true }
        ],
        SWASTA: [
            { id: 'KARYAWAN', name: 'Karyawan Swasta', has_rank: false },
            { id: 'WIRASWASTA', name: 'Wiraswasta', has_rank: false }
        ],
        BUMN: [
            { id: 'BUMN_STAF', name: 'Pegawai BUMN', has_rank: false }
        ],
        BUMD: [
            { id: 'BUMD_STAF', name: 'Pegawai BUMD', has_rank: false }
        ],
        LAINNYA: [
            { id: 'LAIN', name: 'Lainnya', has_rank: false }
        ]
    };

    const occupations = occupationsMap[institutionTypeId] || [];
    if (occupations.length === 0) {
        occupationSelect.html('<option value="">Tidak ada data</option>').prop('disabled', true);
        return;
    }

    let options = '<option value="">Pilih Pekerjaan</option>';
    occupations.forEach((occ) => {
        options += `<option value="${occ.id}" data-has-rank="${occ.has_rank ? '1' : '0'}">${occ.name}</option>`;
    });
    occupationSelect.html(options).prop('disabled', false);
}

function loadRankGroups_<?php echo $uniqueId; ?>() {
    const occupationSelect = $('#add_occupation_<?php echo $uniqueId; ?>');
    const occupationId = occupationSelect.val();
    const hasRank = occupationSelect.find('option:selected').data('has-rank') === 1 || occupationSelect.find('option:selected').data('has-rank') === '1' || occupationSelect.find('option:selected').data('has-rank') === true;

    const rankGroupSelect = $('#add_rank_group_<?php echo $uniqueId; ?>');
    const rankSelect = $('#add_rank_<?php echo $uniqueId; ?>');

    rankGroupSelect.html('<option value="">Pilih Golongan Pangkat</option>').prop('disabled', true);
    rankSelect.html('<option value="">Pilih Pangkat</option>').prop('disabled', true);
    $('#rankFields_<?php echo $uniqueId; ?>').hide();

    if (!hasRank) {
        return;
    }

    // TODO: ganti data statis ini dengan panggilan API jika endpoint siap
    const rankGroupsMap = {
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

    const groups = rankGroupsMap[occupationId] || [];
    if (groups.length === 0) {
        rankGroupSelect.html('<option value="">Tidak ada data</option>').prop('disabled', true);
        return;
    }

    let options = '<option value="">Pilih Golongan Pangkat</option>';
    groups.forEach((group) => {
        options += `<option value="${group.id}">${group.name}</option>`;
    });

    rankGroupSelect.html(options).prop('disabled', false);
    $('#rankFields_<?php echo $uniqueId; ?>').show();
}

function loadRanks_<?php echo $uniqueId; ?>() {
    const rankGroupId = $('#add_rank_group_<?php echo $uniqueId; ?>').val();
    const rankSelect = $('#add_rank_<?php echo $uniqueId; ?>');

    rankSelect.html('<option value="">Pilih Pangkat</option>').prop('disabled', true);

    if (!rankGroupId) {
        return;
    }

    // TODO: ganti data statis ini dengan panggilan API jika endpoint siap
    const ranksMap = {
        JURU: [
            { id: 'I_A', name: 'Juru Muda', abbr: 'I/a' },
            { id: 'I_B', name: 'Juru Muda Tingkat I', abbr: 'I/b' },
            { id: 'I_C', name: 'Juru', abbr: 'I/c' },
            { id: 'I_D', name: 'Juru Tingkat I', abbr: 'I/d' }
        ],
        PENGATUR: [
            { id: 'II_A', name: 'Pengatur Muda', abbr: 'II/a' },
            { id: 'II_B', name: 'Pengatur Muda Tingkat I', abbr: 'II/b' },
            { id: 'II_C', name: 'Pengatur', abbr: 'II/c' },
            { id: 'II_D', name: 'Pengatur Tingkat I', abbr: 'II/d' }
        ],
        PENATA: [
            { id: 'III_A', name: 'Penata Muda', abbr: 'III/a' },
            { id: 'III_B', name: 'Penata Muda Tingkat I', abbr: 'III/b' },
            { id: 'III_C', name: 'Penata', abbr: 'III/c' },
            { id: 'III_D', name: 'Penata Tingkat I', abbr: 'III/d' }
        ],
        PEMBINA: [
            { id: 'IV_A', name: 'Pembina', abbr: 'IV/a' },
            { id: 'IV_B', name: 'Pembina Tingkat I', abbr: 'IV/b' },
            { id: 'IV_C', name: 'Pembina Utama Muda', abbr: 'IV/c' },
            { id: 'IV_D', name: 'Pembina Utama Madya', abbr: 'IV/d' },
            { id: 'IV_E', name: 'Pembina Utama', abbr: 'IV/e' }
        ],
        TAMTAMA: [
            { id: 'T1', name: 'Tamtama Muda', abbr: '-' },
            { id: 'T2', name: 'Tamtama', abbr: '-' }
        ],
        BINTARA: [
            { id: 'B1', name: 'Bintara Muda', abbr: '-' },
            { id: 'B2', name: 'Bintara', abbr: '-' }
        ],
        PERWIRA: [
            { id: 'P1', name: 'Perwira Muda', abbr: '-' },
            { id: 'P2', name: 'Perwira', abbr: '-' },
            { id: 'P3', name: 'Perwira Tinggi', abbr: '-' }
        ]
    };

    const ranks = ranksMap[rankGroupId] || [];
    if (ranks.length === 0) {
        rankSelect.html('<option value="">Tidak ada data</option>').prop('disabled', true);
        return;
    }

    let options = '<option value="">Pilih Pangkat</option>';
    ranks.forEach((rank) => {
        options += `<option value="${rank.id}">${rank.name}${rank.abbr ? ' (' + rank.abbr + ')' : ''}</option>`;
    });

    rankSelect.html(options).prop('disabled', false);
}

function setupFormValidation() {
    var validationRules = {
        nama: {
            required: true,
            minLength: 3,
            message: 'Nama minimal 3 karakter'
        },
        nik: {
            required: true,
            minLength: 16,
            maxLength: 16,
            message: 'NIK harus 16 digit'
        },
        email: {
            required: true,
            email: true,
            message: 'Format email tidak valid'
        },
        phone: {
            required: true,
            phone: true,
            message: 'Format phone tidak valid'
        }
    };
    
    $('#addMemberForm_<?php echo $uniqueId; ?>').data('validate-rules', validationRules);
    $('#editMemberForm_<?php echo $uniqueId; ?>').data('validate-rules', validationRules);
}

function setupBulkSelection() {
    // Select all checkbox
    $('#selectAll_<?php echo $uniqueId; ?>').on('change', function() {
        var isChecked = $(this).prop('checked');
        $('.bulk-select').prop('checked', isChecked);
        updateBulkActions();
    });
    
    // Individual checkboxes
    $('.bulk-select').on('change', function() {
        updateBulkActions();
    });
}

function updateBulkActions() {
    var selectedCount = $('.bulk-select:checked').length;
    var $bulkActions = $('#bulkActions_<?php echo $uniqueId; ?>');
    
    if (selectedCount > 0) {
        $bulkActions.show();
        $('#selectedCount_<?php echo $uniqueId; ?>').text(selectedCount);
    } else {
        $bulkActions.hide();
    }
}

function toggleBulkActions() {
    var $bulkActions = $('#bulkActions_<?php echo $uniqueId; ?>');
    var $selectAll = $('#selectAll_<?php echo $uniqueId; ?>');
    
    if ($bulkActions.is(':visible')) {
        $bulkActions.hide();
        $('.bulk-select').prop('checked', false);
        $selectAll.prop('checked', false);
    } else {
        $bulkActions.show();
    }
}

function editMember(memberId) {
    $.ajax({
        url: 'src/public/api/anggota.php?action=get&id=' + memberId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                populateEditForm(response.member);
                $('#editMemberModal').modal('show');
            } else {
                KSP.showNotification(response.message || 'Gagal memuat data anggota', 'danger');
            }
        },
        error: function() {
            KSP.showNotification('Terjadi kesalahan. Silakan coba lagi.', 'danger');
        }
    });
}

function populateEditForm(member) {
    var form = $('#editMemberForm_<?php echo $uniqueId; ?>');
    
    // Clear and populate form
    form.empty().html(`
        <input type="hidden" name="member_id" value="${member.id}">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" class="form-control" name="nama" value="${member.nama || ''}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">NIK *</label>
                    <input type="text" class="form-control" name="nik" value="${member.nik || ''}" required maxlength="16">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Email *</label>
                    <input type="email" class="form-control" name="email" value="${member.email || ''}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Phone *</label>
                    <input type="tel" class="form-control phone" name="phone" value="${member.phone || ''}" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea class="form-control auto-resize" name="alamat" rows="3">${member.alamat || ''}</textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text" class="form-control" name="tempat_lahir" value="${member.tempat_lahir || ''}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" class="form-control" name="tanggal_lahir" value="${member.tanggal_lahir || ''}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <select class="form-select" name="jenis_kelamin">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L" ${member.jenis_kelamin === 'L' ? 'selected' : ''}>Laki-laki</option>
                        <option value="P" ${member.jenis_kelamin === 'P' ? 'selected' : ''}>Perempuan</option>
                    </select>
                </div>
            </div>
        </div>
    `);
    
    // Re-initialize form validation and auto-format
    setupFormValidation();
    $('.phone').autoFormat();
    $('.auto-resize').autoResize();
}

function viewMember(memberId) {
    $.ajax({
        url: 'src/public/api/anggota.php?action=get&id=' + memberId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayMemberDetails(response.member);
                $('#viewMemberModal').modal('show');
            } else {
                KSP.showNotification(response.message || 'Gagal memuat data anggota', 'danger');
            }
        },
        error: function() {
            KSP.showNotification('Terjadi kesalahan. Silakan coba lagi.', 'danger');
        }
    });
}

function displayMemberDetails(member) {
    var content = `
        <div class="row">
            <div class="col-md-6">
                <h6>Informasi Pribadi</h6>
                <table class="table table-sm">
                    <tr><td>NIK</td><td>${member.nik || '-'}</td></tr>
                    <tr><td>Nama</td><td>${member.nama || '-'}</td></tr>
                    <tr><td>Email</td><td>${member.email || '-'}</td></tr>
                    <tr><td>Phone</td><td>${member.phone || '-'}</td></tr>
                    <tr><td>Jenis Kelamin</td><td>${member.jenis_kelamin || '-'}</td></tr>
                    <tr><td>Tempat Lahir</td><td>${member.tempat_lahir || '-'}</td></tr>
                    <tr><td>Tanggal Lahir</td><td>${member.tanggal_lahir || '-'}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Informasi Keanggotaan</h6>
                <table class="table table-sm">
                    <tr><td>Status</td><td><span class="badge bg-${getStatusColor(member.status)}">${member.status}</span></td></tr>
                    <tr><td>Tanggal Daftar</td><td>${member.created_at ? new Date(member.created_at).toLocaleDateString('id-ID') : '-'}</td></tr>
                    <tr><td>Alamat</td><td>${member.alamat || '-'}</td></tr>
                </table>
            </div>
        </div>
    `;
    
    $('#viewMemberContent_<?php echo $uniqueId; ?>').html(content);
}

function getStatusColor(status) {
    var colors = {
        'active': 'success',
        'inactive': 'warning',
        'blacklist': 'danger'
    };
    return colors[status] || 'secondary';
}

function toggleStatus(memberId, currentStatus) {
    var newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    
    $.ajax({
        url: 'src/public/api/anggota.php?action=update_status',
        type: 'POST',
        data: {
            member_id: memberId,
            status: newStatus
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                KSP.showNotification('Status anggota berhasil diubah', 'success');
                refreshTable();
            } else {
                KSP.showNotification(response.message || 'Gagal mengubah status', 'danger');
            }
        },
        error: function() {
            KSP.showNotification('Terjadi kesalahan. Silakan coba lagi.', 'danger');
        }
    });
}

function deleteMember(memberId) {
    $.ajax({
        url: 'src/public/api/anggota.php?action=delete&id=' + memberId,
        type: 'DELETE',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                KSP.showNotification('Anggota berhasil dihapus', 'success');
                refreshTable();
            } else {
                KSP.showNotification(response.message || 'Gagal menghapus anggota', 'danger');
            }
        },
        error: function() {
            KSP.showNotification('Terjadi kesalahan. Silakan coba lagi.', 'danger');
        }
    });
}

function searchMembers() {
    var searchTerm = $('#searchInput_<?php echo $uniqueId; ?>').val();
    var statusFilter = $('#statusFilter_<?php echo $uniqueId; ?>').val();
    var sortBy = $('#sortBy_<?php echo $uniqueId; ?>').val();
    var perPage = $('#perPage_<?php echo $uniqueId; ?>').val();
    
    loadMembers(1, searchTerm, statusFilter, sortBy, perPage);
}

function filterMembers() {
    searchMembers();
}

function sortMembers() {
    searchMembers();
}

function changePerPage() {
    searchMembers();
}

function loadMembers(page, searchTerm, statusFilter, sortBy, perPage) {
    $.ajax({
        url: 'src/public/api/anggota.php?action=list',
        type: 'GET',
        data: {
            page: page,
            search: searchTerm,
            status: statusFilter,
            sort: sortBy,
            per_page: perPage
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateMembersTable(response.members);
                updateStatistics(response.statistics);
            } else {
                KSP.showNotification(response.message || 'Gagal memuat data', 'danger');
            }
        },
        error: function() {
            KSP.showNotification('Terjadi kesalahan. Silakan coba lagi.', 'danger');
        }
    });
}

function updateMembersTable(members) {
    var tbody = $('#membersTableBody_<?php echo $uniqueId; ?>');
    tbody.empty();
    
    if (members.length === 0) {
        tbody.append('<tr><td colspan="9" class="text-center text-muted">Tidak ada data anggota</td></tr>');
        return;
    }
    
    var no = 1;
    members.forEach(function(member) {
        var row = `
            <tr data-id="${member.id}">
                <td><input type="checkbox" class="form-check-input bulk-select" value="${member.id}"></td>
                <td>${no++}</td>
                <td>${member.nik || '-'}</td>
                <td><strong>${member.nama || '-'}</strong></td>
                <td>${member.email || '-'}</td>
                <td>${member.phone || '-'}</td>
                <td>
                    <span class="badge bg-${getStatusColor(member.status)}">${member.status}</span>
                </td>
                <td>${member.created_at ? new Date(member.created_at).toLocaleDateString('id-ID') : '-'}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="editMember(${member.id})" data-bs-toggle="tooltip" title="Ubah">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-info" onclick="viewMember(${member.id})" data-bs-toggle="tooltip" title="Lihat">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-outline-warning" onclick="toggleStatus(${member.id}, '${member.status}')" data-bs-toggle="tooltip" title="Ubah Status">
                            <i class="bi bi-toggle-on"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="deleteMember(${member.id})" data-bs-toggle="tooltip" title="Hapus" data-confirm="Apakah Anda yakin ingin menghapus anggota ini?">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
    
    // Re-initialize tooltips
    KSP.initializeTooltips();
}

function updateStatistics(statistics) {
    $('#totalMembers_<?php echo $uniqueId; ?>').text(statistics.total || 0);
    $('#activeMembers_<?php echo $uniqueId; ?>').text(statistics.active || 0);
    $('#inactiveMembers_<?php echo $uniqueId; ?>').text(statistics.inactive || 0);
    $('#blacklistMembers_<?php echo $uniqueId; ?>').text(statistics.blacklist || 0);
}

function refreshTable() {
    var currentPage = 1;
    var searchTerm = $('#searchInput_<?php echo $uniqueId; ?>').val();
    var statusFilter = $('#statusFilter_<?php echo $uniqueId; ?>').val();
    var sortBy = $('#sortBy_<?php echo $uniqueId; ?>').val();
    var perPage = $('#perPage_<?php echo $uniqueId; ?>').val();
    
    loadMembers(currentPage, searchTerm, statusFilter, sortBy, perPage);
}

function exportMembers() {
    var searchTerm = $('#searchInput_<?php echo $uniqueId; ?>').val();
    var statusFilter = $('#statusFilter_<?php echo $uniqueId; ?>').val();
    
    window.open('src/public/api/anggota.php?action=export&search=' + searchTerm + '&status=' + statusFilter, '_blank');
}

function bulkEdit() {
    var selectedIds = $('.bulk-select:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selectedIds.length === 0) {
        KSP.showNotification('Pilih anggota terlebih dahulu', 'warning');
        return;
    }
    
    // Implement bulk edit logic
    KSP.showNotification('Fitur edit massal segera hadir', 'info');
}

function bulkToggleStatus() {
    var selectedIds = $('.bulk-select:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selectedIds.length === 0) {
        KSP.showNotification('Pilih anggota terlebih dahulu', 'warning');
        return;
    }
    
    // Implement bulk toggle status logic
    KSP.showNotification('Fitur toggle status massal segera hadir', 'info');
}

function bulkDelete() {
    var selectedIds = $('.bulk-select:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selectedIds.length === 0) {
        KSP.showNotification('Pilih anggota terlebih dahulu', 'warning');
        return;
    }
    
    // Implement bulk delete logic
    KSP.showNotification('Fitur hapus massal segera hadir', 'info');
}
</script>
