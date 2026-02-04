<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Koperasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            backdrop-filter: blur(10px);
        }
        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        .login-header h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .login-header p {
            color: #666;
            margin: 0;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
            color: white;
        }
        .btn-login:disabled {
            opacity: 0.7;
            transform: none;
            box-shadow: none;
        }
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px;
        }
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2><i class="bi bi-bank me-2"></i>Koperasi</h2>
            <p>Silakan login untuk melanjutkan</p>
            <div class="alert alert-info mt-3">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Informasi Login:</strong><br>
                Username: <code>820800</code><br>
                Password: <code>820800</code><br>
                <small>Gunakan kredensial ini untuk proses produksi</small>
            </div>
        </div>
        
        <div id="loginAlert"></div>
        
        <form id="loginForm">
            <div class="mb-3">
                <label for="username" class="form-label">Username atau Email</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" class="form-control" id="username" name="username" placeholder="820800" value="820800" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="820800" required>
                </div>
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember">
                    Ingat saya
                </label>
            </div>
            
            <button type="submit" class="btn btn-login" id="loginButton">
                <span id="loginText">Login (820800/820800)</span>
                <span id="loginSpinner" class="spinner-border spinner-border-sm ms-2 d-none"></span>
            </button>
        </form>
        
        <div class="text-center mt-3">
            <small class="text-muted">
                Belum punya akun? <a href="/ksp_peb/register.php" class="text-decoration-none">Daftar sekarang</a>
            </small>
            <div class="mt-2">
                <small class="text-muted">
                    <strong>Cara login cepat:</strong><br>
                    1. Username: <code>820800</code><br>
                    2. Password: <code>820800</code><br>
                    3. Klik tombol Login (820800/820800)<br>
                    4. Akan diarahkan ke dashboard
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Clear any existing URL parameters
        if (window.location.search || window.location.hash) {
            window.history.replaceState({}, document.title, window.location.pathname);
        }
        
        // Clear any session storage or local storage that might contain old URLs
        sessionStorage.removeItem('lastVisitedPage');
        localStorage.removeItem('redirectUrl');
        
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            const loginButton = document.getElementById('loginButton');
            const loginText = document.getElementById('loginText');
            const loginSpinner = document.getElementById('loginSpinner');
            const alertContainer = document.getElementById('loginAlert');
            
            // Clear previous alerts
            alertContainer.innerHTML = '';
            
            // Basic validation
            if (!username || !password) {
                showAlert('danger', 'Username dan password harus diisi');
                return;
            }
            
            // Show loading state
            loginButton.disabled = true;
            loginText.textContent = 'Masuk...';
            loginSpinner.classList.remove('d-none');
            
            try {
                const response = await fetch('/ksp_peb/src/public/api/auth.php?action=login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, password })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', 'Login berhasil! Mengarahkan ke dashboard...');
                    setTimeout(() => {
                        // Clean redirect without parameters
                        window.location.href = '/ksp_peb/dashboard.php';
                    }, 1500);
                } else {
                    showAlert('danger', result.message || 'Login gagal. Periksa username dan password Anda.');
                }
            } catch (error) {
                console.error('Login error:', error);
                showAlert('danger', 'Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                // Hide loading state
                loginButton.disabled = false;
                loginText.textContent = 'Login (820800/820800)';
                loginSpinner.classList.add('d-none');
            }
        });
        
        function showAlert(type, message) {
            const alertContainer = document.getElementById('loginAlert');
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
            
            alertContainer.innerHTML = `
                <div class="${alertClass} alert-dismissible fade show" role="alert">
                    <i class="bi ${icon} me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }
        
        // Auto-login untuk proses produksi
        if (window.location.search === '?production=true') {
            setTimeout(() => {
                document.getElementById('loginForm').dispatchEvent(new Event('submit'));
            }, 1000);
        }
    </script>
</body>
</html>
