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
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .main-container {
            width: 100%;
            max-width: 1200px;
            padding: 0 1rem;
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
            padding: 1rem 0;
        }
        .register-header h2 {
            color: #333;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .register-header p {
            color: #666;
            margin-bottom: 0;
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
        
        .is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }
        
        .is-invalid:focus {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }
        
        /* Label styling for better readability */
        .form-label small {
            font-size: 0.75em;
            font-weight: normal;
        }

        /* Responsive design for better centering */
        @media (max-width: 768px) {
            body {
                padding: 1rem 0;
                align-items: flex-start;
                padding-top: 2rem;
            }
            
            .main-container {
                padding: 0 0.5rem;
            }
            
            .register-container {
                padding: 1.5rem;
                margin: 0;
            }
        }

        @media (min-width: 769px) {
            body {
                padding: 2rem 0;
            }
        }
    </style>
    <link href="src/public/css/form-helper.css" rel="stylesheet">
</head>
<body>
    <div class="main-container">
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
                            <label for="coop_village" class="form-label">Desa/Kelurahan *</label>
                            <select class="form-control" id="coop_village" name="village_id" required tabindex="1">
                                <option value="">Pilih Desa/Kelurahan</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="postal_code" class="form-label">Kode Pos</label>
                            <input type="text" class="form-control" id="postal_code" name="postal_code" placeholder="Otomatis dari desa" readonly tabindex="2">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="alamat_detail" class="form-label">Detil Alamat</label>
                    <textarea class="form-control" id="alamat_detail" name="alamat_detail" rows="3" placeholder="Nama jalan, RT/RW, patokan" required tabindex="3"></textarea>
                </div>

                <!-- Cooperative Information -->
                <h5 class="mb-3">Informasi Koperasi</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="jenis_koperasi" class="form-label">Jenis Koperasi <small class="text-muted">(PP No. 7/2021)</small></label>
                            <select class="form-control" id="jenis_koperasi" name="jenis_koperasi" required tabindex="4">
                                <option value="">Pilih Jenis Koperasi</option>
                                <!-- Options will be loaded from database -->
                            </select>
                           
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="nama_koperasi" class="form-label">Nama Koperasi *</label>
                            <input type="text" class="form-control" id="nama_koperasi" name="nama_koperasi" required tabindex="5">
                            <div class="form-text text-muted small">Nama resmi koperasi sesuai akta pendirian</div>
                        </div>
                    </div>
                    
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="badan_hukum" class="form-label">Badan Hukum <small class="text-muted">(UU No. 25/1992 & UU No. 6/2023)</small></label>
                            <select class="form-control" id="badan_hukum" name="badan_hukum" required tabindex="6">
                                <option value="">Pilih Status Badan Hukum</option>
                                <option value="belum_terdaftar">Belum Terdaftar</option>
                                <option value="terdaftar">Terdaftar (SABH)</option>
                                <option value="badan_hukum">Badan Hukum (SABH)</option>
                            </select>
                            <div class="form-text text-muted small">Status sesuai UU No. 25/1992 & UU No. 6/2023</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tanggal_pendirian_display" class="form-label">Tanggal Pendirian *</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="tanggal_pendirian_display" placeholder="31082026 (hanya angka)" required tabindex="7" inputmode="numeric" pattern="[0-9\/]*">
                                <button class="btn btn-outline-secondary" type="button" id="tanggal_pendirian_btn">
                                    <i class="bi bi-calendar-date"></i>
                                </button>
                            </div>
                            <input type="hidden" id="tanggal_pendirian" name="tanggal_pendirian" required>
                            <input type="date" class="form-control" id="tanggal_pendirian_picker" style="position: absolute; opacity: 0; pointer-events: none; z-index: -1;">
                            <div class="form-text text-muted small">Ketik angka saja (31082026) → otomatis jadi 31/08/2026</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="npwp" class="form-label">NPWP <small class="text-muted">(PMK No. 112/2022)</small></label>
                            <input type="text" class="form-control" id="npwp" name="npwp" placeholder="16 digit NPWP" tabindex="8" inputmode="numeric" pattern="[0-9\-]*">
                            <div class="form-text text-muted small">Format: 3201234567890001 (16 digit tanpa separator)</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="kontak_resmi" class="form-label">Kontak Resmi *</label>
                            <input type="tel" class="form-control" id="kontak_resmi" name="kontak_resmi" placeholder="0857-1122-3344" required tabindex="9" inputmode="numeric" pattern="[0-9\-]*">
                            <div class="form-text text-muted small">Format: 0857-1122-3344 (akan otomatis disalin ke No. HP Admin)</div>
                        </div>
                    </div>
                </div>

                <!-- Admin Information -->
                <h5 class="mb-3">Informasi Administrator</h5>
                <div class="row">
 <div class="col-md-12">
                        <div class="mb-3">
                            <label for="admin_nama" class="form-label">Nama Lengkap Admin *</label>
                            <input type="text" class="form-control" id="admin_nama" name="admin_nama" required tabindex="10">
                        </div>
                    </div>
                </div>
                <div class="row">
                   <div class="col-md-6">
                     <div class="mb-3">
                            <label for="admin_phone" class="form-label">No. HP Admin *</label>
                            <input type="tel" class="form-control" id="admin_phone" name="admin_phone" inputmode="numeric" minlength="11" maxlength="14" placeholder="0857-1122-3344" required tabindex="11" pattern="[0-9\-]*">
                            <div class="form-text text-muted small">Format: 0857-1122-3344 (otomatis dari Kontak Resmi jika kosong)</div>
                        </div>
                   </div>
                     
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="admin_email" class="form-label">Email Admin *</label>
                            <input type="email" class="form-control" id="admin_email" name="admin_email" required tabindex="12">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="admin_username" class="form-label">Username Admin *</label>
                            <input type="text" class="form-control" id="admin_username" name="admin_username" pattern="[a-zA-Z0-9_\.]{4,20}" minlength="4" maxlength="20" placeholder="huruf/angka, 4-20 karakter" required tabindex="13">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="admin_password" class="form-label">Password Admin *</label>
                            <input type="password" class="form-control" id="admin_password" name="admin_password" required tabindex="14">
                        </div>
                    </div>
                </div>

               

                <button type="submit" class="btn btn-primary btn-register" id="registerButton">
                    <span class="spinner-border spinner-border-sm d-none" id="registerSpinner"></span>
                    <span id="registerText">Daftar Koperasi</span>
                </button>
            </form>

            <div class="login-link text-center mt-3">
                <p>Sudah punya akun? <a href="/ksp_peb/login.php">Login disini</a></p>
                <p><a href="/ksp_peb/register.php">Kembali ke Registrasi Anggota</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="src/public/js/date-helper.js"></script>
    <script src="src/public/js/form-helper.js"></script>
    <script src="src/public/js/avoid-next-error.js"></script>
    <script>
        // Load location data from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            // Reset all fields except location data using FormHelper
            FormHelper.resetAllFieldsExceptLocation('cooperativeRegisterForm');
            
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

            // Load cooperative types from API dan setup dynamic behavior
            loadCooperativeTypes().then(() => {
                // Setup jenis koperasi dynamic behavior setelah types dimuat
                FormHelper.setupJenisKoperasiDynamic('jenis_koperasi', 'nama_koperasi', null); // Remove legal section
            });

            // Setup focus dropdown for static combo boxes
            setupFocusDropdown('badan_hukum');

            // Setup ENTER key navigation for the form
            FormHelper.setupEnterKeyNavigation('cooperativeRegisterForm', 'registerButton');
            
            // Setup NPWP formatting (16 digit PMK 112/2022 standard)
            FormHelper.setupNPWPFormatting('npwp');
            
            // Setup phone formatting (only numbers, auto-dash)
            FormHelper.setupPhoneFormatting('kontak_resmi', 14);
            FormHelper.setupPhoneFormatting('admin_phone', 14);
            
            // Format detil alamat ke Camel Case saat blur
            const alamatDetail = document.getElementById('alamat_detail');
            alamatDetail.addEventListener('blur', () => {
                if (alamatDetail.value) {
                    // Convert to Camel Case: "jalan sudirman no 123" -> "Jalan Sudirman No 123"
                    let value = alamatDetail.value.toLowerCase();
                    value = value.replace(/\b\w/g, function(match) {
                        return match.toUpperCase();
                    });
                    alamatDetail.value = value;
                }
            });

            // Copy kontak_resmi to admin_phone on blur if valid and admin_phone is empty
            const adminPhone = document.getElementById('admin_phone');
            const kontakResmi = document.getElementById('kontak_resmi');
            
            kontakResmi.addEventListener('blur', () => {
                // Only copy if admin_phone is empty and kontak_resmi is valid
                // Copy WITH dashes to admin_phone since both now use same format
                if (!adminPhone.value && /^08[0-9-]{9,14}$/.test(kontakResmi.value)) {
                    adminPhone.value = kontakResmi.value; // Copy with dashes
                }
            });

            // Auto-uppercase for admin_nama on blur
            const adminNamaInput = document.getElementById('admin_nama');
            adminNamaInput.addEventListener('blur', () => {
                if (adminNamaInput.value) {
                    adminNamaInput.value = adminNamaInput.value.toUpperCase();
                }
            });

            // Auto-uppercase for nama_koperasi on blur (with focus management)
            const namaKoperasiInput = document.getElementById('nama_koperasi');
            namaKoperasiInput.addEventListener('blur', () => {
                if (namaKoperasiInput.value) {
                    namaKoperasiInput.value = namaKoperasiInput.value.toUpperCase();
                    
                    // Focus ke badan_hukum
                    const badanHukumInput = document.getElementById('badan_hukum');
                    badanHukumInput.focus();
                    
                    // Update label Informasi Administrator
                    const adminInfoLabel = document.querySelector('h5');
                    if (adminInfoLabel && adminInfoLabel.textContent.includes('Informasi Administrator')) {
                        adminInfoLabel.textContent = 'Informasi Administrator';
                    }
                }
            });

            // Init reusable date helper
            initDateInput({
                displayId: 'tanggal_pendirian_display',
                hiddenId: 'tanggal_pendirian',
                pickerId: 'tanggal_pendirian_picker',
                triggerId: 'tanggal_pendirian_btn'
            });
        });

        // Load villages for a district
        async function loadVillages(districtId) {
            if (!districtId) return;
            const villageSelect = document.getElementById('coop_village');
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
                    
                    // Setup focus dropdown behavior for village select
                    setupFocusDropdown('coop_village');
                    
                } else {
                    villageSelect.innerHTML = '<option value="">Gagal memuat desa</option>';
                }
            } catch (error) {
                villageSelect.innerHTML = '<option value="">Gagal memuat desa</option>';
            }

            // When village changes, set postal code and focus to alamat_detail
            villageSelect.addEventListener('change', (e) => {
                const selected = e.target.selectedOptions[0];
                postalInput.value = selected ? (selected.dataset.postal || '') : '';
                
                // Focus to alamat_detail after village selection
                const alamatDetailInput = document.getElementById('alamat_detail');
                if (alamatDetailInput && selected) {
                    alamatDetailInput.focus();
                }
            });
        }

        // Helper function to setup focus dropdown behavior
        function setupFocusDropdown(selectId) {
            const selectElement = document.getElementById(selectId);
            if (!selectElement) return;
            
            // Add focus event to show dropdown when data is loaded
            selectElement.addEventListener('focus', function() {
                // Only show dropdown if there are options beyond the placeholder
                if (this.options.length > 1) {
                    this.size = this.options.length > 10 ? 10 : this.options.length;
                    this.setAttribute('size', this.size);
                }
            });
            
            // Add blur event to restore single line
            selectElement.addEventListener('blur', function() {
                this.removeAttribute('size');
                this.size = 1;
            });
            
            // Add change event to restore single line after selection
            selectElement.addEventListener('change', function() {
                this.removeAttribute('size');
                this.size = 1;
            });
        }

        // Load cooperative types from database
        async function loadCooperativeTypes() {
            const jenisSelect = document.getElementById('jenis_koperasi');
            jenisSelect.innerHTML = '<option value="">Memuat jenis koperasi...</option>';
            
            try {
                const response = await fetch('src/public/api/cooperative.php?action=types');
                const result = await response.json();
                
                if (result.success && result.data) {
                    jenisSelect.innerHTML = '<option value="">Pilih Jenis Koperasi</option>';
                    result.data.forEach(type => {
                        const option = document.createElement('option');
                        option.value = type.code; // Use CODE as value (KSP, KK, KP, etc.)
                        option.textContent = type.name; // Use full name as display
                        option.dataset.category = type.category; // Store category for future use
                        jenisSelect.appendChild(option);
                    });
                    
                    // Setup focus dropdown behavior
                    setupFocusDropdown('jenis_koperasi');
                    
                } else {
                    jenisSelect.innerHTML = '<option value="">Gagal memuat jenis koperasi</option>';
                    console.error('Failed to load cooperative types:', result);
                }
            } catch (error) {
                jenisSelect.innerHTML = '<option value="">Gagal memuat jenis koperasi</option>';
                console.error('Error loading cooperative types:', error);
            }
        }

        // Form submission
        document.getElementById('cooperativeRegisterForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            // Define field validation rules
            const fieldRules = {
                'jenis_koperasi': {
                    label: 'Jenis Koperasi',
                    required: true,
                    elementId: 'jenis_koperasi'
                },
                'nama_koperasi': {
                    label: 'Nama Koperasi',
                    required: true,
                    elementId: 'nama_koperasi'
                },
                'badan_hukum': {
                    label: 'Badan Hukum',
                    required: true,
                    elementId: 'badan_hukum'
                },
                'tanggal_pendirian': {
                    label: 'Tanggal Pendirian',
                    required: true,
                    elementId: 'tanggal_pendirian'
                },
                'village_id': {
                    label: 'Desa/Kelurahan',
                    required: true,
                    elementId: 'coop_village'
                },
                'alamat_detail': {
                    label: 'Detil Alamat',
                    required: true,
                    elementId: 'alamat_detail'
                },
                'kontak_resmi': {
                    label: 'Kontak Resmi',
                    required: true,
                    type: 'phone',
                    elementId: 'kontak_resmi'
                },
                'admin_nama': {
                    label: 'Nama Lengkap Admin',
                    required: true,
                    elementId: 'admin_nama'
                },
                'admin_phone': {
                    label: 'No. HP Admin',
                    required: true,
                    type: 'phone',
                    elementId: 'admin_phone'
                },
                'admin_email': {
                    label: 'Email Admin',
                    required: true,
                    type: 'email',
                    elementId: 'admin_email'
                },
                'admin_username': {
                    label: 'Username Admin',
                    required: true,
                    minLength: 4,
                    maxLength: 20,
                    pattern: '[a-zA-Z0-9_\\.]{4,20}',
                    elementId: 'admin_username'
                },
                'admin_password': {
                    label: 'Password Admin',
                    required: true,
                    minLength: 6,
                    elementId: 'admin_password'
                }
            };

            // Validate form
            const validation = FormHelper.validateForm('cooperativeRegisterForm', fieldRules);
            
            if (!validation.isValid) {
                const errorMessage = FormHelper.showFormErrors(validation.errors);
                FormHelper.showAlert('danger', errorMessage);
                return;
            }

            const data = validation.data;

            // Map jenis_koperasi to backend field
            data.jenis = data.jenis_koperasi || '';
            
            // If jenis_koperasi is a code (from database), find the corresponding name
            if (data.jenis && /^[A-Z]+$/.test(data.jenis)) {
                // This is a code, we need to convert it to the expected format
                // For now, keep the code as is, backend will handle the conversion
            }

            // Normalize detil alamat to camelCase on submit (if filled)
            if (data.alamat_detail) {
                data.alamat_detail = toTitleCase(data.alamat_detail);
                document.getElementById('alamat_detail').value = data.alamat_detail;
            }

            // Map detil alamat sebagai alamat_legal untuk backend
            data.alamat_legal = data.alamat_detail || '';

            // Remove formatting dashes from phone numbers before sending to database
            if (data.kontak_resmi) {
                data.kontak_resmi = data.kontak_resmi.replace(/-/g, '');
            }
            if (data.admin_phone) {
                data.admin_phone = data.admin_phone.replace(/-/g, '');
            }
            
            // Use clean NPWP value from hidden field
            const npwpCleanField = document.getElementById('npwp_clean');
            if (npwpCleanField && npwpCleanField.value) {
                data.npwp = npwpCleanField.value;
            } else if (data.npwp) {
                // Fallback: clean NPWP (remove all non-digits) before sending to database
                data.npwp = data.npwp.replace(/[^0-9]/g, '');
            }

            // Add location data
            const locationData = localStorage.getItem('cooperativeLocationData');
            if (locationData) {
                const locData = JSON.parse(locationData);
                data.district_id = locData.district_id;
                data.province_id = locData.province_id;
                data.regency_id = locData.regency_id;
            }

            const registerSpinner = document.getElementById('registerSpinner');
            const registerText = document.getElementById('registerText');

            registerSpinner.classList.remove('d-none');
            registerText.textContent = 'Mendaftarkan...';
            
            // Disable submit button during loading
            document.getElementById('registerButton').disabled = true;

            try {
                const response = await fetch('src/public/api/cooperative.php?action=create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    FormHelper.showAlert('success', 'Koperasi berhasil didaftarkan! Mengarahkan ke halaman login...');
                    localStorage.removeItem('cooperativeLocationData'); // Clear stored data
                    setTimeout(() => {
                        window.location.href = '/ksp_peb/login.php';
                    }, 2000);
                } else {
                    FormHelper.showAlert('danger', result.message || 'Pendaftaran koperasi gagal');
                }
            } catch (error) {
                FormHelper.showAlert('danger', 'Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                registerSpinner.classList.add('d-none');
                registerText.textContent = 'Daftar Koperasi';
                
                // Re-enable submit button
                document.getElementById('registerButton').disabled = false;
            }
        });
    </script>
</body>
</html>
