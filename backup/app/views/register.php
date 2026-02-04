<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Aplikasi Koperasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Registrasi</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?> <a href="/koperasi/src/public/login">Login sekarang</a></div>
                        <?php endif; ?>
                        <form method="POST" action="/koperasi/src/public/register">
                            <div class="mb-3">
                                <label for="member_nama" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="member_nama" name="member_nama" required>
                            </div>
                            <div class="mb-3">
                                <label for="member_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="member_email" name="member_email" required>
                            </div>
                            <div class="mb-3">
                                <label for="member_phone" class="form-label">Nomor Telepon</label>
                                <input type="tel" class="form-control" id="member_phone" name="member_phone">
                            </div>
                            <div class="mb-3">
                                <label for="member_password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="member_password" name="member_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="member_confirm_password" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="member_confirm_password" name="member_confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Daftar</button>
                        </form>
                        <div class="text-center mt-3">
                            <a href="/koperasi/src/public/login">Sudah punya akun? Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
