<?php
// Member Management Class
class Member {
    private $peopleDB;
    private $coopDB;
    
    public function __construct() {
        $app = App::getInstance();
        $this->peopleDB = $app->getPeopleDB();
        $this->coopDB = $app->getCoopDB();
    }
    
    // Get all members with user details
    public function getAllMembers($limit = 50, $offset = 0) {
        try {
            $stmt = $this->coopDB->prepare("
                SELECT a.*, u.nama, u.email, u.phone, u.status as user_status, i.nik
                FROM anggota a
                JOIN people_db.users u ON a.user_id = u.id
                LEFT JOIN people_db.identities i ON u.id = i.user_id
                ORDER BY a.joined_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$limit, $offset]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Get member by ID
    public function getMember($id) {
        try {
            $stmt = $this->coopDB->prepare("
                SELECT a.*, u.nama, u.email, u.phone, u.status as user_status, i.nik,
                       i.tempat_lahir, i.tanggal_lahir
                FROM anggota a
                JOIN people_db.users u ON a.user_id = u.id
                LEFT JOIN people_db.identities i ON u.id = i.user_id
                WHERE a.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }
    
    // Create new member
    public function createMember($userId, $nomorAnggota = null) {
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
            
            // Check if user is already a member
            $memberStmt = $this->coopDB->prepare("SELECT id FROM anggota WHERE user_id = ?");
            $memberStmt->execute([$userId]);
            if ($memberStmt->fetch()) {
                return ['success' => false, 'message' => 'User is already a member'];
            }
            
            // Generate member number if not provided
            if (!$nomorAnggota) {
                $nomorAnggota = $this->generateMemberNumber();
            } else {
                // Check if member number already exists
                $checkStmt = $this->coopDB->prepare("SELECT id FROM anggota WHERE nomor_anggota = ?");
                $checkStmt->execute([$nomorAnggota]);
                if ($checkStmt->fetch()) {
                    return ['success' => false, 'message' => 'Member number already exists'];
                }
            }
            
            // Insert member
            $stmt = $this->coopDB->prepare("
                INSERT INTO anggota (user_id, nomor_anggota, status_keanggotaan) 
                VALUES (?, ?, 'active')
            ");
            $result = $stmt->execute([$userId, $nomorAnggota]);
            
            if ($result) {
                $memberId = $this->coopDB->lastInsertId();
                
                // Assign anggota role
                $rbac = new RBAC();
                $roleStmt = $this->coopDB->prepare("SELECT id FROM roles WHERE name = 'anggota'");
                $roleStmt->execute();
                $role = $roleStmt->fetch();
                
                if ($role) {
                    $rbac->assignRole($userId, $role['id']);
                }
                
                return ['success' => true, 'member_id' => $memberId, 'nomor_anggota' => $nomorAnggota];
            }
            
            return ['success' => false, 'message' => 'Failed to create member'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Update member status
    public function updateMemberStatus($memberId, $status, $reason = '') {
        try {
            $validStatuses = ['active', 'inactive', 'suspended'];
            if (!in_array($status, $validStatuses)) {
                return ['success' => false, 'message' => 'Invalid status'];
            }
            
            $stmt = $this->coopDB->prepare("
                UPDATE anggota 
                SET status_keanggotaan = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?
            ");
            $result = $stmt->execute([$status, $memberId]);
            
            if ($result) {
                // Log the status change
                $this->logMemberActivity($memberId, 'status_change', [
                    'new_status' => $status,
                    'reason' => $reason
                ]);
                
                return ['success' => true, 'message' => 'Member status updated successfully'];
            }
            
            return ['success' => false, 'message' => 'Failed to update member status'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Get member savings
    public function getMemberSavings($memberId) {
        try {
            $stmt = $this->coopDB->prepare("
                SELECT st.*, spt.name as type_name
                FROM simpanan_transactions st
                JOIN simpanan_types spt ON st.type_id = spt.id
                WHERE st.anggota_id = ?
                ORDER BY st.transaction_date DESC
            ");
            $stmt->execute([$memberId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Get member loans
    public function getMemberLoans($memberId) {
        try {
            $stmt = $this->coopDB->prepare("
                SELECT p.*, 
                       (SELECT COUNT(*) FROM pinjaman_angsuran pa WHERE pa.pinjaman_id = p.id AND pa.status = 'pending') as pending_installments,
                       (SELECT SUM(pa.total_amount - pa.paid_amount) FROM pinjaman_angsuran pa WHERE pa.pinjaman_id = p.id AND pa.status = 'pending') as outstanding_amount
                FROM pinjaman p
                WHERE p.anggota_id = ?
                ORDER BY p.created_at DESC
            ");
            $stmt->execute([$memberId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Get member savings balance
    public function getMemberSavingsBalance($memberId) {
        try {
            $stmt = $this->coopDB->prepare("
                SELECT spt.name, SUM(CASE WHEN st.transaction_type = 'deposit' THEN st.amount ELSE -st.amount END) as balance
                FROM simpanan_transactions st
                JOIN simpanan_types spt ON st.type_id = spt.id
                WHERE st.anggota_id = ?
                GROUP BY spt.id, spt.name
            ");
            $stmt->execute([$memberId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Search members
    public function searchMembers($query, $limit = 50) {
        try {
            $stmt = $this->coopDB->prepare("
                SELECT a.id, a.nomor_anggota, a.status_keanggotaan, u.nama, u.email, u.phone, i.nik
                FROM anggota a
                JOIN people_db.users u ON a.user_id = u.id
                LEFT JOIN people_db.identities i ON u.id = i.user_id
                WHERE u.nama LIKE ? OR a.nomor_anggota LIKE ? OR u.email LIKE ? OR i.nik LIKE ?
                ORDER BY u.nama
                LIMIT ?
            ");
            $searchTerm = "%$query%";
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $limit]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Get member count by status
    public function getMemberCountByStatus() {
        try {
            $stmt = $this->coopDB->prepare("
                SELECT status_keanggotaan, COUNT(*) as count
                FROM anggota
                GROUP BY status_keanggotaan
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Generate unique member number
    private function generateMemberNumber() {
        do {
            $year = date('Y');
            $random = mt_rand(1000, 9999);
            $nomorAnggota = "KSP{$year}{$random}";
            
            $stmt = $this->coopDB->prepare("SELECT id FROM anggota WHERE nomor_anggota = ?");
            $stmt->execute([$nomorAnggota]);
            $exists = $stmt->fetch();
        } while ($exists);
        
        return $nomorAnggota;
    }
    
    // Log member activity
    private function logMemberActivity($memberId, $action, $details = []) {
        try {
            $stmt = $this->coopDB->prepare("
                INSERT INTO audit_logs (user_id, action, details, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $userId = $_SESSION['user_id'] ?? null;
            $stmt->execute([
                $userId,
                "member_{$action}",
                json_encode(array_merge(['member_id' => $memberId], $details)),
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        } catch (Exception $e) {
            // Log error silently
        }
    }
}
