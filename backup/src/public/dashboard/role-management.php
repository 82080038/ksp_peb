<?php
// Role Management Page
require_once __DIR__ . '/../../../bootstrap.php';

$auth = new Auth();
if (!$auth->isLoggedIn() || !$auth->hasPermission('admin_access')) {
    header('Location: /ksp_peb/login.php');
    exit;
}

$app = App::getInstance();
$coopDB = $app->getCoopDB();

// Get all roles with user count
try {
    $stmt = $coopDB->prepare("
        SELECT r.*, COUNT(ur.user_id) as user_count
        FROM roles r
        LEFT JOIN user_roles ur ON r.id = ur.role_id
        GROUP BY r.id
        ORDER BY r.name
    ");
    $stmt->execute();
    $roles = $stmt->fetchAll();
} catch (Exception $e) {
    $roles = [];
}

// Get all permissions
try {
    $stmt = $coopDB->prepare("SELECT * FROM permissions ORDER BY name");
    $stmt->execute();
    $permissions = $stmt->fetchAll();
} catch (Exception $e) {
    $permissions = [];
}
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manajemen Role</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                <i class="bi bi-plus-circle me-2"></i>Tambah Role
            </button>
        </div>
    </div>
</div>

<!-- Roles Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Daftar Role</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($roles)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama Role</th>
                            <th>Deskripsi</th>
                            <th>Jumlah User</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles as $role): ?>
                        <tr>
                            <td>
                                <span class="badge bg-primary"><?php echo htmlspecialchars($role['name']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($role['description']); ?></td>
                            <td>
                                <span class="badge bg-info"><?php echo $role['user_count']; ?> user</span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($role['created_at'])); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="editRole(<?php echo $role['id']; ?>)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-info" onclick="viewPermissions(<?php echo $role['id']; ?>)">
                                        <i class="bi bi-shield-check"></i>
                                    </button>
                                    <?php if ($role['user_count'] == 0): ?>
                                    <button class="btn btn-outline-danger" onclick="deleteRole(<?php echo $role['id']; ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center">Belum ada role</p>
        <?php endif; ?>
    </div>
</div>

<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Role Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addRoleForm">
                    <div class="mb-3">
                        <label for="roleName" class="form-label">Nama Role *</label>
                        <input type="text" class="form-control" id="roleName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="roleDescription" class="form-label">Deskripsi *</label>
                        <textarea class="form-control" id="roleDescription" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="rolePermissions" class="form-label">Permissions</label>
                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                            <?php foreach ($permissions as $permission): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="<?php echo $permission['id']; ?>" 
                                           id="perm_<?php echo $permission['id']; ?>" name="permissions[]">
                                    <label class="form-check-label" for="perm_<?php echo $permission['id']; ?>">
                                        <?php echo htmlspecialchars($permission['name']); ?>
                                        <small class="text-muted d-block"><?php echo htmlspecialchars($permission['description']); ?></small>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="addRole()">Tambah Role</button>
            </div>
        </div>
    </div>
</div>

<!-- Permissions Modal -->
<div class="modal fade" id="permissionsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Role Permissions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="permissionsContent">
                    <!-- Permissions will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function addRole() {
    const form = document.getElementById('addRoleForm');
    const formData = new FormData(form);
    
    fetch('src/public/api/management.php?type=role&action=create', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Role berhasil ditambahkan');
            location.reload();
        } else {
            alert(data.message || 'Gagal menambah role');
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan. Silakan coba lagi.');
    });
}

function editRole(roleId) {
    // TODO: Implement edit role functionality
    alert('Edit role functionality coming soon');
}

function viewPermissions(roleId) {
    fetch(`src/public/api/management.php?type=role&action=permissions&id=${roleId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const content = data.permissions.map(perm => `
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <div>
                        <strong>${perm.name}</strong>
                        <br><small class="text-muted">${perm.description}</small>
                    </div>
                    <span class="badge bg-success">Assigned</span>
                </div>
            `).join('');
            
            document.getElementById('permissionsContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('permissionsModal')).show();
        } else {
            alert(data.message || 'Gagal memuat permissions');
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan. Silakan coba lagi.');
    });
}

function deleteRole(roleId) {
    if (confirm('Apakah Anda yakin ingin menghapus role ini?')) {
        fetch(`src/public/api/management.php?type=role&action=delete&id=${roleId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Role berhasil dihapus');
                location.reload();
            } else {
                alert(data.message || 'Gagal menghapus role');
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan. Silakan coba lagi.');
        });
    }
}
</script>
