<?php
// User Management Page
require_once __DIR__ . '/../../../bootstrap.php';

$auth = new Auth();
if (!$auth->isLoggedIn() || !$auth->hasPermission('admin_access')) {
    header('Location: /ksp_peb/login.php');
    exit;
}

$app = App::getInstance();
$coopDB = $app->getCoopDB();
$peopleDB = $app->getPeopleDB();

// Get all users with their roles
try {
    $stmt = $coopDB->prepare("
        SELECT cu.id as coop_user_id, cu.username, cu.status as coop_status, cu.created_at as coop_created,
               u.id as people_user_id, u.nama, u.email, u.phone, u.status as people_status,
               GROUP_CONCAT(r.name) as roles
        FROM users cu
        LEFT JOIN people_db.users u ON cu.user_db_id = u.id
        LEFT JOIN user_roles ur ON cu.id = ur.user_id
        LEFT JOIN roles r ON ur.role_id = r.id
        GROUP BY cu.id, cu.username, cu.status, cu.created_at, u.id, u.nama, u.email, u.phone, u.status
        ORDER BY cu.created_at DESC
    ");
    $stmt->execute();
    $users = $stmt->fetchAll();
} catch (Exception $e) {
    $users = [];
}

// Get available roles
try {
    $stmt = $coopDB->prepare("SELECT * FROM roles ORDER BY name");
    $stmt->execute();
    $roles = $stmt->fetchAll();
} catch (Exception $e) {
    $roles = [];
}
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manajemen User</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-person-plus me-2"></i>Tambah User
            </button>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Daftar User</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($users)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Roles</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['nama'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($user['email'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                            <td>
                                <?php if ($user['roles']): ?>
                                    <?php foreach (explode(',', $user['roles']) as $role): ?>
                                        <span class="badge bg-primary me-1"><?php echo htmlspecialchars($role); ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted">No roles</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $user['coop_status'] === 'active' ? 'success' : 'danger'; ?>">
                                    <?php echo htmlspecialchars($user['coop_status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($user['coop_created'])); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="editUser(<?php echo $user['coop_user_id']; ?>)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="deleteUser(<?php echo $user['coop_user_id']; ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center">Belum ada user</p>
        <?php endif; ?>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah User Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username *</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Lengkap *</label>
                                <input type="text" class="form-control" id="nama" name="nama" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone *</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="roles" class="form-label">Roles *</label>
                                <select class="form-select" id="roles" name="roles[]" multiple required>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="addUser()">Tambah User</button>
            </div>
        </div>
    </div>
</div>

<script>
function addUser() {
    const form = document.getElementById('addUserForm');
    const formData = new FormData(form);
    
    fetch('src/public/api/management.php?type=user&action=create', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('User berhasil ditambahkan');
            location.reload();
        } else {
            alert(data.message || 'Gagal menambah user');
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan. Silakan coba lagi.');
    });
}

function editUser(userId) {
    // TODO: Implement edit user functionality
    alert('Edit user functionality coming soon');
}

function deleteUser(userId) {
    if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
        fetch(`src/public/api/management.php?type=user&action=delete&id=${userId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('User berhasil dihapus');
                location.reload();
            } else {
                alert(data.message || 'Gagal menghapus user');
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan. Silakan coba lagi.');
        });
    }
}
</script>
