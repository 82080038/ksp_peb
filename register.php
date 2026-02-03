<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Koperasi</title>
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
            max-width: 600px;
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
        .login-link {
            text-align: center;
            margin-top: 1rem;
        }
        .login-link a {
            color: #667eea;
            text-decoration: none;
        }
        .alert {
            border-radius: 10px;
            margin-bottom: 1rem;
        }
        .password-strength {
            margin-top: 0.5rem;
        }
        .password-strength-bar {
            height: 5px;
            border-radius: 3px;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <div class="register-header">
                <h2>Daftar Akun Baru</h2>
                <p class="text-muted">Bergabung dengan Koperasi Simpan Pinjam</p>
            </div>
            
            <!-- Lokasi Koperasi Selection -->
            <div class="location-selection mb-4">
                <h4 class="mb-3">Pilih Lokasi Koperasi</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="province" class="form-label">Provinsi *</label>
                            <select class="form-control" id="province" required>
                                <option value="">Pilih Provinsi</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="regency" class="form-label">Kabupaten/Kota *</label>
                            <select class="form-control" id="regency" required disabled>
                                <option value="">Pilih Kabupaten/Kota</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Cooperative Selection (shown after district selection) -->
            <div id="cooperativeSelection" style="display: none;">
                <h4 class="mb-3">Pilih Koperasi</h4>
                <div class="mb-3">
                    <label for="cooperative" class="form-label">Koperasi di Lokasi Terpilih</label>
                    <select class="form-control" id="cooperative" required>
                        <option value="">Memuat koperasi...</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" id="selectCooperative" disabled style="display: none;">
                        Lanjutkan Registrasi Anggota
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="createCooperative">
                        <i class="fas fa-plus"></i> Buat Koperasi Baru
                    </button>
                </div>
                <div id="noCooperativeMessage" style="display: none;" class="alert alert-info mt-3">
                    <h6>Belum ada koperasi di kecamatan ini</h6>
                    <p>Anda dapat membuat koperasi baru atau memilih kecamatan lain.</p>
                </div>
            </div>
            
            <!-- Registration Form (shown after cooperative selection) -->
            <div id="registrationForm" style="display: none;">
                <h4 class="mb-3">Informasi Pendaftaran</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap *</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nik" class="form-label">NIK</label>
                            <input type="text" class="form-control" id="nik" name="nik" maxlength="16">
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                
                <div class="mb-3">
                    <label for="phone" class="form-label">No. HP *</label>
                    <input type="tel" class="form-control" id="phone" name="phone" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password *</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div class="password-strength">
                        <div class="progress" style="height: 5px;">
                            <div class="password-strength-bar bg-danger" id="passwordStrength" style="width: 0%"></div>
                        </div>
                        <small class="text-muted" id="passwordStrengthText">Masukkan password</small>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Konfirmasi Password *</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                
                <!-- Address Fields -->
                <h5 class="mb-3">Alamat Lengkap</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="address_province" class="form-label">Provinsi</label>
                            <input type="text" class="form-control" id="address_province" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="address_regency" class="form-label">Kabupaten/Kota</label>
                            <input type="text" class="form-control" id="address_regency" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="address_district" class="form-label">Kecamatan</label>
                            <input type="text" class="form-control" id="address_district" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="village" class="form-label">Desa/Kelurahan *</label>
                            <select class="form-control" id="village" name="village_id" required>
                                <option value="">Pilih Desa/Kelurahan</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="full_address" class="form-label">Alamat Lengkap *</label>
                    <textarea class="form-control" id="full_address" name="full_address" rows="3" placeholder="Jl. Contoh No. 123, RT/RW 001/002" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary btn-register">
                    <span class="spinner-border spinner-border-sm d-none" id="registerSpinner"></span>
                    <span id="registerText">Daftar</span>
                </button>
            </form>
            </div>
            
            <div id="alert-container"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Global variable to store selected cooperative
        let selectedCooperativeId = null;

        // Load provinces on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadProvinces();
        });

        // Load provinces
        async function loadProvinces() {
            try {
                const response = await fetch('src/public/api/cooperative.php?action=provinces');
                const result = await response.json();
                if (result.success) {
                    const provinceSelect = document.getElementById('province');
                    provinceSelect.innerHTML = '<option value="">Pilih Provinsi</option>';
                    result.data.forEach(province => {
                        provinceSelect.innerHTML += `<option value="${province.id}">${province.name}</option>`;
                    });
                }
            } catch (error) {
                console.error('Error loading provinces:', error);
            }
        }

        // Province change handler
        document.getElementById('province').addEventListener('change', async function() {
            const provinceId = this.value;
            const regencySelect = document.getElementById('regency');
            const districtSelect = document.getElementById('district');
            const confirmBtn = document.getElementById('confirmLocation');
            
            // Reset dependent dropdowns
            regencySelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
            districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            regencySelect.disabled = true;
            districtSelect.disabled = true;
            confirmBtn.disabled = true;
            
            if (provinceId) {
                try {
                    const response = await fetch(`src/public/api/cooperative.php?action=cities&province_id=${provinceId}`);
                    const result = await response.json();
                    if (result.success) {
                        regencySelect.disabled = false;
                        result.data.forEach(regency => {
                            regencySelect.innerHTML += `<option value="${regency.id}">${regency.name}</option>`;
                        });
                    }
                } catch (error) {
                    console.error('Error loading cities:', error);
                }
            }
        });

        // Regency change handler
        document.getElementById('regency').addEventListener('change', async function() {
            const regencyId = this.value;
            const districtSelect = document.getElementById('district');
            const confirmBtn = document.getElementById('confirmLocation');
            
            // Reset dependent dropdowns
            districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            districtSelect.disabled = true;
            confirmBtn.disabled = true;
            
            if (regencyId) {
                try {
                    const response = await fetch(`src/public/api/cooperative.php?action=districts&city_id=${regencyId}`);
                    const result = await response.json();
                    if (result.success) {
                        districtSelect.disabled = false;
                        result.data.forEach(district => {
                            districtSelect.innerHTML += `<option value="${district.id}">${district.name}</option>`;
                        });
                    }
                } catch (error) {
                    console.error('Error loading districts:', error);
                }
            }
        });

        // District change handler
        document.getElementById('district').addEventListener('change', async function() {
            const districtId = this.value;
            
            if (districtId) {
                // Show cooperative selection section
                document.querySelector('.location-selection').style.display = 'none';
                document.getElementById('cooperativeSelection').style.display = 'block';
                
                // Load cooperatives for the selected district
                await loadCooperatives(districtId);
                
                // Also load villages for later use in registration
                await loadVillages(districtId);
                
                // Populate address fields
                const provinceName = document.getElementById('province').options[document.getElementById('province').selectedIndex].text;
                const regencyName = document.getElementById('regency').options[document.getElementById('regency').selectedIndex].text;
                const districtName = this.options[this.selectedIndex].text;
                
                document.getElementById('address_province').value = provinceName;
                document.getElementById('address_regency').value = regencyName;
                document.getElementById('address_district').value = districtName;
            });

            // Load villages for selected district
            async function loadVillages(districtId) {
                try {
                    const response = await fetch(`src/public/api/cooperative.php?action=villages&district_id=${districtId}`);
                    const result = await response.json();
                    if (result.success) {
                        const villageSelect = document.getElementById('village');
                        villageSelect.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';
                        result.data.forEach(village => {
                            villageSelect.innerHTML += `<option value="${village.id}">${village.name}</option>`;
                        });
                    }
                } catch (error) {
                    console.error('Error loading villages:', error);
                }
            }

            // Load cooperatives for selected district
            async function loadCooperatives(districtId) {
                try {
                    const response = await fetch(`src/public/api/cooperative.php?action=cooperatives_by_district&district_id=${districtId}`);
                    const result = await response.json();
                    const cooperativeSelect = document.getElementById('cooperative');
                    const selectBtn = document.getElementById('selectCooperative');
                    const noCoopMessage = document.getElementById('noCooperativeMessage');
                    
                    if (result.success && result.data.length > 0) {
                        // Cooperatives exist - show selection dropdown
                        cooperativeSelect.innerHTML = '<option value="">Pilih Koperasi</option>';
                        result.data.forEach(coop => {
                            cooperativeSelect.innerHTML += `<option value="${coop.id}">${coop.nama}</option>`;
                        });
                        cooperativeSelect.style.display = 'block';
                        selectBtn.style.display = 'block';
                        selectBtn.disabled = true;
                        noCoopMessage.style.display = 'none';
                        
                        showAlert('success', 'Koperasi ditemukan di lokasi ini. Silakan pilih koperasi untuk melanjutkan registrasi anggota.');
                    } else {
                        // No cooperatives - show message and hide selection
                        cooperativeSelect.style.display = 'none';
                        selectBtn.style.display = 'none';
                        noCoopMessage.style.display = 'block';
                        
                        showAlert('info', 'Belum ada koperasi di kecamatan ini. Anda dapat membuat koperasi baru.');
                    }
                } catch (error) {
                    console.error('Error loading cooperatives:', error);
                    const cooperativeSelect = document.getElementById('cooperative');
                    const noCoopMessage = document.getElementById('noCooperativeMessage');
                    cooperativeSelect.innerHTML = '<option value="">Error memuat koperasi</option>';
                    cooperativeSelect.style.display = 'block';
                    noCoopMessage.style.display = 'none';
                }
            }

            // Cooperative selection change handler
            document.getElementById('cooperative').addEventListener('change', function() {
                const selectBtn = document.getElementById('selectCooperative');
                selectBtn.disabled = !this.value;
            });

            // Select cooperative button - proceed to member registration
            document.getElementById('selectCooperative').addEventListener('click', function() {
                const cooperativeId = document.getElementById('cooperative').value;
                if (!cooperativeId) {
                    showAlert('danger', 'Pilih koperasi terlebih dahulu');
                    return;
                }
                
                // Store selected cooperative
                selectedCooperativeId = cooperativeId;
                
                // Hide cooperative selection and show registration form
                document.getElementById('cooperativeSelection').style.display = 'none';
                document.getElementById('registrationForm').style.display = 'block';
                
                // Attach form submit event listener now that the form is visible
                setTimeout(() => attachRegisterFormListener(), 100);
                
                showAlert('success', 'Koperasi berhasil dipilih. Silakan lengkapi informasi pendaftaran anggota.');
            });

            // Create cooperative button - redirect to cooperative registration
            document.getElementById('createCooperative').addEventListener('click', function() {
                // Get selected location data
                const provinceId = document.getElementById('province').value;
                const regencyId = document.getElementById('regency').value;
                const districtId = document.getElementById('district').value;
                
                if (!provinceId || !regencyId || !districtId) {
                    showAlert('danger', 'Pilih lokasi lengkap terlebih dahulu');
                    return;
                }
                
                // Store location data in session/localStorage for cooperative registration
                const locationData = {
                    province_id: provinceId,
                    province_name: document.getElementById('province').options[document.getElementById('province').selectedIndex].text,
                    regency_id: regencyId,
                    regency_name: document.getElementById('regency').options[document.getElementById('regency').selectedIndex].text,
                    district_id: districtId,
                    district_name: document.getElementById('district').options[document.getElementById('district').selectedIndex].text
                };
                
                // Store in localStorage for the cooperative registration page
                localStorage.setItem('cooperativeLocationData', JSON.stringify(locationData));
                
                // Confirm with user
                if (confirm('Anda akan diarahkan ke halaman pendaftaran koperasi. Apakah Anda ingin melanjutkan?')) {
                    // Redirect to cooperative registration page (you'll need to create this)
                    window.location.href = 'register_cooperative.php';
                }
            });

            // Password strength checker
            document.getElementById('password').addEventListener('input', function(e) {
                const password = e.target.value;
                const strengthBar = document.getElementById('passwordStrength');
                const strengthText = document.getElementById('passwordStrengthText');
                
                let strength = 0;
                let strengthLabel = '';
                let strengthColor = 'bg-danger';
                
                if (password.length >= 8) strength++;
                if (password.match(/[a-z]/)) strength++;
                if (password.match(/[A-Z]/)) strength++;
                if (password.match(/[0-9]/)) strength++;
                if (password.match(/[^a-zA-Z0-9]/)) strength++;
                
                switch(strength) {
                    case 0:
                    case 1:
                        strengthLabel = 'Sangat lemah';
                        strengthColor = 'bg-danger';
                        break;
                    case 2:
                        strengthLabel = 'Lemah';
                        strengthColor = 'bg-warning';
                        break;
                    case 3:
                        strengthLabel = 'Sedang';
                        strengthColor = 'bg-info';
                        break;
                    case 4:
                        strengthLabel = 'Kuat';
                        strengthColor = 'bg-primary';
                        break;
                    case 5:
                        strengthLabel = 'Sangat kuat';
                        strengthColor = 'bg-success';
                        break;
                }
                
                strengthBar.style.width = (strength * 20) + '%';
                strengthBar.className = 'password-strength-bar ' + strengthColor;
                strengthText.textContent = strengthLabel;
            });
        }
        
        // Attach register form submit listener
        function attachRegisterFormListener() {
            const registerForm = document.getElementById('registerForm');
            if (!registerForm) {
                console.error('registerForm element not found');
                return;
            }
            
            registerForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = new FormData(e.target);
                const data = Object.fromEntries(formData);
                
                // Validate passwords match
                if (data.password !== data.confirm_password) {
                    showAlert('danger', 'Password tidak cocok');
                    return;
                }
                
                // Validate phone format
                if (!/^08[0-9]{9,12}$/.test(data.phone)) {
                    showAlert('danger', 'Format nomor HP tidak valid (contoh: 08123456789)');
                    return;
                }
                
                const registerSpinner = document.getElementById('registerSpinner');
                const registerText = document.getElementById('registerText');
                
                // Show loading
                registerSpinner.classList.remove('d-none');
                registerText.textContent = 'Mendaftar...';
                
                try {
                    const response = await fetch('src/public/api/auth.php?action=register', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(data)
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        showAlert('success', 'Pendaftaran berhasil! Mengarahkan ke halaman login...');
                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 2000);
                    } else {
                        showAlert('danger', result.message || 'Pendaftaran gagal');
                    }
                } catch (error) {
                    showAlert('danger', 'Terjadi kesalahan. Silakan coba lagi.');
                } finally {
                    // Hide loading
                    registerSpinner.classList.add('d-none');
                    registerText.textContent = 'Daftar';
                }
            });
        }
        
        function showAlert(type, message) {
            const alertContainer = document.getElementById('alert-container');
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            alertContainer.appendChild(alertDiv);
            
            // Auto dismiss after 5 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
    </script>
</body>
</html>
