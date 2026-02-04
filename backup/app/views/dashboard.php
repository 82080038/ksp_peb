<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Aplikasi Koperasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/koperasi/src/public/dashboard">Koperasi Dashboard</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/koperasi/src/public/logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Selamat datang, <?php echo htmlspecialchars($currentUser['nama']); ?>!</h4>
                    </div>
                    <div class="card-body">
                        <h5>Informasi Akun</h5>
                        <p><strong>Nama:</strong> <?php echo htmlspecialchars($currentUser['nama']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($currentUser['email']); ?></p>
                        <p><strong>Telepon:</strong> <?php echo htmlspecialchars($currentUser['phone'] ?: 'Tidak diisi'); ?></p>
                        <p><strong>Status:</strong> <?php echo htmlspecialchars($currentUser['status']); ?></p>
                        <p><strong>Role:</strong> <?php echo implode(', ', $roles); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Menu</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><a href="/koperasi/src/public/savings">Simpanan</a></li>
                            <li class="list-group-item"><a href="/koperasi/src/public/loans">Pinjaman</a></li>
                            <li class="list-group-item"><a href="/koperasi/src/public/reports">Laporan</a> (Belum tersedia)</li>
                            <li class="list-group-item"><a href="/koperasi/src/public/voting">Voting</a> (Belum tersedia)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
