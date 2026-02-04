<?php
require_once __DIR__ . '/../../bootstrap.php';

$auth = new Auth();
if (!$auth->isLoggedIn() || !$auth->hasPermission('view_accounts')) {
    header('Location: /ksp_peb/login.php');
    exit;
}

$app = App::getInstance();
$cooperativeId = $_SESSION['cooperative_id'] ?? null;
if (!$cooperativeId) {
    header('Location: /ksp_peb/dashboard.php');
    exit;
}

$cooperative = new Cooperative();
$coopData = $cooperative->getCooperative($cooperativeId);

$pageTitle   = 'Akuntansi & Keuangan';
$activeRoute = '/ksp_peb/dashboard/akuntansi.php';

ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Akuntansi & Keuangan</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-primary" onclick="showAddJournalModal()">
            <i class="bi bi-plus-circle me-1"></i> Tambah Jurnal
        </button>
    </div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" id="accountingTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="coa-tab" data-bs-toggle="tab" data-bs-target="#coa-tab-pane" type="button" role="tab">
            <i class="bi bi-list-ul me-1"></i> Chart of Accounts
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="journals-tab" data-bs-toggle="tab" data-bs-target="#journals-tab-pane" type="button" role="tab">
            <i class="bi bi-journal me-1"></i> Jurnal Umum
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="ledger-tab" data-bs-toggle="tab" data-bs-target="#ledger-tab-pane" type="button" role="tab">
            <i class="bi bi-book me-1"></i> Buku Besar
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="trial-balance-tab" data-bs-toggle="tab" data-bs-target="#trial-balance-tab-pane" type="button" role="tab">
            <i class="bi bi-balance-scale me-1"></i> Neraca Saldo
        </button>
    </li>
</ul>

<div class="tab-content" id="accountingTabsContent">
    <!-- COA -->
    <div class="tab-pane fade show active" id="coa-tab-pane" role="tabpanel">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Chart of Accounts</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Akun</th>
                                <th>Tipe</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="coaTable">
                            <tr><td>1000</td><td>Kas</td><td><span class="badge bg-primary">Asset</span></td><td><span class="badge bg-success">Aktif</span></td><td>-</td></tr>
                            <tr><td>1100</td><td>Bank</td><td><span class="badge bg-primary">Asset</span></td><td><span class="badge bg-success">Aktif</span></td><td>-</td></tr>
                            <tr><td>2000</td><td>Simpanan Anggota</td><td><span class="badge bg-warning">Liability</span></td><td><span class="badge bg-success">Aktif</span></td><td>-</td></tr>
                            <tr><td>2100</td><td>Pinjaman Anggota</td><td><span class="badge bg-primary">Asset</span></td><td><span class="badge bg-success">Aktif</span></td><td>-</td></tr>
                            <tr><td>3000</td><td>Modal</td><td><span class="badge bg-info">Equity</span></td><td><span class="badge bg-success">Aktif</span></td><td>-</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Journals -->
    <div class="tab-pane fade" id="journals-tab-pane" role="tabpanel">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Jurnal Umum</h5>
                <button type="button" class="btn btn-sm btn-primary" onclick="showAddJournalModal()">
                    <i class="bi bi-plus me-1"></i> Tambah Jurnal
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead><tr><th>Tanggal</th><th>Deskripsi</th><th>Debit</th><th>Kredit</th><th>Aksi</th></tr></thead>
                        <tbody id="journalsTable">
                            <tr><td colspan="5" class="text-center">Belum ada data jurnal</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Ledger -->
    <div class="tab-pane fade" id="ledger-tab-pane" role="tabpanel">
        <div class="row mb-3">
            <div class="col-md-4">
                <select class="form-select" id="ledgerAccount">
                    <option value="">Pilih Akun</option>
                    <option value="1000">1000 - Kas</option>
                    <option value="1100">1100 - Bank</option>
                    <option value="2000">2000 - Simpanan Anggota</option>
                    <option value="2100">2100 - Pinjaman Anggota</option>
                    <option value="3000">3000 - Modal</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-primary" onclick="loadLedger()">
                    <i class="bi bi-search me-1"></i> Lihat
                </button>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Buku Besar</h5></div>
            <div class="card-body">
                <div id="ledgerContent" class="text-center text-muted">
                    <i class="bi bi-book fs-1"></i><p>Pilih akun untuk melihat buku besar</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Trial Balance -->
    <div class="tab-pane fade" id="trial-balance-tab-pane" role="tabpanel">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Neraca Saldo</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportTrialBalance()">
                    <i class="bi bi-download me-1"></i> Export
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead><tr><th>Kode Akun</th><th>Nama Akun</th><th>Debit</th><th>Kredit</th><th>Saldo</th></tr></thead>
                        <tbody id="trialBalanceTable">
                            <tr><td>1000</td><td>Kas</td><td>Rp 10,000,000</td><td>-</td><td>Rp 10,000,000</td></tr>
                            <tr><td>1100</td><td>Bank</td><td>Rp 25,000,000</td><td>-</td><td>Rp 25,000,000</td></tr>
                            <tr><td>2000</td><td>Simpanan Anggota</td><td>-</td><td>Rp 30,000,000</td><td>Rp 30,000,000</td></tr>
                            <tr><td>2100</td><td>Pinjaman Anggota</td><td>Rp 5,000,000</td><td>-</td><td>Rp 5,000,000</td></tr>
                            <tr class="table-info fw-bold"><td colspan="4">TOTAL</td><td>Rp 10,000,000</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Journal Modal -->
