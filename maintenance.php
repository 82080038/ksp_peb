<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koperasi Simpan Pinjam - Maintenance Mode</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .maintenance-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            text-align: center;
        }
        .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .title {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        .message {
            color: #6c757d;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .contact-info {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        .progress {
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 1rem;
        }
        .progress-bar {
            height: 100%;
            background: linear-gradient(45deg, #667eea, #764ba2);
            animation: progress 3s ease-in-out infinite;
        }
        @keyframes progress {
            0% { width: 0%; }
            50% { width: 70%; }
            100% { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="icon">🔧</div>
        <h1 class="title">Sedang Dalam Perbaikan</h1>
        
        <div class="message">
            <p>Sistem Koperasi Simpan Pinjam sedang dalam maintenance untuk memberikan layanan yang lebih baik.</p>
            <p>Kami akan kembali dalam beberapa saat. Terima kasih atas pengertian Anda.</p>
        </div>
        
        <div class="progress">
            <div class="progress-bar"></div>
        </div>
        
        <div class="contact-info">
            <h5>Butuh Bantuan?</h5>
            <p><strong>Email:</strong> support@koperasi.com</p>
            <p><strong>Telepon:</strong> (021) 1234-5678</p>
            <p><strong>WhatsApp:</strong> 0812-3456-7890</p>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <button onclick="window.location.reload()" class="btn btn-outline-primary w-100">
                    🔄 Refresh Halaman
                </button>
            </div>
            <div class="col-md-6">
                <button onclick="window.history.back()" class="btn btn-outline-secondary w-100">
                    ← Kembali
                </button>
            </div>
        </div>
        
        <div class="mt-3">
            <small class="text-muted">
                Estimasi selesai: <span id="eta">--:--</span><br>
                Status: <span class="badge bg-warning text-dark">Maintenance</span>
            </small>
        </div>
    </div>

    <script>
        // Auto refresh every 30 seconds
        setInterval(function() {
            window.location.reload();
        }, 30000);

        // Update ETA
        function updateETA() {
            const now = new Date();
            const eta = new Date(now.getTime() + 15 * 60000); // 15 minutes from now
            const etaString = eta.toLocaleTimeString('id-ID', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
            document.getElementById('eta').textContent = etaString;
        }
        
        updateETA();
        setInterval(updateETA, 60000); // Update every minute

        // Prevent back button cache
        window.onload = function() {
            if (window.history.forward) {
                window.history.forward();
            }
        };
    </script>
</body>
</html>
