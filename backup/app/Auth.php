<?php
// Authentication and User Management Class
class Auth {
    private $db;
    private $coopDB;
    
    public function __construct() {
        $app = App::getInstance();
        $this->db = $app->getDB();
        $this->coopDB = $app->getCoopDB();
    }
    
    // Hash password using bcrypt
    public function hashPassword($password) {
        $cost = intval(Environment::get('HASH_COST', 12));
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => $cost]);
    }
    
    // Verify password
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    // Register new user
    public function register($data) {
        try {
            // Validate required fields
            $required = ['member_name', 'member_email', 'member_password', 'member_phone', 'member_village_id', 'member_full_address'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return ['success' => false, 'message' => "Field $field is required"];
                }
            }
            
            // Check if email exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$data['member_email']]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email already exists'];
            }
            
            // Check if phone exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE phone = ?");
            $stmt->execute([$data['member_phone']]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Phone number already exists'];
            }
            
            // Hash password
            $hashedPassword = $this->hashPassword($data['member_password']);
            
            // Insert user
            $stmt = $this->db->prepare("
                INSERT INTO users (nama, email, phone, password_hash, status) 
                VALUES (?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([$data['member_name'], $data['member_email'], $data['member_phone'], $hashedPassword]);
            $userId = $this->db->lastInsertId();
            
            // Add identity record if provided
            if (!empty($data['nik'])) {
                $stmt = $this->db->prepare("
                    INSERT INTO identities (user_id, nik) VALUES (?, ?)
                ");
                $stmt->execute([$userId, $data['nik']]);
            }
            
            // Add address record
            $stmt = $this->db->prepare("
                INSERT INTO addresses (user_id, village_id, full_address) VALUES (?, ?, ?)
            ");
            $stmt->execute([$userId, $data['member_village_id'], $data['member_full_address']]);
            
            // Assign default role (calon_anggota)
            $roleStmt = $this->coopDB->prepare("SELECT id FROM roles WHERE name = 'calon_anggota'");
            $roleStmt->execute();
            $role = $roleStmt->fetch();
            
            if ($role) {
                $userRoleStmt = $this->coopDB->prepare("
                    INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)
                ");
                $userRoleStmt->execute([$userId, $role['id']]);
            }
            
            return ['success' => true, 'user_id' => $userId, 'message' => 'Registration successful'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }
    
    // Login user (supports both email and username login)
    public function login($identifier, $password) {
        try {
            // Rate limiting check
            if (!Security::checkRateLimit('login', 5, 300)) {
                return ['success' => false, 'message' => 'Too many login attempts. Please try again later.'];
            }
            
            // Sanitize input
            $identifier = Security::sanitize($identifier);
            
            // First try to find user by username in coop_db (for cooperative admins)
            $coopStmt = $this->coopDB->prepare("
                SELECT cu.id, cu.username, cu.user_db_id, cu.status as coop_status
                FROM users cu
                WHERE cu.username = ? AND cu.status = 'active'
            ");
            $coopStmt->execute([$identifier]);
            $coopUser = $coopStmt->fetch();

            if ($coopUser) {
                // Found cooperative user, now get people_db user details
                $stmt = $this->db->prepare("
                    SELECT u.*, 'N/A' as nik 
                    FROM users u 
                    WHERE u.id = ? AND u.status = 'active'
                ");
                $stmt->execute([$coopUser['user_db_id']]);
                $user = $stmt->fetch();
                
                if (!$user) {
                    return ['success' => false, 'message' => 'User account not found'];
                }
            } else {
                // Try login by email in people_db (fallback for regular users)
                $stmt = $this->db->prepare("
                    SELECT u.*, 'N/A' as nik 
                    FROM users u 
                    WHERE u.email = ? AND u.status = 'active'
                ");
                $stmt->execute([$identifier]);
                $user = $stmt->fetch();
                
                if (!$user) {
                    // Try login by phone
                    $stmt = $this->db->prepare("
                        SELECT u.*, 'N/A' as nik 
                        FROM users u 
                        WHERE u.phone = ? AND u.status = 'active'
                    ");
                    $stmt->execute([$identifier]);
                    $user = $stmt->fetch();
                    
                    if (!$user) {
                        return ['success' => false, 'message' => 'Invalid credentials or inactive account'];
                    }
                }
            }

            if (!$this->verifyPassword($password, $user['password_hash'])) {
                return ['success' => false, 'message' => 'Invalid credentials'];
            }

            // Get user roles
            $roleStmt = $this->coopDB->prepare("
                SELECT r.name, r.description 
                FROM roles r 
                JOIN user_roles ur ON r.id = ur.role_id 
                WHERE ur.user_id = ?
            ");
            $roleStmt->execute([$coopUser ? $coopUser['id'] : $user['id']]);
            $roles = $roleStmt->fetchAll();
            
            // Set session
            $app = App::getInstance();
            $app->startSession();
            $_SESSION['user_id'] = $user['id']; // people_db user id
            $_SESSION['coop_user_id'] = $coopUser ? $coopUser['id'] : null; // coop_db user id
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['nama'];
            $_SESSION['user_roles'] = $roles;
            $_SESSION['login_time'] = time();

            // Auto-assign cooperative_id for admin users (latest created by this coop user)
            if ($coopUser && empty($_SESSION['cooperative_id'])) {
                try {
                    $coopStmt = $this->coopDB->prepare("SELECT id FROM cooperatives WHERE created_by = ? ORDER BY id DESC LIMIT 1");
                    $coopStmt->execute([$coopUser['id']]);
                    $coopRecord = $coopStmt->fetch();
                    if ($coopRecord) {
                        $_SESSION['cooperative_id'] = $coopRecord['id'];
                    }
                } catch (Exception $e) {
                    // Ignore if cooperative not found
                }
            }
            
            // Log login
            $this->logActivity($user['id'], 'login', [
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
            
            return [
                'success' => true, 
                'user' => [
                    'id' => $user['id'],
                    'nama' => $user['nama'],
                    'email' => $user['email'],
                    'phone' => $user['phone'],
                    'nik' => $user['nik'],
                    'roles' => $roles
                ]
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Login failed: ' . $e->getMessage()];
        }
    }
    
    // Logout user
    public function logout() {
        $app = App::getInstance();
        $app->startSession();
        
        if (isset($_SESSION['user_id'])) {
            $this->logActivity($_SESSION['user_id'], 'logout', [
                'session_duration' => time() - $_SESSION['login_time']
            ]);
        }
        
        session_destroy();
        return ['success' => true, 'message' => 'Logged out successfully'];
    }
    
    // Check if user is logged in
    public function isLoggedIn() {
        $app = App::getInstance();
        $app->startSession();
        return isset($_SESSION['user_id']);
    }
    
    // Get current user
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        $app = App::getInstance();
        $app->startSession();
        
        try {
            $stmt = $this->db->prepare("
                SELECT u.id, u.nama, u.email, u.phone, u.status, 'N/A' as nik 
                FROM users u 
                WHERE u.id = ?
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Get roles - use coop_user_id if available (for cooperative users)
                $roleUserId = $_SESSION['coop_user_id'] ?? $_SESSION['user_id'];
                $roleStmt = $this->coopDB->prepare("
                    SELECT r.name, r.description 
                    FROM roles r 
                    JOIN user_roles ur ON r.id = ur.role_id 
                    WHERE ur.user_id = ?
                ");
                $roleStmt->execute([$roleUserId]);
                $user['roles'] = $roleStmt->fetchAll();
            }
            
            return $user;
            
        } catch (Exception $e) {
            return null;
        }
    }
    
    // Check user permission
    public function hasPermission($permission) {
        $user = $this->getCurrentUser();
        if (!$user || empty($user['roles'])) {
            return false;
        }
        
        $roleNames = array_column($user['roles'], 'name');
        
        // Super admin has all permissions
        if (in_array('super_admin', $roleNames)) {
            return true;
        }
        
        // Check specific permission
        try {
            $placeholders = str_repeat('?,', count($roleNames) - 1) . '?';
            $stmt = $this->coopDB->prepare("
                SELECT p.name 
                FROM permissions p 
                JOIN role_permissions rp ON p.id = rp.permission_id 
                JOIN roles r ON rp.role_id = r.id 
                WHERE r.name IN ($placeholders) AND p.name = ?
            ");
            $params = array_merge($roleNames, [$permission]);
            $stmt->execute($params);
            return $stmt->fetch() !== false;
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Log user activity
    private function logActivity($userId, $action, $details = []) {
        try {
            $stmt = $this->coopDB->prepare("
                INSERT INTO audit_logs (user_id, action, details, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $action,
                json_encode($details),
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        } catch (Exception $e) {
            // Log error silently
        }
    }
    
    // Change password
    public function changePassword($userId, $currentPassword, $newPassword) {
        try {
            $stmt = $this->db->prepare("SELECT password_hash FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if (!$user || !$this->verifyPassword($currentPassword, $user['password_hash'])) {
                return ['success' => false, 'message' => 'Current password is incorrect'];
            }
            
            $newHash = $this->hashPassword($newPassword);
            $updateStmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $updateStmt->execute([$newHash, $userId]);
            
            $this->logActivity($userId, 'password_changed');
            
            return ['success' => true, 'message' => 'Password changed successfully'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Password change failed: ' . $e->getMessage()];
        }
    }
}
