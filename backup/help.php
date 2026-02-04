<?php
// Help Page
require_once __DIR__ . '/../../../bootstrap.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header('Location: /ksp_peb/login.php');
    exit;
}

$user = $auth->getCurrentUser();
?>

<div class="row">
    <div class="col-12">
        <h2>Pusat Bantuan</h2>
        <p class="text-muted">Dokumentasi dan panduan penggunaan sistem koperasi</p>
    </div>
</div>

<!-- Quick Help Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-book fs-1 text-primary mb-3"></i>
                <h5>Panduan Pengguna</h5>
                <p class="text-muted">Panduan lengkap penggunaan sistem koperasi</p>
                <button class="btn btn-primary" onclick="showGuide('user')">Buka Panduan</button>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-play-circle fs-1 text-success mb-3"></i>
                <h5>Video Tutorial</h5>
                <p class="text-muted">Video tutorial langkah demi langkah</p>
                <button class="btn btn-success" onclick="showVideos()">Tonton Video</button>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-question-circle fs-1 text-info mb-3"></i>
                <h5>FAQ</h5>
                <p class="text-muted">Pertanyaan yang sering diajukan</p>
                <button class="btn btn-info" onclick="showFAQ()">Lihat FAQ</button>
            </div>
        </div>
    </div>
</div>

<!-- Documentation Sections -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Dokumentasi Sistem</h5>
            </div>
            <div class="card-body">
                <div class="accordion" id="documentationAccordion">
                    <!-- Getting Started -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#gettingStarted">
                                <i class="bi bi-rocket-takeoff me-2"></i>Memulai
                            </button>
                        </h2>
                        <div id="gettingStarted" class="accordion-collapse collapse show" data-bs-parent="#documentationAccordion">
                            <div class="accordion-body">
                                <h6>1. Login ke Sistem</h6>
                                <p>Gunakan username dan password yang telah diberikan oleh admin. Pastikan Anda menggunakan koneksi internet yang stabil.</p>
                                
                                <h6>2. Dashboard Overview</h6>
                                <p>Dashboard menampilkan statistik penting seperti total anggota, simpanan, pinjaman, dan SHU. Anda dapat mengakses semua modul dari menu sidebar.</p>
                                
                                <h6>3. Navigasi Sistem</h6>
                                <p>Gunakan menu sidebar di sebelah kiri untuk navigasi antar modul. Setiap modul memiliki fitur-fitur spesifik sesuai kebutuhan.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Member Management -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#memberManagement">
                                <i class="bi bi-people me-2"></i>Manajemen Anggota
                            </button>
                        </h2>
                        <div id="memberManagement" class="accordion-collapse collapse" data-bs-parent="#documentationAccordion">
                            <div class="accordion-body">
                                <h6>Menambah Anggota Baru</h6>
                                <ol>
                                    <li>Buka menu "Anggota" di sidebar</li>
                                    <li>Klik tombol "Tambah Anggota"</li>
                                    <li>Isi form pendaftaran dengan data lengkap</li>
                                    <li>Upload dokumen yang diperlukan (KTP, dll)</li>
                                    <li>Klik "Simpan" untuk menyimpan data</li>
                                </ol>
                                
                                <h6>Mengedit Data Anggota</h6>
                                <p>Klik ikon edit pada baris anggota yang ingin diubah, lalu perbarui data yang diperlukan.</p>
                                
                                <h6>Status Anggota</h6>
                                <ul>
                                    <li><strong>Active:</strong> Anggota dapat melakukan transaksi</li>
                                    <li><strong>Inactive:</strong> Anggota tidak dapat melakukan transaksi</li>
                                    <li><strong>Blacklist:</strong> Anggota diblokir dari sistem</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Savings Management -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#savingsManagement">
                                <i class="bi bi-piggy-bank me-2"></i>Manajemen Simpanan
                            </button>
                        </h2>
                        <div id="savingsManagement" class="accordion-collapse collapse" data-bs-parent="#documentationAccordion">
                            <div class="accordion-body">
                                <h6>Jenis Simpanan</h6>
                                <ul>
                                    <li><strong>Simpanan Pokok:</strong> Simpanan wajib sekali saat pendaftaran</li>
                                    <li><strong>Simpanan Wajib:</strong> Simpanan bulanan wajib</li>
                                    <li><strong>Simpanan Sukarela:</strong> Simpanan tambahan sesuai keinginan</li>
                                </ul>
                                
                                <h6>Proses Simpanan</h6>
                                <ol>
                                    <li>Pilih anggota yang akan menabung</li>
                                    <li>Pilih jenis simpanan</li>
                                    <li>Masukkan jumlah simpanan</li>
                                    <li>Konfirmasi transaksi</li>
                                    <li>Cetak bukti simpanan (opsional)</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <!-- Loan Management -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#loanManagement">
                                <i class="bi bi-cash-stack me-2"></i>Manajemen Pinjaman
                            </button>
                        </h2>
                        <div id="loanManagement" class="accordion-collapse collapse" data-bs-parent="#documentationAccordion">
                            <div class="accordion-body">
                                <h6>Proses Pengajuan Pinjaman</h6>
                                <ol>
                                    <li>Anggota mengajukan pinjaman</li>
                                    <li>Admin memverifikasi dokumen</li>
                                    <li>Analisis kredit dan persetujuan</li>
                                    <li>Pencairan dana pinjaman</li>
                                    <li>Pembayaran cicilan rutin</li>
                                </ol>
                                
                                <h6>Syarat Pinjaman</h6>
                                <ul>
                                    <li>Anggota harus aktif minimal 3 bulan</li>
                                    <li>Tidak ada pinjaman macet</li>
                                    <li>Kelengkapan dokumen terpenuhi</li>
                                    <li>Memenuhi plafon yang ditetapkan</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Accounting -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accounting">
                                <i class="bi bi-journal-text me-2"></i>Akuntansi
                            </button>
                        </h2>
                        <div id="accounting" class="accordion-collapse collapse" data-bs-parent="#documentationAccordion">
                            <div class="accordion-body">
                                <h6>Jurnal Umum</h6>
                                <p>Semua transaksi secara otomatis tercatat dalam jurnal umum. Anda dapat melihat dan mengedit jurnal jika diperlukan.</p>
                                
                                <h6>Neraca Saldo</h6>
                                <p>Digunakan untuk membuat laporan keuangan. Sistem secara otomatis menghitung neraca saldo berdasarkan transaksi.</p>
                                
                                <h6>Laporan Keuangan</h6>
                                <ul>
                                    <li>Laporan Laba Rugi</li>
                                    <li>Neraca</li>
                                    <li>Arus Kas</li>
                                    <li>Laporan Perubahan Modal</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Support Info -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informasi Support</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6><i class="bi bi-telephone me-2"></i>Hotline</h6>
                    <p>(021) 1234-5678</p>
                    <small class="text-muted">Senin - Jumat, 08:00 - 17:00</small>
                </div>
                
                <div class="mb-3">
                    <h6><i class="bi bi-envelope me-2"></i>Email</h6>
                    <p>support@koperasi.com</p>
                    <small class="text-muted">Response time: 24 jam</small>
                </div>
                
                <div class="mb-3">
                    <h6><i class="bi bi-whatsapp me-2"></i>WhatsApp</h6>
                    <p>0812-3456-7890</p>
                    <small class="text-muted">Available 24/7 for urgent issues</small>
                </div>
                
                <div class="mb-3">
                    <h6><i class="bi bi-geo-alt me-2"></i>Alamat Kantor</h6>
                    <p>Jl. Koperasi No. 123<br>Jakarta 12345</p>
                </div>
            </div>
        </div>

        <!-- Quick Tips -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Tips Cepat</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <h6><i class="bi bi-lightning me-2"></i>Shortcut Keyboard</h6>
                        <small class="text-muted">Ctrl + K untuk quick search</small>
                    </div>
                    <div class="list-group-item">
                        <h6><i class="bi bi-download me-2"></i>Export Data</h6>
                        <small class="text-muted">Gunakan tombol Export di setiap halaman</small>
                    </div>
                    <div class="list-group-item">
                        <h6><i class="bi bi-printer me-2"></i>Print</h6>
                        <small class="text-muted">Ctrl + P untuk print halaman</small>
                    </div>
                    <div class="list-group-item">
                        <h6><i class="bi bi-arrow-clockwise me-2"></i>Refresh Data</h6>
                        <small class="text-muted">F5 untuk refresh data terbaru</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Status Sistem</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Database</span>
                    <span class="badge bg-success">Online</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>API Services</span>
                    <span class="badge bg-success">Normal</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Backup</span>
                    <span class="badge bg-warning">Manual</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Uptime</span>
                    <span class="badge bg-info">99.9%</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Support Modal -->
