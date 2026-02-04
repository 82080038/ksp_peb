    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadProvinces();
            attachEventListeners();
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
                }
            } catch (error) {
                console.error('Error loading provinces:', error);
            }
        }

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

        // Attach all event listeners
        function attachEventListeners() {
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
                }
            });

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
