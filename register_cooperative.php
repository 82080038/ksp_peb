<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Koperasi - Koperasi Simpan Pinjam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        .register-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 2rem;
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .register-header h2 {
            color: #333;
            font-weight: 600;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #ddd;
        }
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            margin-top: 1rem;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .alert {
            border-radius: 10px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <div class="register-header">
                <h2>Daftar Koperasi Baru</h2>
                <p class="text-muted">Buat koperasi simpan pinjam baru</p>
            </div>

            <div id="alert-container"></div>

            <form id="cooperativeRegisterForm">
                <!-- Location Information (pre-filled) -->
                <h5 class="mb-3">Lokasi Koperasi</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="province" class="form-label">Provinsi</label>
                            <input type="text" class="form-control" id="province" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="regency" class="form-label">Kabupaten/Kota</label>
                            <input type="text" class="form-control" id="regency" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="district" class="form-label">Kecamatan</label>
                            <input type="text" class="form-control" id="district" readonly>
                        </div>
                    </div>
                </div>

                <!-- Cooperative Information -->
                <h5 class="mb-3">Informasi Koperasi</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Koperasi *</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="jenis" class="form-label">Jenis Koperasi *</label>
                            <select class="form-control" id="jenis" name="jenis" required>
                                <option value="">Pilih Jenis</option>
                                <option value="Simpan Pinjam">Simpan Pinjam</option>
                                <option value="Konsumsi">Konsumsi</option>
                                <option value="Produksi">Produksi</option>
                                <option value="Pemasaran">Pemasaran</option>
                                <option value="Jasa">Jasa</option>
                                <option value="Serba Usaha">Serba Usaha</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="badan_hukum" class="form-label">Badan Hukum *</label>
                            <input type="text" class="form-control" id="badan_hukum" name="badan_hukum" placeholder="Contoh: Koperasi" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tanggal_pendirian" class="form-label">Tanggal Pendirian *</label>
                            <input type="date" class="form-control" id="tanggal_pendirian" name="tanggal_pendirian" required>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="npwp" class="form-label">NPWP</label>
                    <input type="text" class="form-control" id="npwp" name="npwp" placeholder="15 digit NPWP">
                </div>

                <div class="mb-3">
                    <label for="alamat_legal" class="form-label">Alamat Legal *</label>
                    <textarea class="form-control" id="alamat_legal" name="alamat_legal" rows="3" placeholder="Alamat lengkap koperasi" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="kontak_resmi" class="form-label">Kontak Resmi *</label>
                    <input type="tel" class="form-control" id="kontak_resmi" name="kontak_resmi" placeholder="Nomor telepon kantor" required>
                </div>

                <!-- Admin Information -->
                <h5 class="mb-3">Informasi Administrator</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="admin_nama" class="form-label">Nama Lengkap Admin *</label>
                            <input type="text" class="form-control" id="admin_nama" name="admin_nama" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="admin_email" class="form-label">Email Admin *</label>
                            <input type="email" class="form-control" id="admin_email" name="admin_email" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="admin_phone" class="form-label">No. HP Admin *</label>
                            <input type="tel" class="form-control" id="admin_phone" name="admin_phone" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="admin_password" class="form-label">Password Admin *</label>
                            <input type="password" class="form-control" id="admin_password" name="admin_password" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-register">
                    <span class="spinner-border spinner-border-sm d-none" id="registerSpinner"></span>
                    <span id="registerText">Daftar Koperasi</span>
                </button>
            </form>

            <div class="login-link text-center mt-3">
                <p>Sudah punya akun? <a href="login.php">Login disini</a></p>
                <p><a href="register.php">Kembali ke Registrasi Anggota</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load location data from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const locationData = localStorage.getItem('cooperativeLocationData');
            if (locationData) {
                const data = JSON.parse(locationData);
                document.getElementById('province').value = data.province_name;
                document.getElementById('regency').value = data.regency_name;
                document.getElementById('district').value = data.district_name;

                // Store IDs for submission
                document.getElementById('province').setAttribute('data-id', data.province_id);
                document.getElementById('regency').setAttribute('data-id', data.regency_id);
                document.getElementById('district').setAttribute('data-id', data.district_id);
            } else {
                showAlert('warning', 'Data lokasi tidak ditemukan. Silakan kembali ke halaman registrasi anggota.');
            }
        });

        // Form submission
        document.getElementById('cooperativeRegisterForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);

            // Add location data
            const locationData = localStorage.getItem('cooperativeLocationData');
            if (locationData) {
                const locData = JSON.parse(locationData);
                data.district_id = locData.district_id;
            }

            // Validate phone format
            if (!/^08[0-9]{9,12}$/.test(data.kontak_resmi)) {
                showAlert('danger', 'Format nomor kontak resmi tidak valid');
                return;
            }

            if (!/^08[0-9]{9,12}$/.test(data.admin_phone)) {
                showAlert('danger', 'Format nomor HP admin tidak valid');
                return;
            }

            const registerSpinner = document.getElementById('registerSpinner');
            const registerText = document.getElementById('registerText');

            registerSpinner.classList.remove('d-none');
            registerText.textContent = 'Mendaftarkan...';

            try {
                const response = await fetch('src/public/api/cooperative.php?action=create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    showAlert('success', 'Koperasi berhasil didaftarkan! Mengarahkan ke halaman login...');
                    localStorage.removeItem('cooperativeLocationData'); // Clear stored data
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
                } else {
                    showAlert('danger', result.message || 'Pendaftaran koperasi gagal');
                }
            } catch (error) {
                showAlert('danger', 'Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                registerSpinner.classList.add('d-none');
                registerText.textContent = 'Daftar Koperasi';
            }
        });

        function showAlert(type, message) {
            const alertContainer = document.getElementById('alert-container');
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
            alertContainer.appendChild(alertDiv);

            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
    </script>
</body>
</html>
