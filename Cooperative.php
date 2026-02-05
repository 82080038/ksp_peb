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
            // Debug: Log incoming data
            error_log("DEBUG: Cooperative create - Incoming data: " . json_encode($data));
            error_log("DEBUG: Admin password exists: " . isset($data['admin_password']));
            error_log("DEBUG: Admin password value: " . ($data['admin_password'] ?? 'NULL'));
            
            $this->coopDB->beginTransaction();
            
            // Validate required fields (alamat_legal diambil dari alamat_detail jika tidak ada)
            $required = ['nama_koperasi', 'jenis', 'badan_hukum', 'tanggal_pendirian', 'alamat_detail', 'admin_username', 'admin_email', 'admin_phone', 'admin_password'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return ['success' => false, 'message' => "Field $field is required"];
                }
            }

            // Handle jenis koperasi - if it's a code, convert to name
            if (!empty($data['jenis'])) {
                // Check if jenis is a code (all uppercase letters)
                if (preg_match('/^[A-Z]+$/', $data['jenis'])) {
                    $typeStmt = $this->coopDB->prepare("SELECT name, code FROM cooperative_types WHERE code = ? AND is_active = 1");
                    $typeStmt->execute([$data['jenis']]);
                    $typeResult = $typeStmt->fetch();
                    if ($typeResult) {
                        // Store both code and name for flexibility
                        $data['jenis'] = [
                            'code' => $typeResult['code'],
                            'name' => $typeResult['name']
                        ];
                    } else {
                        return ['success' => false, 'message' => 'Jenis koperasi tidak valid'];
                    }
                }
            }
            
            // Handle jenis_koperasi from frontend (if exists)
            if (!empty($data['jenis_koperasi'])) {
                // If jenis already set from jenis_koperasi field, use that
                $data['jenis'] = $data['jenis_koperasi'];
            }
            
            // Handle nama_koperasi - directly save user input (no prefix removal)
            // User input is stored as-is since they can modify it after the prefix is added

            // Validate phone format (remove dashes for validation)
            $kontakResmiClean = preg_replace('/[^0-9]/', '', $data['kontak_resmi'] ?? '');
            $adminPhoneClean = preg_replace('/[^0-9]/', '', $data['admin_phone'] ?? '');
            
            // Validate NPWP format (16 digit standard PMK 112/2022)
            $npwpClean = preg_replace('/[^0-9]/', '', $data['npwp'] ?? '');
            
            if ($npwpClean && strlen($npwpClean) === 16) {
                // Format 16 digit (baru)
                if (!preg_match('/^[0-9]{16}$/', $npwpClean)) {
                    return ['success' => false, 'message' => 'Format NPWP 16 digit tidak valid (contoh: 3201234567890001)'];
                }
            } elseif ($npwpClean && strlen($npwpClean) === 15) {
                // Format 15 digit (lama) - optional legacy support
                if (!preg_match('/^[0-9]{15}$/', $npwpClean)) {
                    return ['success' => false, 'message' => 'Format NPWP 15 digit tidak valid (contoh: 01.234.567.8-012.000)'];
                }
            } elseif ($npwpClean) {
                return ['success' => false, 'message' => 'Format NPWP harus 15 atau 16 digit'];
            }
            
            // Debug: Check phone validation
            error_log("DEBUG: kontak_resmi value: '" . ($data['kontak_resmi'] ?? 'NULL') . "'");
            error_log("DEBUG: kontak_resmi length: " . strlen($data['kontak_resmi'] ?? ''));
            error_log("DEBUG: kontak_resmi regex test: " . (preg_match('/^08[0-9-]{9,14}$/', $data['kontak_resmi'] ?? '') ? 'VALID' : 'INVALID'));
            
            // Fix: Accept valid phone numbers
            if (!preg_match('/^08[0-9-]{9,14}$/', $data['kontak_resmi'] ?? '')) {
                return ['success' => false, 'message' => 'Format nomor kontak resmi tidak valid (contoh: 08123456789 atau 0812-3456-7890)'];
            }

            if (!preg_match('/^08[0-9-]{9,14}$/', $data['admin_phone'] ?? '')) {
                return ['success' => false, 'message' => 'Format nomor HP admin tidak valid (contoh: 08123456789 atau 0812-3456-7890)'];
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
            if (!isset($data['admin_password']) || empty($data['admin_password'])) {
                return ['success' => false, 'message' => 'Admin password is required'];
            }
            
            $hashedPassword = $auth->hashPassword($data['admin_password']);
            
            // Debug: Check if password hashing worked
            if (empty($hashedPassword)) {
                return ['success' => false, 'message' => 'Failed to hash admin password. Original password: ' . $data['admin_password']];
            }

            // Ensure admin username unique in coop_db users
            $userCheck = $this->coopDB->prepare("SELECT id FROM users WHERE username = ?");
            $userCheck->execute([$data['admin_username']]);
            if ($userCheck->fetch()) {
                return ['success' => false, 'message' => 'Username admin sudah digunakan'];
            }

            // Create or reuse people_db user based on email/phone (dengan password)
            $peopleUserId = null;
            $peopleCheck = $this->addressDB->prepare("SELECT id FROM people_db.users WHERE email = ? OR phone = ? LIMIT 1");
            $peopleCheck->execute([$data['admin_email'], $adminPhoneClean]);
            $existingPeople = $peopleCheck->fetch();
            if ($existingPeople) {
                $peopleUserId = $existingPeople['id'];
            } else {
                $peopleInsert = $this->addressDB->prepare("INSERT INTO people_db.users (nama, email, phone, password_hash, status) VALUES (?, ?, ?, ?, 'active')");
                $peopleInsert->execute([$data['admin_nama'] ?? $data['admin_username'], $data['admin_email'], $adminPhoneClean, $hashedPassword]);
                $peopleUserId = $this->addressDB->lastInsertId();
            }

            // Insert coop_db auth user linked to people_db
            $coopUserStmt = $this->coopDB->prepare("INSERT INTO users (username, password_hash, user_db_id, status) VALUES (?, ?, ?, 'active')");
            
            // Debug: Check variables before insert
            if (!isset($hashedPassword) || empty($hashedPassword)) {
                return ['success' => false, 'message' => 'Password hash is empty or not set. Original password: ' . ($data['admin_password'] ?? 'NULL')];
            }
            
            if (!isset($peopleUserId) || empty($peopleUserId)) {
                return ['success' => false, 'message' => 'User DB ID is empty or not set'];
            }
            
            try {
                $coopUserStmt->execute([$data['admin_username'], $hashedPassword, $peopleUserId]);
            } catch (PDOException $e) {
                return ['success' => false, 'message' => 'Failed to create user: ' . $e->getMessage()];
            }
            $coopUserId = $this->coopDB->lastInsertId();

            // Insert cooperative
            $stmt = $this->coopDB->prepare("
                INSERT INTO cooperatives (
                    nama, jenis, badan_hukum, tanggal_pendirian, npwp, 
                    alamat_legal, kontak_resmi, logo,
                    province_id, regency_id, district_id, village_id,
                    created_by, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
            ");

            $stmt->execute([
                $data['nama_koperasi'],
                json_encode($data['jenis']), // Store as JSON with code and name
                $data['badan_hukum'],
                $data['tanggal_pendirian'],
                $data['npwp'],
                $alamatLegal,
                $data['kontak_resmi'],
                $data['logo'] ?? null,
                $data['province_id'] ?? null,
                $data['regency_id'] ?? null,
                $data['district_id'] ?? null,
                $data['village_id'] ?? null,
                $coopUserId
            ]);

            $cooperativeId = $this->coopDB->lastInsertId();
            
            // Create default financial settings for current year
            $this->createDefaultFinancialSettings($cooperativeId, $coopUserId);
            
            // Create tenant config
            $jenisName = is_array($data['jenis']) ? $data['jenis']['name'] : $data['jenis'];
            $this->createTenantConfig($cooperativeId, $jenisName);
            
            // Create default COA
            $this->createDefaultCOA($cooperativeId);
            
            // Assign Admin role to created admin user
            $rbac = new RBAC();
            $roleStmt = $this->coopDB->prepare("SELECT id FROM roles WHERE name = 'admin'");
            $roleStmt->execute();
            $role = $roleStmt->fetch();
            
            $roleStmt = $this->coopDB->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
            $roleStmt->execute([$coopUserId, $role['id']]);
            
            $this->coopDB->commit();
            return ['success' => true, 'message' => 'Koperasi berhasil didaftarkan', 'cooperative_id' => $cooperativeId];
            
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
            if (isset($moduleMap[$jenis])) {
                $activeModules = $moduleMap[$jenis];
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
    
    // Create default financial settings for new cooperative
    private function createDefaultFinancialSettings($cooperativeId, $createdBy) {
        try {
            $currentYear = date('Y');
            $stmt = $this->coopDB->prepare("
                INSERT INTO cooperative_financial_settings 
                (cooperative_id, tahun_buku, periode_mulai, periode_akhir, 
                 simpanan_pokok, simpanan_wajib, bunga_pinjaman, denda_telat, periode_shu, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $cooperativeId,
                $currentYear,
                $currentYear . '-01-01',
                $currentYear . '-12-31',
                100000,  // Default simpanan pokok
                50000,   // Default simpanan wajib
                12.00,   // Default bunga pinjaman
                2.00,    // Default denda telat
                'yearly',
                $createdBy
            ]);
        } catch (Exception $e) {
            throw $e;
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
            $stmt = $this->coopDB->prepare("SELECT id, name, code, category FROM cooperative_types WHERE is_active = 1 ORDER BY name");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    // Update cooperative basic information
    public function updateCooperative($id, $data) {
        try {
            $this->coopDB->beginTransaction();
            
            // Get current data for comparison
            $currentStmt = $this->coopDB->prepare("SELECT * FROM cooperatives WHERE id = ?");
            $currentStmt->execute([$id]);
            $current = $currentStmt->fetch();
            
            if (!$current) {
                return ['success' => false, 'message' => 'Koperasi tidak ditemukan'];
            }
            
            // Validate NPWP format
            if (!empty($data['npwp'])) {
                $npwpClean = preg_replace('/[^0-9]/', '', $data['npwp']);
            }
            
            // Validate phone format (remove dashes for validation)
            $kontakResmiClean = preg_replace('/[^0-9]/', '', $data['kontak_resmi']);
            $adminPhoneClean = preg_replace('/[^0-9]/', '', $data['admin_phone']);
            
            // Validate NPWP format (16 digit standard PMK 112/2022)
            if (!empty($data['npwp'])) {
                if (strlen($npwpClean) === 16) {
                    if (!preg_match('/^[0-9]{16}$/', $npwpClean)) {
                        return ['success' => false, 'message' => 'Format NPWP 16 digit tidak valid'];
                    }
                } elseif (strlen($npwpClean) === 15) {
                    if (!preg_match('/^[0-9]{15}$/', $npwpClean)) {
                        return ['success' => false, 'message' => 'Format NPWP 15 digit tidak valid'];
                    }
                } else {
                    return ['success' => false, 'message' => 'Format NPWP harus 15 atau 16 digit'];
                }
            }
            
            // Update cooperative basic info
            $stmt = $this->coopDB->prepare("
                UPDATE cooperatives SET 
                    nama = ?, jenis = ?, badan_hukum = ?, tanggal_pendirian = ?, 
                    npwp = ?, kontak_resmi = ?, updated_at = CURRENT_TIMESTAMP()
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['nama_koperasi'],
                json_encode($data['jenis']),
                $data['badan_hukum'],
                $data['tanggal_pendirian'],
                $data['npwp'],
                $data['kontak_resmi'],
                $id
            ]);
            
            // Track status change if badan_hukum status changed
            if ($current['badan_hukum'] !== $data['badan_hukum']) {
                $this->trackStatusChange($id, $current['badan_hukum'], $data['badan_hukum'], $_SESSION['user_id'] ?? null, 'Update status badan hukum');
            }
            
            $this->coopDB->commit();
            return ['success' => true, 'message' => 'Informasi koperasi berhasil diperbarui'];
            
        } catch (Exception $e) {
            $this->coopDB->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // Update legal information with proper document tracking
    public function updateLegalInformation($id, $data) {
        try {
            $this->coopDB->beginTransaction();
            
            // Get current data
            $currentStmt = $this->coopDB->prepare("SELECT * FROM cooperatives WHERE id = ?");
            $currentStmt->execute([$id]);
            $current = $currentStmt->fetch();
            
            if (!$current) {
                return ['success' => false, 'message' => 'Koperasi tidak ditemukan'];
            }
            
            // Validate legal fields
            if (!empty($data['nomor_bh'])) {
                if (!preg_match('/^AHU-[0-9]{8}\.[A-Z]{2}\.[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/', $data['nomor_bh'])) {
                    return ['success' => false, 'message' => 'Format Nomor Badan Hukum tidak valid'];
                }
            }
            
            if (!empty($data['nib'])) {
                if (!preg_match('/^[0-9]{13}$/', $data['nib'])) {
                    return ['success' => false, 'message' => 'NIB harus 13 digit'];
                }
            }
            
            if (!empty($data['nik_koperasi'])) {
                if (!preg_match('/^[0-9]{16}$/', $data['nik_koperasi'])) {
                    return ['success' => false, 'message' => 'NIK Koperasi harus 16 digit'];
                }
            }
            
            if (!empty($data['modal_pokok'])) {
                if (!is_numeric($data['modal_pokok']) || $data['modal_pokok'] < 0) {
                    return ['success' => false, 'message' => 'Modal pokok harus angka positif'];
                }
            }
            
            // Track document changes before update
            $userId = $_SESSION['user_id'] ?? null;
            
            // Track nomor_bh change
            if ($current['nomor_bh'] !== ($data['nomor_bh'] ?? null)) {
                $this->trackDocumentChange($id, 'nomor_bh', $current['nomor_bh'], $data['nomor_bh'] ?? null, null, null, $userId, 'Update Nomor Badan Hukum');
            }
            
            // Track nib change
            if ($current['nib'] !== ($data['nib'] ?? null)) {
                $this->trackDocumentChange($id, 'nib', $current['nib'], $data['nib'] ?? null, null, null, $userId, 'Update NIB');
            }
            
            // Track nik_koperasi change
            if ($current['nik_koperasi'] !== ($data['nik_koperasi'] ?? null)) {
                $this->trackDocumentChange($id, 'nik_koperasi', $current['nik_koperasi'], $data['nik_koperasi'] ?? null, null, null, $userId, 'Update NIK Koperasi');
            }
            
            // Track modal_pokok change
            if ($current['modal_pokok'] != ($data['modal_pokok'] ?? 0)) {
                $this->trackDocumentChange($id, 'modal_pokok', null, null, $current['modal_pokok'], $data['modal_pokok'] ?? 0, $userId, 'Update Modal Pokok');
            }
            
            // Update legal information
            $stmt = $this->coopDB->prepare("
                UPDATE cooperatives SET 
                    nomor_bh = ?, nib = ?, nik_koperasi = ?, modal_pokok = ?, 
                    status_notes = ?, updated_at = CURRENT_TIMESTAMP()
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['nomor_bh'] ?? null,
                $data['nib'] ?? null,
                $data['nik_koperasi'] ?? null,
                $data['modal_pokok'] ?? 0,
                $data['status_notes'] ?? null,
                $id
            ]);
            
            $this->coopDB->commit();
            return ['success' => true, 'message' => 'Informasi legal berhasil diperbarui'];
            
        } catch (Exception $e) {
            $this->coopDB->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // Track document changes using stored procedure
    private function trackDocumentChange($cooperativeId, $documentType, $oldValue, $newValue, $oldDecimal, $newDecimal, $userId, $reason) {
        try {
            $stmt = $this->coopDB->prepare("CALL track_document_change(?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$cooperativeId, $documentType, $oldValue, $newValue, $oldDecimal, $newDecimal, $userId, $reason]);
        } catch (Exception $e) {
            // Log error but don't fail the main transaction
            error_log('Error tracking document change: ' . $e->getMessage());
        }
    }

    // Get status history
    public function getStatusHistory($cooperativeId) {
        try {
            $stmt = $this->coopDB->prepare("
                SELECT h.*, u.username as user_name
                FROM cooperative_status_history h
                LEFT JOIN users u ON h.user_id = u.id
                WHERE h.cooperative_id = ?
                ORDER BY h.created_at DESC
            ");
            $stmt->execute([$cooperativeId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    // Get document history
    public function getDocumentHistory($cooperativeId) {
        try {
            $stmt = $this->coopDB->prepare("
                SELECT h.*, u.username as user_name
                FROM cooperative_document_history h
                LEFT JOIN users u ON h.user_id = u.id
                WHERE h.cooperative_id = ?
                ORDER BY h.created_at DESC
            ");
            $stmt->execute([$cooperativeId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    // Get current legal documents
    public function getCurrentLegalDocuments($cooperativeId) {
        try {
            $stmt = $this->coopDB->prepare("
                SELECT nomor_bh, nib, nik_koperasi, modal_pokok, status_badan_hukum, tanggal_status_terakhir
                FROM cooperatives
                WHERE id = ?
            ");
            $stmt->execute([$cooperativeId]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }

    // Update modal pokok from RAT
    public function updateModalPokokFromRAT($cooperativeId, $tahun, $modalPokokBaru, $alasan, $userId) {
        try {
            $this->coopDB->beginTransaction();
            
            // Validate modal pokok
            if (!is_numeric($modalPokokBaru) || $modalPokokBaru < 0) {
                return ['success' => false, 'message' => 'Modal pokok harus angka positif'];
            }
            
            // Call stored procedure
            $stmt = $this->coopDB->prepare("CALL update_modal_pokok_from_rat(?, ?, ?, ?, ?)");
            $stmt->execute([$cooperativeId, $tahun, $modalPokokBaru, $alasan, $userId]);
            
            $this->coopDB->commit();
            return ['success' => true, 'message' => 'Modal pokok berhasil diperbarui dari hasil RAT'];
            
        } catch (Exception $e) {
            $this->coopDB->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // Update modal pokok manually
    public function updateModalPokokManual($cooperativeId, $modalPokokBaru, $alasan, $userId) {
        try {
            $this->coopDB->beginTransaction();
            
            // Validate modal pokok
            if (!is_numeric($modalPokokBaru) || $modalPokokBaru < 0) {
                return ['success' => false, 'message' => 'Modal pokok harus angka positif'];
            }
            
            // Call stored procedure
            $stmt = $this->coopDB->prepare("CALL update_modal_pokok_manual(?, ?, ?, ?)");
            $stmt->execute([$cooperativeId, $modalPokokBaru, $alasan, $userId]);
            
            $this->coopDB->commit();
            return ['success' => true, 'message' => 'Modal pokok berhasil diperbarui'];
            
        } catch (Exception $e) {
            $this->coopDB->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // Get modal pokok history
    public function getModalPokokHistory($cooperativeId) {
        try {
            $stmt = $this->coopDB->prepare("
                SELECT h.*, u.username as user_name,
                       CASE 
                           WHEN h.referensi_id IS NOT NULL THEN 
                               CONCAT('RAT Tahun ', (SELECT tahun FROM rat_sessions WHERE id = h.referensi_id))
                           ELSE 'Manual'
                       END as perubahan_sumber
                FROM modal_pokok_changes h
                LEFT JOIN users u ON h.user_id = u.id
                LEFT JOIN rat_sessions r ON h.referensi_id = r.id
                WHERE h.cooperative_id = ?
                ORDER BY h.created_at DESC
            ");
            $stmt->execute([$cooperativeId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    // Get RAT sessions for cooperative
    public function getRATSessions($cooperativeId) {
        try {
            $stmt = $this->coopDB->prepare("
                SELECT r.*, u.username as approved_by_name
                FROM rat_sessions r
                LEFT JOIN users u ON r.approved_by = u.id
                WHERE r.cooperative_id = ?
                ORDER BY r.tahun DESC, r.tanggal_rapat DESC
            ");
            $stmt->execute([$cooperativeId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    // Create new RAT session
    public function createRATSession($cooperativeId, $tahun, $tanggalRapat, $tempat, $agenda, $userId) {
        try {
            $this->coopDB->beginTransaction();
            
            // Get current modal pokok
            $currentStmt = $this->coopDB->prepare("SELECT modal_pokok FROM cooperatives WHERE id = ?");
            $currentStmt->execute([$cooperativeId]);
            $currentModalPokok = $currentStmt->fetch()['modal_pokok'];
            
            $stmt = $this->coopDB->prepare("
                INSERT INTO rat_sessions (
                    cooperative_id, tahun, tanggal_rapat, tempat, agenda, 
                    modal_pokok_sebelum, status, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, 'scheduled', ?)
            ");
            
            $stmt->execute([
                $cooperativeId, $tahun, $tanggalRapat, $tempat, $agenda,
                $currentModalPokok, $userId
            ]);
            
            $this->coopDB->commit();
            return ['success' => true, 'message' => 'Sesi RAT berhasil dibuat'];
            
        } catch (Exception $e) {
            $this->coopDB->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // Track status changes using stored procedure
    private function trackStatusChange($cooperativeId, $oldStatus, $newStatus, $userId, $reason) {
        try {
            $stmt = $this->coopDB->prepare("CALL track_status_change(?, ?, ?, ?, ?)");
            $stmt->execute([$cooperativeId, $oldStatus, $newStatus, $userId, $reason]);
        } catch (Exception $e) {
            // Log error but don't fail the main transaction
            error_log('Error tracking status change: ' . $e->getMessage());
        }
    }
    
    // Trigger documentation update
    private function triggerDocumentationUpdate($event, $data) {
        try {
            // Only trigger if auto update is enabled
            if (!defined('DOCUMENTATION_AUTO_UPDATE') || !DOCUMENTATION_AUTO_UPDATE) {
                return;
            }
            
            // Load documentation integration
            require_once __DIR__ . '/../src/lib/DocumentationIntegration.php';
            
            $integration = new DocumentationIntegration();
            $integration->initialize();
            
            // Trigger based on event type
            switch ($event) {
                case 'cooperative_created':
                    $integration->afterCooperativeCreated($data);
                    break;
                case 'member_registered':
                    $integration->afterMemberRegistered($data);
                    break;
                case 'financial_settings_updated':
                    $integration->afterFinancialSettingsUpdate($data);
                    break;
                case 'legal_document_updated':
                    $integration->afterLegalDocumentUpdate($data);
                    break;
                case 'rat_session_completed':
                    $integration->afterRATSession($data);
                    break;
                default:
                    // Generic trigger
                    $integration->triggerAfterFormChanges();
                    break;
            }
            
        } catch (Exception $e) {
            // Log error but don't fail the main transaction
            error_log('Error triggering documentation update: ' . $e->getMessage());
        }
    }
}
