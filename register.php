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
            
            <div id="alert-container"></div>
            
            <form id="registerForm">
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
                
                <button type="submit" class="btn btn-primary btn-register">
                    <span class="spinner-border spinner-border-sm d-none" id="registerSpinner"></span>
                    <span id="registerText">Daftar</span>
                </button>
            </form>
            
            <div class="login-link">
                <p>Sudah punya akun? <a href="login.php">Login disini</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
        
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
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
                const response = await fetch('../src/public/api/auth.php?action=register', {
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
