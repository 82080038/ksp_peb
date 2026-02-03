<?php
// Authentication and User Management Class
class Auth {
    private $peopleDB;
    private $coopDB;
    
    public function __construct() {
        $app = App::getInstance();
        $this->peopleDB = $app->getPeopleDB();
        $this->coopDB = $app->getCoopDB();
    }
    
    // Hash password using bcrypt
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => intval($_ENV['HASH_COST'] ?? 12)]);
    }
    
    // Verify password
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    // Register new user
    public function register($data) {
        try {
            // Validate required fields
            $required = ['nama', 'email', 'password', 'phone', 'village_id', 'full_address'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return ['success' => false, 'message' => "Field $field is required"];
                }
            }
            
            // Check if email exists
            $stmt = $this->peopleDB->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email already exists'];
            }
            
            // Check if phone exists
            $stmt = $this->peopleDB->prepare("SELECT id FROM users WHERE phone = ?");
            $stmt->execute([$data['phone']]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Phone number already exists'];
            }
            
            // Hash password
            $hashedPassword = $this->hashPassword($data['password']);
            
            // Insert user
            $stmt = $this->peopleDB->prepare("
                INSERT INTO users (nama, email, phone, password_hash, status) 
                VALUES (?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([$data['nama'], $data['email'], $data['phone'], $hashedPassword]);
            $userId = $this->peopleDB->lastInsertId();
            
            // Add identity record if provided
            if (!empty($data['nik'])) {
                $stmt = $this->peopleDB->prepare("
                    INSERT INTO identities (user_id, nik) VALUES (?, ?)
                ");
                $stmt->execute([$userId, $data['nik']]);
            }
            
            // Add address record
            $stmt = $this->peopleDB->prepare("
                INSERT INTO addresses (user_id, village_id, full_address) VALUES (?, ?, ?)
            ");
            $stmt->execute([$userId, $data['village_id'], $data['full_address']]);
            
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
    
    // Login user
    public function login($email, $password) {
        try {
            $stmt = $this->peopleDB->prepare("
                SELECT u.*, i.nik 
                FROM users u 
                LEFT JOIN identities i ON u.id = i.user_id 
                WHERE u.email = ? AND u.status = 'active'
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return ['success' => false, 'message' => 'Invalid credentials or inactive account'];
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
            $roleStmt->execute([$user['id']]);
            $roles = $roleStmt->fetchAll();
            
            // Set session
            $app = App::getInstance();
            $app->startSession();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['nama'];
            $_SESSION['user_roles'] = $roles;
            $_SESSION['login_time'] = time();
            
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
            $stmt = $this->peopleDB->prepare("
                SELECT u.id, u.nama, u.email, u.phone, u.status, i.nik 
                FROM users u 
                LEFT JOIN identities i ON u.id = i.user_id 
                WHERE u.id = ?
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Get roles
                $roleStmt = $this->coopDB->prepare("
                    SELECT r.name, r.description 
                    FROM roles r 
                    JOIN user_roles ur ON r.id = ur.role_id 
                    WHERE ur.user_id = ?
                ");
                $roleStmt->execute([$_SESSION['user_id']]);
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
            $stmt = $this->peopleDB->prepare("SELECT password_hash FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if (!$user || !$this->verifyPassword($currentPassword, $user['password_hash'])) {
                return ['success' => false, 'message' => 'Current password is incorrect'];
            }
            
            $newHash = $this->hashPassword($newPassword);
            $updateStmt = $this->peopleDB->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $updateStmt->execute([$newHash, $userId]);
            
            $this->logActivity($userId, 'password_changed');
            
            return ['success' => true, 'message' => 'Password changed successfully'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Password change failed: ' . $e->getMessage()];
        }
    }
}
