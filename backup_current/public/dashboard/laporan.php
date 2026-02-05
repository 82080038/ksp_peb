<?php
require_once __DIR__ . '/../../bootstrap.php';

$auth = new Auth();
if (!$auth->isLoggedIn() || !$auth->hasPermission('view_reports')) {
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

$pageTitle   = 'Laporan Keuangan';
$activeRoute = '/ksp_peb/dashboard/laporan.php';

ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Laporan Keuangan</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportReport()">
                <i class="bi bi-download me-1"></i> Export PDF
            </button>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-primary" onclick="generateReport()">
                <i class="bi bi-arrow-clockwise me-1"></i> Generate
            </button>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-md-3">
        <label class="form-label">Jenis Laporan</label>
        <select class="form-select" id="reportType">
            <option value="neraca">Neraca</option>
            <option value="laba_rugi">Laba Rugi</option>
            <option value="arus_kas">Arus Kas</option>
            <option value="anggota">Laporan Anggota</option>
            <option value="simpanan">Laporan Simpanan</option>
            <option value="pinjaman">Laporan Pinjaman</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Periode</label>
        <select class="form-select" id="reportPeriod">
            <option value="monthly">Bulanan</option>
            <option value="quarterly">Triwulan</option>
            <option value="yearly">Tahunan</option>
            <option value="custom">Custom</option>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">Tahun</label>
        <select class="form-select" id="reportYear">
            <option value="2024">2024</option>
            <option value="2025" selected>2025</option>
            <option value="2026">2026</option>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">Bulan</label>
        <select class="form-select" id="reportMonth">
            <option value="01">Januari</option>
            <option value="02">Februari</option>
            <option value="03">Maret</option>
            <option value="04">April</option>
            <option value="05">Mei</option>
            <option value="06">Juni</option>
            <option value="07">Juli</option>
            <option value="08">Agustus</option>
            <option value="09">September</option>
            <option value="10">Oktober</option>
            <option value="11">November</option>
            <option value="12">Desember</option>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">&nbsp;</label>
        <button class="btn btn-outline-primary w-100" onclick="loadReport()">
            <i class="bi bi-search me-1"></i> Tampilkan
        </button>
    </div>
</div>

<!-- Report Content -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0" id="reportTitle">Neraca - Periode Februari 2025</h5>
    </div>
    <div class="card-body">
        <!-- Balance Sheet -->
        <div id="balanceSheetReport" class="report-content">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary">AKTIVA</h6>
                    <table class="table table-sm">
                        <tr><td>Kas</td><td class="text-end">Rp 15,000,000</td></tr>
                        <tr><td>Bank</td><td class="text-end">Rp 25,000,000</td></tr>
                        <tr><td>Pinjaman Anggota</td><td class="text-end">Rp 5,000,000</td></tr>
                        <tr class="fw-bold"><td>TOTAL AKTIVA</td><td class="text-end">Rp 45,000,000</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-danger">PASIVA</h6>
                    <table class="table table-sm">
                        <tr><td>Simpanan Anggota</td><td class="text-end">Rp 35,000,000</td></tr>
                        <tr><td>Modal</td><td class="text-end">Rp 10,000,000</td></tr>
                        <tr class="fw-bold"><td>TOTAL PASIVA</td><td class="text-end">Rp 45,000,000</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Income Statement -->
        <div id="incomeStatementReport" class="report-content d-none">
            <h6 class="text-success">PENDAPATAN</h6>
            <table class="table table-sm">
                <tr><td>Pendapatan Bunga Pinjaman</td><td class="text-end">Rp 2,500,000</td></tr>
                <tr class="fw-bold"><td>TOTAL PENDAPATAN</td><td class="text-end">Rp 2,500,000</td></tr>
            </table>

            <h6 class="text-danger mt-3">BEBAN</h6>
            <table class="table table-sm">
                <tr><td>Beban Bunga Simpanan</td><td class="text-end">Rp 1,200,000</td></tr>
                <tr><td>Beban Operasional</td><td class="text-end">Rp 800,000</td></tr>
                <tr class="fw-bold"><td>TOTAL BEBAN</td><td class="text-end">Rp 2,000,000</td></tr>
                <tr class="fw-bold table-success"><td>LABA BERSIH</td><td class="text-end">Rp 500,000</td></tr>
            </table>
        </div>

        <!-- Cash Flow -->
        <div id="cashFlowReport" class="report-content d-none">
            <h6>Aktivitas Operasi</h6>
            <table class="table table-sm">
                <tr><td>Penerimaan Simpanan</td><td class="text-end">Rp 10,000,000</td></tr>
                <tr><td>Pemberian Pinjaman</td><td class="text-end">Rp (5,000,000)</td></tr>
                <tr><td>Penerimaan Angsuran</td><td class="text-end">Rp 2,000,000</td></tr>
                <tr class="fw-bold"><td>Arus Kas dari Aktivitas Operasi</td><td class="text-end">Rp 7,000,000</td></tr>
            </table>
        </div>

        <!-- Members Report -->
        <div id="membersReport" class="report-content d-none">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>No. Anggota</th><th>Nama</th><th>Status</th><th>Tanggal Bergabung</th><th>Simpanan</th><th>Pinjaman</th></tr></thead>
                    <tbody>
                        <tr>
                            <td>001</td>
                            <td>ADMIN PALING BAIK DI DUNIA</td>
                            <td><span class="badge bg-success">Aktif</span></td>
                            <td>01/01/2025</td>
                            <td>Rp 2,000,000</td>
                            <td>Rp 0</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Savings Report -->
        <div id="savingsReport" class="report-content d-none">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>Jenis Simpanan</th><th>Jumlah Anggota</th><th>Total Saldo</th><th>Rata-rata per Anggota</th></tr></thead>
                    <tbody>
                        <tr><td>Simpanan Pokok</td><td>1</td><td>Rp 100,000</td><td>Rp 100,000</td></tr>
                        <tr><td>Simpanan Wajib</td><td>1</td><td>Rp 50,000</td><td>Rp 50,000</td></tr>
                        <tr><td>Simpanan Sukarela</td><td>1</td><td>Rp 1,850,000</td><td>Rp 1,850,000</td></tr>
                        <tr class="fw-bold"><td>TOTAL</td><td>1</td><td>Rp 2,000,000</td><td>Rp 2,000,000</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Loans Report -->
        <div id="loansReport" class="report-content d-none">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>ID Pinjaman</th><th>Anggota</th><th>Jumlah</th><th>Bunga</th><th>Status</th><th>Tanggal Pengajuan</th><th>Sisa Angsuran</th></tr></thead>
                    <tbody>
                        <tr><td colspan="7" class="text-center text-muted">Belum ada data pinjaman</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function loadReport() {
    const reportType = document.getElementById('reportType').value;
    const year = document.getElementById('reportYear').value;
    const month = document.getElementById('reportMonth').value;
    const monthNames = ['', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    document.getElementById('reportTitle').textContent =
        `${document.getElementById('reportType').options[document.getElementById('reportType').selectedIndex].text} - Periode ${monthNames[parseInt(month)]} ${year}`;

    document.querySelectorAll('.report-content').forEach(el => el.classList.add('d-none'));
    const map = {
        neraca: 'balanceSheetReport',
        laba_rugi: 'incomeStatementReport',
        arus_kas: 'cashFlowReport',
        anggota: 'membersReport',
        simpanan: 'savingsReport',
        pinjaman: 'loansReport'
    };
    const target = map[reportType];
    if (target) document.getElementById(target).classList.remove('d-none');
}

async function generateReport() {
    try {
        const type = document.getElementById('reportType').value;
        const year = document.getElementById('reportYear').value;
        const month = document.getElementById('reportMonth').value;
        const resp = await fetch(`/ksp_peb/src/public/api/laporan.php?action=generate&type=${type}&year=${year}&month=${month}`);
        const data = await resp.json();
        if (data.success) {
            loadReport();
            alert('Laporan berhasil di-generate');
        } else {
            alert(data.message || 'Gagal generate laporan');
        }
    } catch (e) {
        alert('Terjadi kesalahan saat generate laporan');
    }
}

function exportReport() {
    const type = document.getElementById('reportType').value;
    const year = document.getElementById('reportYear').value;
    const month = document.getElementById('reportMonth').value;
    const link = document.createElement('a');
    link.href = `/ksp_peb/src/public/api/laporan.php?action=export&type=${type}&year=${year}&month=${month}&format=pdf`;
    link.download = `${type}_${year}_${month}.pdf`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

document.addEventListener('DOMContentLoaded', loadReport);
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