<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hubungi Support</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="supportForm">
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subjek</label>
                        <select class="form-select" id="subject" name="subject" required>
                            <option value="">Pilih Subjek</option>
                            <option value="technical">Masalah Teknis</option>
                            <option value="account">Akun & Login</option>
                            <option value="transaction">Transaksi</option>
                            <option value="feature">Permintaan Fitur</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="priority" class="form-label">Prioritas</label>
                        <select class="form-select" id="priority" name="priority" required>
                            <option value="low">Rendah</option>
                            <option value="medium">Sedang</option>
                            <option value="high">Tinggi</option>
                            <option value="urgent">Darurat</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Pesan</label>
                        <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="sendSupport()">Kirim Tiket</button>
            </div>
        </div>
    </div>
</div>

<script>
function showGuide(type) {
    // Scroll to relevant section
    const section = type === 'user' ? 'gettingStarted' : 'memberManagement';
    const element = document.getElementById(section);
    element.scrollIntoView({ behavior: 'smooth' });
    
    // Expand accordion if collapsed
    const button = element.previousElementSibling.querySelector('.accordion-button');
    if (button.classList.contains('collapsed')) {
        button.click();
    }
}

function showVideos() {
    alert('Video tutorials akan segera tersedia. Silakan hubungi support untuk bantuan langsung.');
}

function showFAQ() {
    const faqContent = `
        <h5>Pertanyaan Umum</h5>
        <div class="accordion" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        Bagaimana cara reset password?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse show">
                    <div class="accordion-body">
                        Hubungi admin sistem untuk reset password. Anda juga dapat menggunakan fitur "Lupa Password" di halaman login.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq2">
                        Kenapa tidak bisa login?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        Periksa kembali username dan password. Pastikan caps lock tidak aktif. Jika masih gagal, hubungi admin.
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Show FAQ in modal
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">FAQ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    ${faqContent}
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    modal.addEventListener('hidden.bs.modal', () => {
        document.body.removeChild(modal);
    });
}

function sendSupport() {
    const form = document.getElementById('supportForm');
    const formData = new FormData(form);
    
    // Here you would normally send to backend
    alert('Tiket support telah dikirim. Kami akan menghubungi Anda segera.');
    bootstrap.Modal.getInstance(document.getElementById('contactModal')).hide();
    form.reset();
}
</script>
