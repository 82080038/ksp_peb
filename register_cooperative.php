<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Koperasi - Koperasi Simpan Pinjam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
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
                <p class="text-muted">Isi informasi koperasi yang akan dibuat</p>
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

                <!-- Village & Address Detail -->
                <h5 class="mb-3">Detail Lokasi</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="village" class="form-label">Desa/Kelurahan *</label>
                            <select class="form-control" id="village" name="village_id" required>
                                <option value="">Pilih Desa/Kelurahan</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="postal_code" class="form-label">Kode Pos</label>
                            <input type="text" class="form-control" id="postal_code" name="postal_code" placeholder="Otomatis dari desa" readonly>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="alamat_detail" class="form-label">Detil Alamat</label>
                    <textarea class="form-control" id="alamat_detail" name="alamat_detail" rows="3" placeholder="Nama jalan, RT/RW, patokan" required></textarea>
                </div>

                <!-- Cooperative Information -->
                <h5 class="mb-3">Informasi Koperasi</h5>
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Koperasi *</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="jenis_koperasi" class="form-label">Jenis Koperasi *</label>
                            <select class="form-control" id="jenis_koperasi" name="jenis_koperasi" required>
                                <option value="">Memuat jenis...</option>
                            </select>
                            <div class="form-text text-muted small">Low priority: nanti modul bisa diaktif/nonaktif per jenis.</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="badan_hukum" class="form-label">Badan Hukum *</label>
                            <input type="text" class="form-control" id="badan_hukum" name="badan_hukum" placeholder="Contoh: 12345/BH/M.KUKM.2/III/2020" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tanggal_pendirian_display" class="form-label">Tanggal Pendirian *</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="tanggal_pendirian_display" placeholder="dd/mm/yyyy" style="cursor: pointer; background-color: #fff;">
                                <button class="btn btn-outline-secondary" type="button" id="tanggal_pendirian_trigger">
                                    <i class="bi bi-calendar-date"></i>
                                </button>
                            </div>
                            <input type="hidden" id="tanggal_pendirian" name="tanggal_pendirian" required>
                            <input type="date" class="form-control d-none" id="tanggal_pendirian_picker">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="npwp" class="form-label">NPWP</label>
                    <input type="text" class="form-control" id="npwp" name="npwp" placeholder="15 digit NPWP">
                </div>

                <div class="mb-3">
                    <label for="kontak_resmi" class="form-label">Kontak Resmi *</label>
                    <input type="tel" class="form-control" id="kontak_resmi" name="kontak_resmi" placeholder="Nomor telepon kantor" required>
                </div>

                <!-- Admin Information -->
                <h5 class="mb-3">Informasi Administrator</h5>
                <div class="row">
 <div class="col-md-12">
                        <div class="mb-3">
                            <label for="admin_nama" class="form-label">Nama Lengkap Admin *</label>
                            <input type="text" class="form-control" id="admin_nama" name="admin_nama" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                   <div class="col-md-6">
                     <div class="mb-3">
                            <label for="admin_phone" class="form-label">No. HP Admin *</label>
                            <input type="tel" class="form-control" id="admin_phone" name="admin_phone" pattern="08[0-9]{9,12}" inputmode="numeric" minlength="11" maxlength="14" placeholder="08XXXXXXXXXX" required>
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
                            <label for="admin_username" class="form-label">Username Admin *</label>
                            <input type="text" class="form-control" id="admin_username" name="admin_username" pattern="[a-zA-Z0-9_\.]{4,20}" minlength="4" maxlength="20" placeholder="huruf/angka, 4-20 karakter" required>
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
    <script src="src/public/js/string-helper.js"></script>
    <script src="src/public/js/date-helper.js"></script>
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

                // Load villages for selected district
                loadVillages(data.district_id);
            } else {
                showAlert('warning', 'Data lokasi tidak ditemukan. Silakan kembali ke halaman registrasi anggota.');
            }

            // Load cooperative types from API
            loadCooperativeTypes();

            // Format detil alamat ke Title Case saat blur
            const alamatDetail = document.getElementById('alamat_detail');
            alamatDetail.addEventListener('blur', () => {
                if (alamatDetail.value) {
                    alamatDetail.value = toTitleCase(alamatDetail.value);
                }
            });

            // Batasi input admin_phone hanya angka dan sesuai pola 08xxxxxxxxxx
            const adminPhone = document.getElementById('admin_phone');
            adminPhone.addEventListener('input', () => {
                adminPhone.value = adminPhone.value.replace(/[^0-9]/g, '').slice(0, 14);
            });

            // Normalize username ke huruf kecil dan batasi karakter
            const adminUsername = document.getElementById('admin_username');
            adminUsername.addEventListener('input', () => {
                adminUsername.value = adminUsername.value.toLowerCase().replace(/[^a-z0-9_.]/g, '').slice(0, 20);
            });

            // Init reusable date helper
            initDateInput({
                displayId: 'tanggal_pendirian_display',
                hiddenId: 'tanggal_pendirian',
                pickerId: 'tanggal_pendirian_picker',
                triggerId: 'tanggal_pendirian_trigger'
            });
        });

        // Load villages for a district
        async function loadVillages(districtId) {
            if (!districtId) return;
            const villageSelect = document.getElementById('village');
            const postalInput = document.getElementById('postal_code');
            villageSelect.innerHTML = '<option value="">Memuat desa...</option>';
            try {
                const response = await fetch(`src/public/api/cooperative.php?action=villages&district_id=${districtId}`);
                const result = await response.json();
                if (result.success) {
                    villageSelect.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';
                    result.data.forEach(v => {
                        const opt = document.createElement('option');
                        opt.value = v.id;
                        opt.textContent = v.name;
                        opt.dataset.postal = v.postal_code || '';
                        villageSelect.appendChild(opt);
                    });
                } else {
                    villageSelect.innerHTML = '<option value="">Gagal memuat desa</option>';
                }
            } catch (error) {
                villageSelect.innerHTML = '<option value="">Gagal memuat desa</option>';
            }

            // When village changes, set postal code
            villageSelect.addEventListener('change', (e) => {
                const selected = e.target.selectedOptions[0];
                postalInput.value = selected ? (selected.dataset.postal || '') : '';
            });
        }

        // Load cooperative types
        async function loadCooperativeTypes() {
            const jenisSelect = document.getElementById('jenis_koperasi');
            jenisSelect.innerHTML = '<option value="">Memuat jenis...</option>';
            try {
                const response = await fetch('src/public/api/cooperative.php?action=types');
                const result = await response.json();
                if (result.success && Array.isArray(result.data)) {
                    jenisSelect.innerHTML = '<option value="">Pilih Jenis</option>';
                    result.data.forEach(t => {
                        const opt = document.createElement('option');
                        opt.value = t.name;
                        opt.textContent = t.name;
                        jenisSelect.appendChild(opt);
                    });
                } else {
                    jenisSelect.innerHTML = '<option value="">Gagal memuat jenis</option>';
                }
            } catch (error) {
                jenisSelect.innerHTML = '<option value="">Gagal memuat jenis</option>';
            }
        }

        // Form submission
        document.getElementById('cooperativeRegisterForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);

            // Map jenis_koperasi to backend field
            data.jenis = data.jenis_koperasi || '';

            // Normalize detil alamat to camelCase on submit (if filled)
            if (data.alamat_detail) {
                data.alamat_detail = toTitleCase(data.alamat_detail);
                document.getElementById('alamat_detail').value = data.alamat_detail;
            }

            // Map detil alamat sebagai alamat_legal untuk backend
            data.alamat_legal = data.alamat_detail || '';

            // Add location data
            const locationData = localStorage.getItem('cooperativeLocationData');
            if (locationData) {
                const locData = JSON.parse(locationData);
                data.district_id = locData.district_id;
                data.province_id = locData.province_id;
                data.regency_id = locData.regency_id;
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

            // Validate village
            if (!data.village_id) {
                showAlert('danger', 'Pilih desa/kelurahan');
                return;
            }

            // Ensure tanggal pendirian terisi
            if (!data.tanggal_pendirian) {
                showAlert('danger', 'Tanggal pendirian wajib diisi');
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
