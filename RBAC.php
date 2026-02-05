<?php
// Role-Based Access Control (RBAC) Management Class
require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/App.php';

class RBAC {
    private $coopDB;
    
    public function __construct() {
        $app = App::getInstance();
        $this->coopDB = $app->getCoopDB();
    }
    
    // Get all roles
    public function getRoles() {
        try {
            $stmt = $this->coopDB->prepare("SELECT * FROM roles ORDER BY name");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Get role by ID
    public function getRole($id) {
        try {
            $stmt = $this->coopDB->prepare("SELECT * FROM roles WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }
    
    // Create new role
    public function createRole($name, $description = '') {
        try {
            $stmt = $this->coopDB->prepare("
                INSERT INTO roles (name, description) VALUES (?, ?)
            ");
            $result = $stmt->execute([$name, $description]);
            
            if ($result) {
                $roleId = $this->coopDB->lastInsertId();
                return ['success' => true, 'role_id' => $roleId];
            }
            return ['success' => false, 'message' => 'Failed to create role'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Update role
    public function updateRole($id, $name, $description = '') {
        try {
            $stmt = $this->coopDB->prepare("
                UPDATE roles SET name = ?, description = ? WHERE id = ?
            ");
            $result = $stmt->execute([$name, $description, $id]);
            
            return ['success' => $result];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Delete role
    public function deleteRole($id) {
        try {
            // Check if role is assigned to users
            $checkStmt = $this->coopDB->prepare("SELECT COUNT(*) FROM user_roles WHERE role_id = ?");
            $checkStmt->execute([$id]);
            $userCount = $checkStmt->fetchColumn();
            
            if ($userCount > 0) {
                return ['success' => false, 'message' => 'Cannot delete role: still assigned to users'];
            }
            
            $stmt = $this->coopDB->prepare("DELETE FROM roles WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            return ['success' => $result];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Get all permissions
    public function getPermissions() {
        try {
            $stmt = $this->coopDB->prepare("SELECT * FROM permissions ORDER BY name");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Get permissions for role
    public function getRolePermissions($roleId) {
        try {
            $stmt = $this->coopDB->prepare("
                SELECT p.* FROM permissions p
                JOIN role_permissions rp ON p.id = rp.permission_id
                WHERE rp.role_id = ?
                ORDER BY p.name
            ");
            $stmt->execute([$roleId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Assign permission to role
    public function assignPermission($roleId, $permissionId) {
        try {
            $stmt = $this->coopDB->prepare("
                INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?, ?)
            ");
            return $stmt->execute([$roleId, $permissionId]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Remove permission from role
    public function removePermission($roleId, $permissionId) {
        try {
            $stmt = $this->coopDB->prepare("
                DELETE FROM role_permissions WHERE role_id = ? AND permission_id = ?
            ");
            return $stmt->execute([$roleId, $permissionId]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Get user roles
    public function getUserRoles($userId) {
        try {
            $stmt = $this->coopDB->prepare("
                SELECT r.* FROM roles r
                JOIN user_roles ur ON r.id = ur.role_id
                WHERE ur.user_id = ?
                ORDER BY r.name
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Assign role to user
    public function assignRole($userId, $roleId) {
        try {
            $stmt = $this->coopDB->prepare("
                INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (?, ?)
            ");
            return $stmt->execute([$userId, $roleId]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Remove role from user
    public function removeRole($userId, $roleId) {
        try {
            $stmt = $this->coopDB->prepare("
                DELETE FROM user_roles WHERE user_id = ? AND role_id = ?
            ");
            return $stmt->execute([$userId, $roleId]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Check if user has specific permission
    public function userHasPermission($userId, $permission) {
        try {
            $stmt = $this->coopDB->prepare("
                SELECT COUNT(*) as count FROM permissions p
                JOIN role_permissions rp ON p.id = rp.permission_id
                JOIN roles r ON rp.role_id = r.id
                JOIN user_roles ur ON r.id = ur.role_id
                WHERE ur.user_id = ? AND p.name = ?
            ");
            $stmt->execute([$userId, $permission]);
            $result = $stmt->fetch();
            
            return $result['count'] > 0;
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Get all users with their roles
    public function getUsersWithRoles() {
        try {
            $stmt = $this->coopDB->prepare("
                SELECT DISTINCT u.id, u.nama, u.email, u.phone, u.status,
                       GROUP_CONCAT(r.name SEPARATOR ', ') as roles
                FROM people_db.users u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                GROUP BY u.id, u.nama, u.email, u.phone, u.status
                ORDER BY u.nama
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Initialize default permissions and roles
    public function initializeDefaults() {
        try {
            // Default permissions
            $permissions = [
                'view_users' => 'View user list',
                'create_users' => 'Create new users',
                'edit_users' => 'Edit user information',
                'delete_users' => 'Delete users',
                'manage_roles' => 'Manage roles and permissions',
                'view_members' => 'View members',
                'manage_members' => 'Manage member data',
                'view_savings' => 'View savings transactions',
                'manage_savings' => 'Manage savings',
                'view_loans' => 'View loan applications',
                'manage_loans' => 'Manage loans',
                'approve_loans' => 'Approve loan applications',
                'view_accounts' => 'View chart of accounts',
                'manage_accounts' => 'Manage accounting',
                'post_journal' => 'Post journal entries',
                'view_reports' => 'View reports',
                'generate_reports' => 'Generate financial reports',
                'vote' => 'Participate in voting',
                'manage_votes' => 'Manage voting sessions',
                'view_audit' => 'View audit logs',
                'admin_access' => 'Full administrative access',
                'pengawas_access' => 'Supervisor access (read/approve only)'
            ];
            
            foreach ($permissions as $name => $description) {
                $stmt = $this->coopDB->prepare("
                    INSERT IGNORE INTO permissions (name, description) VALUES (?, ?)
                ");
                $stmt->execute([$name, $description]);
            }
            
            // Default roles with permissions
            $rolePermissions = [
                'super_admin' => array_keys($permissions),
                'admin' => [
                    'view_users', 'create_users', 'edit_users', 'view_members', 'manage_members',
                    'view_savings', 'manage_savings', 'view_loans', 'manage_loans', 'approve_loans',
                    'view_accounts', 'manage_accounts', 'post_journal', 'view_reports', 'generate_reports',
                    'manage_votes', 'view_audit', 'admin_access'
                ],
                'pengawas' => [
                    'view_users', 'view_members', 'view_savings', 'view_loans', 'view_accounts',
                    'view_reports', 'generate_reports', 'vote', 'manage_votes', 'view_audit', 'pengawas_access'
                ],
                'anggota' => ['vote'],
                'calon_anggota' => []
            ];
            
            foreach ($rolePermissions as $roleName => $permissionNames) {
                // Get or create role
                $stmt = $this->coopDB->prepare("SELECT id FROM roles WHERE name = ?");
                $stmt->execute([$roleName]);
                $role = $stmt->fetch();
                
                if (!$role) {
                    $insertStmt = $this->coopDB->prepare("
                        INSERT INTO roles (name, description) VALUES (?, ?)
                    ");
                    $insertStmt->execute([$roleName, ucfirst(str_replace('_', ' ', $roleName))]);
                    $roleId = $this->coopDB->lastInsertId();
                } else {
                    $roleId = $role['id'];
                }
                
                // Assign permissions
                foreach ($permissionNames as $permissionName) {
                    $permStmt = $this->coopDB->prepare("SELECT id FROM permissions WHERE name = ?");
                    $permStmt->execute([$permissionName]);
                    $permission = $permStmt->fetch();
                    
                    if ($permission) {
                        $assignStmt = $this->coopDB->prepare("
                            INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?, ?)
                        ");
                        $assignStmt->execute([$roleId, $permission['id']]);
                    }
                }
            }
            
            return ['success' => true, 'message' => 'RBAC initialized successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
