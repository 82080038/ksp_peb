<?php
class User {
    private $pdo;
    private $coop_pdo;

    public function __construct() {
        $this->pdo = getPeopleDB();
        $this->coop_pdo = getCoopDB();
    }

    // Register a new user
    public function register($nama, $email, $phone, $password) {
        // Check if email exists
        $stmt = executeQuery($this->pdo, "SELECT id FROM users WHERE email = ?", [$email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Email sudah terdaftar'];
        }

        // Hash password
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        // Insert user
        $stmt = executeQuery($this->pdo, 
            "INSERT INTO users (nama, email, phone, password_hash, status) VALUES (?, ?, ?, ?, 'pending')",
            [$nama, $email, $phone, $hashed]
        );

        if ($stmt->rowCount() > 0) {
            $userId = $this->pdo->lastInsertId();
            // Assign default role 'calon_anggota'
            $this->assignRole($userId, 'calon_anggota');
            return ['success' => true, 'message' => 'Registrasi berhasil', 'user_id' => $userId];
        }

        return ['success' => false, 'message' => 'Registrasi gagal'];
    }

    // Login
    public function login($email, $password) {
        $stmt = executeQuery($this->pdo, "SELECT id, nama, email, password_hash, status FROM users WHERE email = ?", [$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['status'] !== 'active') {
                return ['success' => false, 'message' => 'Akun belum aktif'];
            }
            // Start session
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nama'];
            $_SESSION['user_email'] = $user['email'];
            return ['success' => true, 'message' => 'Login berhasil', 'user' => $user];
        }

        return ['success' => false, 'message' => 'Email atau password salah'];
    }

    // Logout
    public function logout() {
        session_start();
        session_destroy();
        return ['success' => true, 'message' => 'Logout berhasil'];
    }

    // Check if logged in
    public function isLoggedIn() {
        session_start();
        return isset($_SESSION['user_id']);
    }

    // Get current user
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) return null;
        $stmt = executeQuery($this->pdo, "SELECT id, nama, email, phone, status FROM users WHERE id = ?", [$_SESSION['user_id']]);
        return $stmt->fetch();
    }

    // Assign role
    private function assignRole($userId, $roleName) {
        // Get role id
        $stmt = executeQuery($this->pdo, "SELECT id FROM roles WHERE name = ?", [$roleName]);
        $role = $stmt->fetch();
        if ($role) {
            executeQuery($this->pdo, "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)", [$userId, $role['id']]);
        }
    }

    // Get user roles
    public function getUserRoles($userId) {
        $stmt = executeQuery($this->pdo, 
            "SELECT r.name FROM roles r JOIN user_roles ur ON r.id = ur.role_id WHERE ur.user_id = ?",
            [$userId]
        );
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Check permission
    public function hasPermission($userId, $permission) {
        $stmt = executeQuery($this->pdo, 
            "SELECT p.name FROM permissions p 
             JOIN role_permissions rp ON p.id = rp.permission_id 
             JOIN user_roles ur ON rp.role_id = ur.role_id 
             WHERE ur.user_id = ? AND p.name = ?",
            [$userId, $permission]
        );
        return $stmt->fetch() ? true : false;
    }
}
?>
