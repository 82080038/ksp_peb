<?php
// Cooperative Management Class
class Cooperative {
    private $coopDB;
    private $addressDB;
    
    public function __construct() {
        $app = App::getInstance();
        $this->coopDB = $app->getCoopDB();
        $this->addressDB = $app->getAddressDB();
    }
    
    // Create new cooperative
    public function createCooperative($data) {
        try {
            $this->coopDB->beginTransaction();
            
            // Validate required fields (alamat_legal diambil dari alamat_detail jika tidak ada)
            $required = ['nama', 'jenis', 'badan_hukum', 'tanggal_pendirian', 'npwp', 'kontak_resmi', 'alamat_detail', 'admin_username', 'admin_email', 'admin_phone', 'admin_password'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return ['success' => false, 'message' => "Field $field is required"];
                }
            }

            // Set alamat_legal using detil alamat sebagai sumber utama
            $alamatLegal = $data['alamat_detail'] ?? ($data['alamat_legal'] ?? null);

            // Prepare address json (id prov-kab-kec-desa + detail)
            $addressJson = json_encode([
                'province_id' => $data['province_id'] ?? null,
                'regency_id' => $data['regency_id'] ?? null,
                'district_id' => $data['district_id'] ?? null,
                'village_id' => $data['village_id'] ?? null,
                'alamat_detail' => $data['alamat_detail'] ?? null
            ]);

            // Hash admin password
            $auth = new Auth();
            $hashedPassword = $auth->hashPassword($data['admin_password']);

            // Ensure admin username unique in coop_db users
            $userCheck = $this->coopDB->prepare("SELECT id FROM users WHERE username = ?");
            $userCheck->execute([$data['admin_username']]);
            if ($userCheck->fetch()) {
                return ['success' => false, 'message' => 'Username admin sudah digunakan'];
            }

            // Create or reuse people_db user based on email/phone (tanpa menyimpan password)
            $peopleUserId = null;
            $peopleCheck = $this->addressDB->prepare("SELECT id FROM people_db.users WHERE email = ? OR phone = ? LIMIT 1");
            $peopleCheck->execute([$data['admin_email'], $data['admin_phone']]);
            $existingPeople = $peopleCheck->fetch();
            if ($existingPeople) {
                $peopleUserId = $existingPeople['id'];
            } else {
                $peopleInsert = $this->addressDB->prepare("INSERT INTO people_db.users (nama, email, phone, status) VALUES (?, ?, ?, 'active')");
                $peopleInsert->execute([$data['admin_nama'] ?? $data['admin_username'], $data['admin_email'], $data['admin_phone']]);
                $peopleUserId = $this->addressDB->lastInsertId();
            }

            // Insert coop_db auth user linked to people_db
            $coopUserStmt = $this->coopDB->prepare("INSERT INTO users (username, password_hash, user_db_id, status) VALUES (?, ?, ?, 'active')");
            $coopUserStmt->execute([$data['admin_username'], $hashedPassword, $peopleUserId]);
            $coopUserId = $this->coopDB->lastInsertId();

            // Insert cooperative
            $stmt = $this->coopDB->prepare("
                INSERT INTO cooperatives (
                    nama, jenis, badan_hukum, tanggal_pendirian, npwp, 
                    alamat_legal, kontak_resmi, logo, periode_tahun_buku,
                    simpanan_pokok, simpanan_wajib, bunga_pinjaman, denda_telat, periode_shu,
                    created_by, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
            ");

            $stmt->execute([
                $data['nama'],
                json_encode($data['jenis']), // Store as JSON for multi-select
                $data['badan_hukum'],
                $data['tanggal_pendirian'],
                $data['npwp'],
                $alamatLegal,
                $data['kontak_resmi'],
                $data['logo'] ?? null,
                $data['periode_tahun_buku'] ?? 'calendar',
                $data['simpanan_pokok'] ?? 0,
                $data['simpanan_wajib'] ?? 0,
                $data['bunga_pinjaman'] ?? 12,
                $data['denda_telat'] ?? 2,
                $data['periode_shu'] ?? 'yearly',
                $coopUserId
            ]);

            $cooperativeId = $this->coopDB->lastInsertId();
            
            // Create tenant config
            $this->createTenantConfig($cooperativeId, $data['jenis']);
            
            // Create default COA
            $this->createDefaultCOA($cooperativeId);
            
            // Assign Super Admin role to created admin user
            $rbac = new RBAC();
            $roleStmt = $this->coopDB->prepare("SELECT id FROM roles WHERE name = 'super_admin'");
            $roleStmt->execute();
            $role = $roleStmt->fetch();
            if ($role) {
                $rbac->assignRole($coopUserId, $role['id']);
            }
            
            $this->coopDB->commit();
            
            return ['success' => true, 'cooperative_id' => $cooperativeId];
            
        } catch (Exception $e) {
            $this->coopDB->rollBack();
            return ['success' => false, 'message' => 'Failed to create cooperative: ' . $e->getMessage()];
        }
    }
    
    // Get all cooperatives
    public function getAllCooperatives() {
        try {
            $stmt = $this->coopDB->prepare("
                SELECT c.*, u.nama as creator_name 
                FROM cooperatives c
                LEFT JOIN people_db.users u ON c.created_by = u.id
                ORDER BY c.created_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Get cooperative by ID
    public function getCooperative($id) {
        try {
            $stmt = $this->coopDB->prepare("
                SELECT c.*, u.nama as creator_name 
                FROM cooperatives c
                LEFT JOIN people_db.users u ON c.created_by = u.id
                WHERE c.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }
    
    // Create tenant configuration
    private function createTenantConfig($cooperativeId, $jenis) {
        try {
            // Activate modules based on cooperative types
            $moduleMap = [
                'Simpan Pinjam' => ['anggota', 'simpanan', 'pinjaman', 'shu', 'akuntansi', 'voting'],
                'Konsumsi' => ['e-commerce', 'produk', 'order', 'pengiriman', 'pos'],
                'Produksi' => ['bom', 'work_orders', 'inventory', 'hpp'],
                'Pemasaran' => ['e-commerce', 'produk', 'order', 'pengiriman', 'agen'],
                'Jasa' => ['service_catalog', 'service_orders', 'sla'],
                'Serba Usaha' => ['anggota', 'simpanan', 'pinjaman', 'e-commerce', 'inventory']
            ];
            
            $activeModules = [];
            foreach ($jenis as $type) {
                if (isset($moduleMap[$type])) {
                    $activeModules = array_merge($activeModules, $moduleMap[$type]);
                }
            }
            $activeModules = array_unique($activeModules);
            
            // Insert tenant config
            $stmt = $this->coopDB->prepare("
                INSERT INTO tenant_configs (cooperative_id, active_modules, feature_flags) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([
                $cooperativeId,
                json_encode($activeModules),
                json_encode(['multi_tenant' => true, 'modular' => true])
            ]);
            
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    // Create default Chart of Accounts for cooperative
    private function createDefaultCOA($cooperativeId) {
        try {
            $defaultAccounts = [
                ['1000', 'Kas', 'asset'],
                ['1100', 'Bank', 'asset'],
                ['2000', 'Simpanan Anggota', 'liability'],
                ['2100', 'Pinjaman Anggota', 'asset'],
                ['3000', 'Modal', 'equity'],
                ['3100', 'Cadangan', 'equity'],
                ['4000', 'Pendapatan Bunga', 'revenue'],
                ['4100', 'Pendapatan Operasional', 'revenue'],
                ['5000', 'Beban Bunga', 'expense'],
                ['5100', 'Beban Operasional', 'expense'],
                ['5200', 'Beban Administrasi', 'expense']
            ];
            
            $stmt = $this->coopDB->prepare("
                INSERT INTO chart_of_accounts (cooperative_id, code, name, type) 
                VALUES (?, ?, ?, ?)
            ");
            
            foreach ($defaultAccounts as $account) {
                $stmt->execute([$cooperativeId, $account[0], $account[1], $account[2]]);
            }
            
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    // Get provinces for address dropdown
    public function getProvinces() {
        try {
            $stmt = $this->addressDB->prepare("SELECT id, name FROM provinces ORDER BY name");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Get cities by province
    public function getCities($provinceId) {
        try {
            $stmt = $this->addressDB->prepare("
                SELECT id, name FROM regencies WHERE province_id = ? ORDER BY name
            ");
            $stmt->execute([$provinceId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Get districts by city
    public function getDistricts($cityId) {
        try {
            $stmt = $this->addressDB->prepare("
                SELECT id, name FROM districts WHERE regency_id = ? ORDER BY name
            ");
            $stmt->execute([$cityId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Get villages by district
    public function getVillages($districtId) {
        try {
            $stmt = $this->addressDB->prepare("
                SELECT id, name, postal_code FROM villages WHERE district_id = ? ORDER BY name
            ");
            $stmt->execute([$districtId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Get cooperatives by district
    public function getCooperativesByDistrict($districtId) {
        try {
            // For now, return empty array since cooperatives table doesn't exist yet
            // In future, this would query cooperatives table by district_id
            return [];
            
            // Future implementation:
            // $stmt = $this->coopDB->prepare("
            //     SELECT id, nama FROM cooperatives WHERE district_id = ? ORDER BY nama
            // ");
            // $stmt->execute([$districtId]);
            // return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Check if any cooperative exists
    public function hasCooperatives() {
        try {
            $stmt = $this->coopDB->prepare("SELECT COUNT(*) as count FROM cooperatives");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    // Get active cooperative types
    public function getCooperativeTypes() {
        try {
            $stmt = $this->coopDB->prepare("SELECT id, name FROM cooperative_types WHERE is_active = 1 ORDER BY name");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
}
