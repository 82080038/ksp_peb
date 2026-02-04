<?php
// Management (Pengurus) Class
class Management {
    private $peopleDB;
    private $coopDB;
    
    public function __construct() {
        $app = App::getInstance();
        $this->peopleDB = $app->getPeopleDB();
        $this->coopDB = $app->getCoopDB();
    }
    
    // Get all pengurus
    public function getAllPengurus() {
        try {
            $stmt = $this->coopDB->prepare("
                SELECT p.*, u.nama, u.email, u.phone, i.nik
                FROM pengurus p
                JOIN people_db.users u ON p.user_id = u.id
                LEFT JOIN people_db.identities i ON u.id = i.user_id
                ORDER BY p.periode_start DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Get pengurus by ID
    public function getPengurus($id) {
        try {
            $stmt = $this->coopDB->prepare("
                SELECT p.*, u.nama, u.email, u.phone, i.nik
                FROM pengurus p
                JOIN people_db.users u ON p.user_id = u.id
                LEFT JOIN people_db.identities i ON u.id = i.user_id
                WHERE p.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }
    
    // Create new pengurus
    public function createPengurus($userId, $jabatan, $periodeStart, $periodeEnd = null) {
        try {
            // Check if user exists and is active
            $userStmt = $this->peopleDB->prepare("SELECT id, status FROM users WHERE id = ?");
            $userStmt->execute([$userId]);
            $user = $userStmt->fetch();
            
            if (!$user) {
                return ['success' => false, 'message' => 'User not found'];
            }
            
            if ($user['status'] !== 'active') {
                return ['success' => false, 'message' => 'User account is not active'];
            }
            
            // Insert pengurus
            $stmt = $this->coopDB->prepare("
                INSERT INTO pengurus (user_id, jabatan, periode_start, periode_end, status) 
                VALUES (?, ?, ?, ?, 'active')
            ");
            $result = $stmt->execute([$userId, $jabatan, $periodeStart, $periodeEnd]);
            
            if ($result) {
                $pengurusId = $this->coopDB->lastInsertId();
                
                // Assign admin role
                $rbac = new RBAC();
                $roleStmt = $this->coopDB->prepare("SELECT id FROM roles WHERE name = 'admin'");
                $roleStmt->execute();
                $role = $roleStmt->fetch();
                
                if ($role) {
                    $rbac->assignRole($userId, $role['id']);
                }
                
                // Log activity
                $this->logManagementActivity($pengurusId, 'create', [
                    'jabatan' => $jabatan,
                    'periode_start' => $periodeStart,
                    'periode_end' => $periodeEnd
                ]);
                
                return ['success' => true, 'pengurus_id' => $pengurusId];
            }
            
            return ['success' => false, 'message' => 'Failed to create pengurus'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Update pengurus
    public function updatePengurus($id, $jabatan, $periodeStart, $periodeEnd = null) {
        try {
            $stmt = $this->coopDB->prepare("
                UPDATE pengurus 
                SET jabatan = ?, periode_start = ?, periode_end = ? 
                WHERE id = ?
            ");
            $result = $stmt->execute([$jabatan, $periodeStart, $periodeEnd, $id]);
            
            if ($result) {
                $this->logManagementActivity($id, 'update', [
                    'jabatan' => $jabatan,
                    'periode_start' => $periodeStart,
                    'periode_end' => $periodeEnd
                ]);
                
                return ['success' => true, 'message' => 'Pengurus updated successfully'];
            }
            
            return ['success' => false, 'message' => 'Failed to update pengurus'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Update pengurus status
    public function updatePengurusStatus($id, $status) {
        try {
            $validStatuses = ['active', 'inactive'];
            if (!in_array($status, $validStatuses)) {
                return ['success' => false, 'message' => 'Invalid status'];
            }
            
            $stmt = $this->coopDB->prepare("
                UPDATE pengurus SET status = ? WHERE id = ?
            ");
            $result = $stmt->execute([$status, $id]);
            
            if ($result) {
                $this->logManagementActivity($id, 'status_change', ['new_status' => $status]);
                return ['success' => true, 'message' => 'Pengurus status updated successfully'];
            }
            
            return ['success' => false, 'message' => 'Failed to update pengurus status'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Get all pengawas
    public function getAllPengawas() {
        try {
            $stmt = $this->coopDB->prepare("
                SELECT p.*, u.nama, u.email, u.phone, i.nik
                FROM pengawas p
                JOIN people_db.users u ON p.user_id = u.id
                LEFT JOIN people_db.identities i ON u.id = i.user_id
                ORDER BY p.periode_start DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Get pengawas by ID
    public function getPengawas($id) {
        try {
            $stmt = $this->coopDB->prepare("
                SELECT p.*, u.nama, u.email, u.phone, i.nik
                FROM pengawas p
                JOIN people_db.users u ON p.user_id = u.id
                LEFT JOIN people_db.identities i ON u.id = i.user_id
                WHERE p.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }
    
    // Create new pengawas
    public function createPengawas($userId, $jabatan, $periodeStart, $periodeEnd = null) {
        try {
            // Check if user exists and is active
            $userStmt = $this->peopleDB->prepare("SELECT id, status FROM users WHERE id = ?");
            $userStmt->execute([$userId]);
            $user = $userStmt->fetch();
            
            if (!$user) {
                return ['success' => false, 'message' => 'User not found'];
            }
            
            if ($user['status'] !== 'active') {
                return ['success' => false, 'message' => 'User account is not active'];
            }
            
            // Insert pengawas
            $stmt = $this->coopDB->prepare("
                INSERT INTO pengawas (user_id, jabatan, periode_start, periode_end, status) 
                VALUES (?, ?, ?, ?, 'active')
            ");
            $result = $stmt->execute([$userId, $jabatan, $periodeStart, $periodeEnd]);
            
            if ($result) {
                $pengawasId = $this->coopDB->lastInsertId();
                
                // Assign pengawas role
                $rbac = new RBAC();
                $roleStmt = $this->coopDB->prepare("SELECT id FROM roles WHERE name = 'pengawas'");
                $roleStmt->execute();
                $role = $roleStmt->fetch();
                
                if ($role) {
                    $rbac->assignRole($userId, $role['id']);
                }
                
                // Log activity
                $this->logManagementActivity($pengawasId, 'create', [
                    'type' => 'pengawas',
                    'jabatan' => $jabatan,
                    'periode_start' => $periodeStart,
                    'periode_end' => $periodeEnd
                ]);
                
                return ['success' => true, 'pengawas_id' => $pengawasId];
            }
            
            return ['success' => false, 'message' => 'Failed to create pengawas'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Update pengawas
    public function updatePengawas($id, $jabatan, $periodeStart, $periodeEnd = null) {
        try {
            $stmt = $this->coopDB->prepare("
                UPDATE pengawas 
                SET jabatan = ?, periode_start = ?, periode_end = ? 
                WHERE id = ?
            ");
            $result = $stmt->execute([$jabatan, $periodeStart, $periodeEnd, $id]);
            
            if ($result) {
                $this->logManagementActivity($id, 'update', [
                    'type' => 'pengawas',
                    'jabatan' => $jabatan,
                    'periode_start' => $periodeStart,
                    'periode_end' => $periodeEnd
                ]);
                
                return ['success' => true, 'message' => 'Pengawas updated successfully'];
            }
            
            return ['success' => false, 'message' => 'Failed to update pengawas'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Update pengawas status
    public function updatePengawasStatus($id, $status) {
        try {
            $validStatuses = ['active', 'inactive'];
            if (!in_array($status, $validStatuses)) {
                return ['success' => false, 'message' => 'Invalid status'];
            }
            
            $stmt = $this->coopDB->prepare("
                UPDATE pengawas SET status = ? WHERE id = ?
            ");
            $result = $stmt->execute([$status, $id]);
            
            if ($result) {
                $this->logManagementActivity($id, 'status_change', [
                    'type' => 'pengawas',
                    'new_status' => $status
                ]);
                
                return ['success' => true, 'message' => 'Pengawas status updated successfully'];
            }
            
            return ['success' => false, 'message' => 'Failed to update pengawas status'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Get active pengurus count
    public function getActivePengurusCount() {
        try {
            $stmt = $this->coopDB->prepare("SELECT COUNT(*) as count FROM pengurus WHERE status = 'active'");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    // Get active pengawas count
    public function getActivePengawasCount() {
        try {
            $stmt = $this->coopDB->prepare("SELECT COUNT(*) as count FROM pengawas WHERE status = 'active'");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    // Log management activity
    private function logManagementActivity($id, $action, $details = []) {
        try {
            $stmt = $this->coopDB->prepare("
                INSERT INTO audit_logs (user_id, action, details, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $userId = $_SESSION['user_id'] ?? null;
            $stmt->execute([
                $userId,
                "management_{$action}",
                json_encode(array_merge(['management_id' => $id], $details)),
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        } catch (Exception $e) {
            // Log error silently
        }
    }
}
