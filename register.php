<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Koperasi</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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
        .location-selection, .cooperativeSelection {
            margin-bottom: 2rem;
        }
        .password-strength-bar {
            height: 8px;
            border-radius: 4px;
            transition: width 0.3s ease;
        }
    </style>
    <link href="src/public/css/form-helper.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="register-container">
            <div class="register-header">
                <h2>Registrasi Anggota Koperasi</h2>
                <p class="text-muted">Pilih lokasi koperasi Anda</p>
            </div>

            <!-- Alert container -->
            <div id="alert-container"></div>

            <!-- Location Selection -->
            <div class="location-selection">
                <div class="row g-3">
                    <div class="col-12">
                        <label for="province" class="form-label">Provinsi</label>
                        <select class="form-select" id="province" required>
                            <option value="">Pilih Provinsi</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="regency" class="form-label">Kabupaten/Kota</label>
                        <select class="form-select" id="regency" disabled required>
                            <option value="">Pilih Kabupaten/Kota</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="district" class="form-label">Kecamatan</label>
                        <select class="form-select" id="district" disabled required>
                            <option value="">Pilih Kecamatan</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Cooperative Selection -->
            <div id="cooperativeSelection" style="display: none;">
                <h5 class="mb-3">Pilih Koperasi</h5>
                
                <!-- Loading indicator -->
                <div id="cooperativeLoading" class="text-center mb-3" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Memuat...</span>
                    </div>
                    <span class="ms-2">Memuat data koperasi...</span>
                </div>
                
                <div class="mb-3">
                    <label for="cooperative" class="form-label">Koperasi</label>
                    <select class="form-select" id="cooperative">
                        <option value="">Pilih Koperasi</option>
                    </select>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary" id="selectCooperative" disabled style="display: none;">
                        Pilih Koperasi Ini
                    </button>
                </div>
                
                <div id="noCooperativeMessage" class="alert alert-info mt-3" style="display: none;">
                    <p class="mb-2">Belum ada koperasi di lokasi ini.</p>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="createCooperative">
                        Buat Koperasi Baru
                    </button>
                </div>
            </div>

            <!-- Registration Form -->
            <form id="registrationForm" style="display: none;">
                <h5 class="mb-3">Informasi Pendaftaran</h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="member_name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="member_name" name="member_name" required tabindex="1">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="member_phone" class="form-label">Nomor HP</label>
                            <input type="tel" class="form-control" id="member_phone" name="member_phone" placeholder="08123456789" required tabindex="2">
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="member_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="member_email" name="member_email" required tabindex="3">
                    </div>
                    <div class="col-12">
                        <label for="member_username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="member_username" name="member_username" required tabindex="4">
                    </div>
                    <div class="col-12">
                        <label for="member_password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="member_password" name="member_password" required tabindex="5">
                        <div class="progress mt-1" style="height: 8px;">
                            <div class="password-strength-bar bg-danger" id="member_passwordStrength" style="width: 0%;"></div>
                        </div>
                        <small id="member_passwordStrengthText" class="text-muted"></small>
                    </div>
                    <div class="col-12">
                        <label for="member_confirm_password" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="member_confirm_password" name="member_confirm_password" required tabindex="6">
                    </div>
                    <div class="col-12">
                        <label for="member_village" class="form-label">Desa/Kelurahan</label>
                        <select class="form-select" id="member_village" name="member_village_id" required tabindex="7">
                            <option value="">Pilih Desa/Kelurahan</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="member_full_address" class="form-label">Alamat Lengkap</label>
                        <textarea class="form-control" id="member_full_address" name="member_full_address" rows="3" placeholder="Jl. Contoh No. 123, RT/RW 01/02" required tabindex="8"></textarea>
                    </div>
                    <!-- Hidden fields for address -->
                    <input type="hidden" id="member_address_province" name="member_address_province">
                    <input type="hidden" id="member_address_regency" name="member_address_regency">
                    <input type="hidden" id="member_address_district" name="member_address_district">
                </div>
                
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-success" id="registerButton">
                        <span class="spinner-border spinner-border-sm d-none" id="registerSpinner" role="status"></span>
                        <span id="registerText">Daftar</span>
                    </button>
                </div>
            </form>

            <div class="login-link text-center mt-3">
                <p>Sudah punya akun? <a href="/ksp_peb/login.php">Login disini</a></p>
                <p><a href="/ksp_peb/register_cooperative.php">Buat Koperasi Baru</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="src/public/js/form-helper.js"></script>
    <script src="src/public/js/avoid-next-error.js"></script>
    <script>
        // Global variable to store selected cooperative
        let selectedCooperativeId = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Reset registration form on page load (keep location selections)
            FormHelper.resetFormFields('registrationForm', ['province', 'regency', 'district']);
            
            loadProvinces();
            attachEventListeners();
            
            // Setup ENTER key navigation for the form
            FormHelper.setupEnterKeyNavigation('registrationForm', 'registerButton');
        });

        // Load provinces on page load
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
                    
                    // Setup focus dropdown behavior for province select
                    setupFocusDropdown('province');
                }
            } catch (error) {
                console.error('Error loading provinces:', error);
            }
        }

        // Load villages for selected district
        async function loadVillages(districtId) {
            if (!districtId) return;
            const villageSelect = document.getElementById('member_village');
            const postalInput = document.getElementById('postal_code');
            villageSelect.innerHTML = '<option value="">Memuat desa...</option>';
            try {
                const response = await fetch(`src/public/api/cooperative.php?action=villages&district_id=${districtId}`);
                const result = await response.json();
                if (result.success) {
                    villageSelect.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';
                    result.data.forEach(village => {
                        villageSelect.innerHTML += `<option value="${village.id}" data-postal="${village.postal_code}">${village.name}</option>`;
                    });
                    
                    // Setup focus dropdown behavior for village select
                    setupFocusDropdown('member_village');
                }
            } catch (error) {
                console.error('Error loading villages:', error);
                villageSelect.innerHTML = '<option value="">Gagal memuat desa</option>';
            }
        }

        // Attach event listeners
        function attachEventListeners() {
            // Province change handler
            document.getElementById('province').addEventListener('change', async function() {
                const provinceId = this.value;
                const regencySelect = document.getElementById('regency');
                const districtSelect = document.getElementById('district');
                
                // Reset dependent selects
                regencySelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
                regencySelect.disabled = true;
                districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                districtSelect.disabled = true;
                
                if (provinceId) {
                    try {
                        const response = await fetch(`src/public/api/cooperative.php?action=regencies&province_id=${provinceId}`);
                        const result = await response.json();
                        if (result.success) {
                            regencySelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
                            result.data.forEach(regency => {
                                regencySelect.innerHTML += `<option value="${regency.id}">${regency.name}</option>`;
                            });
                            regencySelect.disabled = false;
                            
                            // Setup focus dropdown behavior for regency select
                            setupFocusDropdown('regency');
                        }
                    } catch (error) {
                        console.error('Error loading regencies:', error);
                    }
                }
            });

            // Regency change handler
            document.getElementById('regency').addEventListener('change', async function() {
                const regencyId = this.value;
                const districtSelect = document.getElementById('district');
                
                // Reset dependent selects
                districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                districtSelect.disabled = true;
                
                if (regencyId) {
                    try {
                        const response = await fetch(`src/public/api/cooperative.php?action=districts&regency_id=${regencyId}`);
                        const result = await response.json();
                        if (result.success) {
                            districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                            result.data.forEach(district => {
                                districtSelect.innerHTML += `<option value="${district.id}">${district.name}</option>`;
                            });
                            districtSelect.disabled = false;
                            
                            // Setup focus dropdown behavior for district select
                            setupFocusDropdown('district');
                        }
                    } catch (error) {
                        console.error('Error loading districts:', error);
                        districtSelect.innerHTML = '<option value="">Gagal memuat kecamatan</option>';
                    }
                }
            });

            // District change handler
            document.getElementById('district').addEventListener('change', async function() {
                const districtId = this.value;
                
                if (districtId) {
                    try {
                        // Show cooperative selection and loading
                        document.getElementById('cooperativeSelection').style.display = 'block';
                        document.getElementById('cooperativeLoading').style.display = 'block';
                        document.getElementById('noCooperativeMessage').style.display = 'none';
                        
                        // Load villages and cooperatives in parallel
                        const [villagesResult, cooperativesResult] = await Promise.all([
                            fetch(`src/public/api/cooperative.php?action=villages&district_id=${districtId}`),
                            fetch(`src/public/api/cooperative.php?action=cooperatives_by_district&district_id=${districtId}`)
                        ]);
                        
                        const villages = await villagesResult.json();
                        const cooperatives = await cooperativesResult.json();
                        
                        // Hide loading
                        document.getElementById('cooperativeLoading').style.display = 'none';
                        
                        // Handle villages
                        const villageSelect = document.getElementById('member_village');
                        if (villages.success) {
                            villageSelect.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';
                            villages.data.forEach(village => {
                                villageSelect.innerHTML += `<option value="${village.id}" data-postal="${village.postal_code}">${village.name}</option>`;
                            });
                            
                            // Setup focus dropdown behavior for village select
                            setupFocusDropdown('member_village');
                        }
                        
                        // Handle cooperatives
                        const cooperativeSelect = document.getElementById('cooperative');
                        if (cooperatives.success && cooperatives.data.length > 0) {
                            cooperativeSelect.innerHTML = '<option value="">Pilih Koperasi</option>';
                            cooperatives.data.forEach(cooperative => {
                                cooperativeSelect.innerHTML += `<option value="${cooperative.id}">${cooperative.nama}</option>`;
                            });
                            
                            // Setup focus dropdown behavior for cooperative select
                            setupFocusDropdown('cooperative');
                            
                            document.getElementById('selectCooperative').disabled = false;
                            document.getElementById('selectCooperative').style.display = 'block';
                            document.getElementById('noCooperativeMessage').style.display = 'none';
                        } else {
                            cooperativeSelect.innerHTML = '<option value="">Tidak ada koperasi di kecamatan ini</option>';
                            document.getElementById('selectCooperative').disabled = true;
                            document.getElementById('selectCooperative').style.display = 'none';
                            document.getElementById('noCooperativeMessage').style.display = 'block';
                        }
                    } catch (error) {
                        console.error('Error loading district data:', error);
                        document.getElementById('cooperativeLoading').style.display = 'none';
                        // Show user-friendly error
                    }
                } else {
                    // Hide cooperative selection if no district selected
                    document.getElementById('cooperativeSelection').style.display = 'none';
                }
            });

            // Village change handler
            document.getElementById('member_village').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const postalCode = selectedOption.getAttribute('data-postal');
                const postalInput = document.getElementById('postal_code');
                if (postalInput) {
                    postalInput.value = postalCode || '';
                }
            });

            // Select cooperative button
            document.getElementById('selectCooperative').addEventListener('click', function() {
                const cooperativeSelect = document.getElementById('cooperative');
                selectedCooperativeId = cooperativeSelect.value;
                
                if (selectedCooperativeId) {
                    // Show registration form
                    document.getElementById('cooperativeSelection').style.display = 'none';
                    document.getElementById('registrationForm').style.display = 'block';
                    
                    // Reset form fields
                    FormHelper.resetFormFields('registrationForm', ['province', 'regency', 'district']);
                    
                    // Scroll to form
                    document.getElementById('registrationForm').scrollIntoView({ behavior: 'smooth' });
                }
            });

            // Create cooperative button
            document.getElementById('createCooperative').addEventListener('click', function() {
                // Store location data in localStorage
                const locationData = {
                    province_id: document.getElementById('province').value,
                    province_name: document.getElementById('province').options[document.getElementById('province').selectedIndex].text,
                    regency_id: document.getElementById('regency').value,
                    regency_name: document.getElementById('regency').options[document.getElementById('regency').selectedIndex].text,
                    district_id: document.getElementById('district').value,
                    district_name: document.getElementById('district').options[document.getElementById('district').selectedIndex].text
                };
                
                localStorage.setItem('cooperativeLocationData', JSON.stringify(locationData));
                
                // Redirect to cooperative registration page
                window.location.href = '/ksp_peb/register_cooperative.php';
            });

            // Registration form submit
            document.getElementById('registrationForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Define field validation rules
                const fieldRules = {
                    'member_name': {
                        label: 'Nama Lengkap',
                        required: true,
                        elementId: 'member_name'
                    },
                    'member_phone': {
                        label: 'Nomor HP',
                        required: true,
                        type: 'phone',
                        elementId: 'member_phone'
                    },
                    'member_email': {
                        label: 'Email',
                        required: true,
                        type: 'email',
                        elementId: 'member_email'
                    },
                    'member_username': {
                        label: 'Username',
                        required: true,
                        minLength: 4,
                        maxLength: 20,
                        elementId: 'member_username'
                    },
                    'member_password': {
                        label: 'Password',
                        required: true,
                        minLength: 6,
                        elementId: 'member_password'
                    },
                    'member_confirm_password': {
                        label: 'Konfirmasi Password',
                        required: true,
                        elementId: 'member_confirm_password',
                        validate: (value) => {
                            const password = document.getElementById('member_password').value;
                            return value === password || 'Password tidak cocok';
                        }
                    },
                    'member_village_id': {
                        label: 'Desa/Kelurahan',
                        required: true,
                        elementId: 'member_village'
                    },
                    'member_full_address': {
                        label: 'Alamat Lengkap',
                        required: true,
                        elementId: 'member_full_address'
                    }
                };
                
                // Validate form
                const validation = FormHelper.validateForm('registrationForm', fieldRules);
                
                if (!validation.isValid) {
                    const errorMessage = FormHelper.showFormErrors(validation.errors);
                    FormHelper.showAlert('danger', errorMessage);
                    return;
                }
                
                const data = validation.data;
                
                // Add location data
                data.member_address_province = document.getElementById('province').options[document.getElementById('province').selectedIndex].text;
                data.member_address_regency = document.getElementById('regency').options[document.getElementById('regency').selectedIndex].text;
                data.member_address_district = document.getElementById('district').options[document.getElementById('district').selectedIndex].text;
                data.cooperative_id = selectedCooperativeId;
                
                const registerSpinner = document.getElementById('registerSpinner');
                const registerText = document.getElementById('registerText');
                
                // Show loading
                registerSpinner.classList.remove('d-none');
                registerText.textContent = 'Mendaftar...';
                
                // Disable submit button during loading
                document.getElementById('registerButton').disabled = true;
                
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
                        FormHelper.showAlert('success', 'Pendaftaran berhasil! Mengarahkan ke halaman login...');
                        setTimeout(() => {
                            window.location.href = '/ksp_peb/login.php';
                        }, 2000);
                    } else {
                        FormHelper.showAlert('danger', result.message || 'Pendaftaran gagal');
                    }
                } catch (error) {
                    FormHelper.showAlert('danger', 'Terjadi kesalahan. Silakan coba lagi.');
                } finally {
                    // Hide loading
                    registerSpinner.classList.add('d-none');
                    registerText.textContent = 'Daftar';
                    
                    // Re-enable submit button
                    document.getElementById('registerButton').disabled = false;
                }
            });

            // Auto-CamelCase for member_full_address on blur
            const memberFullAddressInput = document.getElementById('member_full_address');
            if (memberFullAddressInput) {
                memberFullAddressInput.addEventListener('blur', () => {
                    if (memberFullAddressInput.value) {
                        // Convert to Camel Case: "jalan sudirman no 123" -> "Jalan Sudirman No 123"
                        let value = memberFullAddressInput.value.toLowerCase();
                        value = value.replace(/\b\w/g, function(match) {
                            return match.toUpperCase();
                        });
                        memberFullAddressInput.value = value;
                    }
                });
            }

            // Auto-uppercase for member_name on blur
            const memberNameInput = document.getElementById('member_name');
            if (memberNameInput) {
                memberNameInput.addEventListener('blur', () => {
                    if (memberNameInput.value) {
                        memberNameInput.value = memberNameInput.value.toUpperCase();
                    }
                });
            }
        } // Closing bracket for attachEventListeners function
    </script>
</body>
</html>