<div class="modal fade" id="addJournalModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Jurnal Umum</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addJournalForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal *</label>
                                <input type="date" class="form-control" name="entry_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nomor Referensi</label>
                                <input type="text" class="form-control" name="reference_number">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi *</label>
                        <textarea class="form-control" name="description" rows="2" required></textarea>
                    </div>

                    <h6>Detail Jurnal</h6>
                    <div id="journalEntries">
                        <div class="row mb-2 journal-entry">
                            <div class="col-md-4">
                                <select class="form-select account-select" name="accounts[]" required>
                                    <option value="">Pilih Akun</option>
                                    <option value="1000">1000 - Kas</option>
                                    <option value="1100">1100 - Bank</option>
                                    <option value="2000">2000 - Simpanan Anggota</option>
                                    <option value="2100">2100 - Pinjaman Anggota</option>
                                    <option value="3000">3000 - Modal</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control debit-input" name="debits[]" placeholder="Debit" step="0.01">
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control credit-input" name="credits[]" placeholder="Kredit" step="0.01">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-entry">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addJournalEntry()">
                        <i class="bi bi-plus me-1"></i> Tambah Baris
                    </button>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <strong>Total Debit:</strong> <span id="totalDebit">Rp 0</span><br>
                                <strong>Total Kredit:</strong> <span id="totalCredit">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveJournal()">Simpan Jurnal</button>
            </div>
        </div>
    </div>
</div>

