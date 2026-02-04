<?php
// Cooperative Financial Settings Management Class
class CooperativeFinancialSettings {
    private $coopDB;
    
    public function __construct() {
        $app = App::getInstance();
        $this->coopDB = $app->getCoopDB();
    }
    
    // Get current financial settings for a cooperative
    public function getCurrentSettings($cooperativeId, $tahun = null) {
        $tahun = $tahun ?: date('Y');
        
        try {
            $stmt = $this->coopDB->prepare("
                SELECT * FROM cooperative_financial_settings 
                WHERE cooperative_id = ? AND tahun_buku = ? AND status = 'active'
            ");
            $stmt->execute([$cooperativeId, $tahun]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }
    
    // Get all financial settings history for a cooperative
    public function getSettingsHistory($cooperativeId) {
        try {
            $stmt = $this->coopDB->prepare("
                SELECT * FROM cooperative_financial_settings 
                WHERE cooperative_id = ? 
                ORDER BY tahun_buku DESC
            ");
            $stmt->execute([$cooperativeId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Create or update financial settings for a year
    public function setFinancialSettings($data) {
        try {
            $this->coopDB->beginTransaction();
            
            $required = ['cooperative_id', 'tahun_buku', 'simpanan_pokok', 'simpanan_wajib', 'bunga_pinjaman', 'denda_telat'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Field $field is required");
                }
            }
            
            // Check if settings already exist for this year
            $existing = $this->getCurrentSettings($data['cooperative_id'], $data['tahun_buku']);
            
            if ($existing) {
                // Update existing
                $stmt = $this->coopDB->prepare("
                    UPDATE cooperative_financial_settings 
                    SET simpanan_pokok = ?, simpanan_wajib = ?, bunga_pinjaman = ?, 
                        denda_telat = ?, periode_shu = ?, periode_mulai = ?, periode_akhir = ?,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE cooperative_id = ? AND tahun_buku = ?
                ");
                $stmt->execute([
                    $data['simpanan_pokok'],
                    $data['simpanan_wajib'],
                    $data['bunga_pinjaman'],
                    $data['denda_telat'],
                    $data['periode_shu'] ?? 'yearly',
                    $data['periode_mulai'] ?? $data['tahun_buku'] . '-01-01',
                    $data['periode_akhir'] ?? $data['tahun_buku'] . '-12-31',
                    $data['cooperative_id'],
                    $data['tahun_buku']
                ]);
            } else {
                // Insert new
                $stmt = $this->coopDB->prepare("
                    INSERT INTO cooperative_financial_settings 
                    (cooperative_id, tahun_buku, periode_mulai, periode_akhir, 
                     simpanan_pokok, simpanan_wajib, bunga_pinjaman, denda_telat, periode_shu, created_by)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $data['cooperative_id'],
                    $data['tahun_buku'],
                    $data['periode_mulai'] ?? $data['tahun_buku'] . '-01-01',
                    $data['periode_akhir'] ?? $data['tahun_buku'] . '-12-31',
                    $data['simpanan_pokok'],
                    $data['simpanan_wajib'],
                    $data['bunga_pinjaman'],
                    $data['denda_telat'],
                    $data['periode_shu'] ?? 'yearly',
                    $data['created_by'] ?? null
                ]);
            }
            
            $this->coopDB->commit();
            return ['success' => true, 'message' => 'Financial settings saved successfully'];
            
        } catch (Exception $e) {
            $this->coopDB->rollBack();
            return ['success' => false, 'message' => 'Failed to save financial settings: ' . $e->getMessage()];
        }
    }
    
    // Close financial settings for a year
    public function closeFinancialYear($cooperativeId, $tahun) {
        try {
            $stmt = $this->coopDB->prepare("
                UPDATE cooperative_financial_settings 
                SET status = 'closed', updated_at = CURRENT_TIMESTAMP
                WHERE cooperative_id = ? AND tahun_buku = ?
            ");
            $stmt->execute([$cooperativeId, $tahun]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Get active financial year for cooperative
    public function getActiveYear($cooperativeId) {
        try {
            $stmt = $this->coopDB->prepare("
                SELECT tahun_buku FROM cooperative_financial_settings 
                WHERE cooperative_id = ? AND status = 'active'
                ORDER BY tahun_buku DESC LIMIT 1
            ");
            $stmt->execute([$cooperativeId]);
            $result = $stmt->fetch();
            return $result ? $result['tahun_buku'] : date('Y');
        } catch (Exception $e) {
            return date('Y');
        }
    }
}