<script>
async function loadJournals() {
    try {
        const resp = await fetch('/ksp_peb/src/public/api/akuntansi.php?action=journals&limit=20');
        const data = await resp.json();
        const tbody = document.getElementById('journalsTable');
        if (data.success && data.journals && data.journals.length > 0) {
            tbody.innerHTML = data.journals.map(journal => `
                <tr>
                    <td>${journal.entry_date ? new Date(journal.entry_date).toLocaleDateString('id-ID') : '-'}</td>
                    <td>${journal.description ?? '-'}</td>
                    <td>Rp ${journal.total_debit?.toLocaleString('id-ID') ?? 0}</td>
                    <td>Rp ${journal.total_credit?.toLocaleString('id-ID') ?? 0}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="viewJournal(${journal.id})">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        } else {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center">Belum ada data jurnal</td></tr>';
        }
    } catch (e) {
        document.getElementById('journalsTable').innerHTML = '<tr><td colspan="5" class="text-danger text-center">Error memuat data</td></tr>';
    }
}

function loadLedger() {
    const accountId = document.getElementById('ledgerAccount').value;
    if (!accountId) {
        document.getElementById('ledgerContent').innerHTML = '<div class="text-center text-muted"><i class="bi bi-book fs-1"></i><p>Pilih akun untuk melihat buku besar</p></div>';
        return;
    }
    document.getElementById('ledgerContent').innerHTML = `
        <div class="table-responsive">
            <table class="table table-striped">
                <thead><tr><th>Tanggal</th><th>Deskripsi</th><th>Debit</th><th>Kredit</th><th>Saldo</th></tr></thead>
                <tbody>
                    <tr><td>01/01/2024</td><td>Saldo Awal</td><td>Rp 10,000,000</td><td>-</td><td>Rp 10,000,000</td></tr>
                    <tr><td>15/01/2024</td><td>Setoran Simpanan</td><td>Rp 2,000,000</td><td>-</td><td>Rp 12,000,000</td></tr>
                </tbody>
            </table>
        </div>`;
}

function showAddJournalModal() {
    new bootstrap.Modal(document.getElementById('addJournalModal')).show();
}

function addJournalEntry() {
    const entriesContainer = document.getElementById('journalEntries');
    const newEntry = document.createElement('div');
    newEntry.className = 'row mb-2 journal-entry';
    newEntry.innerHTML = `
        <div class="col-md-4">
            <select class="form-select account-select" name="accounts[]" required>
                <option value="">Pilih Akun</option>
                <option value="1000">1000 - Kas</option>
                <option value="1100">1100 - Bank</option>
                <option value="2000">2000 - Simpanan Anggota</option>
                <option value="2100">2100 - Pinjaman Anggota</option>
                <option value="3000">3000 - Modal</option>
            </select>
        </div>
        <div class="col-md-3"><input type="number" class="form-control debit-input" name="debits[]" placeholder="Debit" step="0.01"></div>
        <div class="col-md-3"><input type="number" class="form-control credit-input" name="credits[]" placeholder="Kredit" step="0.01"></div>
        <div class="col-md-2">
            <button type="button" class="btn btn-outline-danger btn-sm remove-entry"><i class="bi bi-trash"></i></button>
        </div>`;
    entriesContainer.appendChild(newEntry);
    attachEventListeners();
}

function attachEventListeners() {
    document.querySelectorAll('.debit-input, .credit-input').forEach(input => {
        input.addEventListener('input', calculateTotals);
    });
    document.querySelectorAll('.remove-entry').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.journal-entry').remove();
            calculateTotals();
        });
    });
}

function calculateTotals() {
    let totalDebit = 0;
    let totalCredit = 0;
    document.querySelectorAll('.debit-input').forEach(input => { totalDebit += parseFloat(input.value) || 0; });
    document.querySelectorAll('.credit-input').forEach(input => { totalCredit += parseFloat(input.value) || 0; });
    document.getElementById('totalDebit').textContent = 'Rp ' + totalDebit.toLocaleString('id-ID');
    document.getElementById('totalCredit').textContent = 'Rp ' + totalCredit.toLocaleString('id-ID');
}

async function saveJournal() {
    const form = document.getElementById('addJournalForm');
    const formData = new FormData(form);
    try {
        const resp = await fetch('/ksp_peb/src/public/api/akuntansi.php?action=create_journal', { method: 'POST', body: formData });
        const data = await resp.json();
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('addJournalModal')).hide();
            form.reset();
            loadJournals();
            alert('Jurnal berhasil disimpan');
        } else {
            alert(data.message || 'Gagal menyimpan jurnal');
        }
    } catch (e) {
        alert('Terjadi kesalahan');
    }
}

function exportTrialBalance() {
    alert('Fitur export akan diimplementasikan');
}

document.addEventListener('DOMContentLoaded', function() {
    loadJournals();
    attachEventListeners();
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
