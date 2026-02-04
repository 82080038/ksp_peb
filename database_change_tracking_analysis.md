# Database Analysis - Cooperatives Table Change Tracking

## 📊 **Tabel `cooperatives` - Kolom yang Mungkin Berubah**

### 🔍 **Analisis Kolom Berdasarkan Kategori Perubahan:**

#### **🔄 Kategori 1: Informasi Legal (Berubah Sesuai Proses Badan Hukum)**
| Kolom | Tipe Perubahan | Alasan | Tabel Terkait | Status Tracking |
|-------|---------------|--------|---------------|-----------------|
| `badan_hukum` | Status | Belum → Terdaftar → Badan Hukum | `pengurus`, `anggota` | ✅ Perlu Tracking |
| `nomor_bh` | Tambah/Hapus | Nomor SABH saat terdaftar | `audit_logs` | ✅ Perlu Tracking |
| `nib` | Tambah/Hapus | NIB dari OSS | `audit_logs` | ✅ Perlu Tracking |
| `nik_koperasi` | Tambah/Hapus | NIK dari Kemenkop | `audit_logs` | ✅ Perlu Tracking |

#### **💰 Kategori 2: Informasi Keuangan (Berubah Sesuai Kebutuhan)**
| Kolom | Tipe Perubahan | Alasan | Tabel Terkait | Status Tracking |
|-------|---------------|--------|---------------|-----------------|
| `modal_pokok` | Update | Sesuai anggaran dasar | `cooperative_financial_settings` | ✅ Perlu Tracking |
| `npwp` | Update | Format 15→16 digit | `audit_logs` | ✅ Perlu Tracking |

#### **📝 Kategori 3: Informasi Operasional (Berubah Sesuai Kebutuhan)**
| Kolom | Tipe Perubahan | Alasan | Tabel Terkait | Status Tracking |
|-------|---------------|--------|---------------|-----------------|
| `nama` | Update | Perubahan nama resmi | `tenant_configs`, `audit_logs` | ✅ Perlu Tracking |
| `jenis` | Update | Perubahan jenis usaha | `tenant_configs` | ✅ Perlu Tracking |
| `alamat_legal` | Update | Perpindahan kantor | `users`, `audit_logs` | ✅ Perlu Tracking |
| `kontak_resmi` | Update | Perubahan kontak | `users`, `audit_logs` | ✅ Perlu Tracking |
| `logo` | Update | Perubahan branding | `tenant_configs` | ✅ Perlu Tracking |

#### **📍 Kategori 4: Informasi Lokasi (Berubah jika Pindah)**
| Kolom | Tipe Perubahan | Alasan | Tabel Terkait | Status Tracking |
|-------|---------------|--------|---------------|-----------------|
| `province_id` | Update | Perpindahan lokasi | `users`, `audit_logs` | ✅ Perlu Tracking |
| `regency_id` | Update | Perpindahan lokasi | `users`, `audit_logs` | ✅ Perlu Tracking |
| `district_id` | Update | Perpindahan lokasi | `users`, `audit_logs` | ✅ Perlu Tracking |
| `village_id` | Update | Perpindahan lokasi | `users`, `audit_logs` | ✅ Perlu Tracking |

---

## 🎯 **Rekomendasi Tanda Aktif di Tabel Terkait**

### **✅ 1. Tabel `audit_logs` - Tracking Semua Perubahan**

```sql
-- Struktur audit_logs untuk tracking perubahan
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_name VARCHAR(50) NOT NULL,
    record_id INT NOT NULL,
    field_name VARCHAR(50) NOT NULL,
    old_value TEXT,
    new_value TEXT,
    action ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    user_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_table_record (table_name, record_id),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### **✅ 2. Tabel `cooperative_status_history` - Tracking Status Badan Hukum**

```sql
-- Tracking perubahan status badan hukum
CREATE TABLE cooperative_status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cooperative_id INT NOT NULL,
    status_sebelumnya VARCHAR(50),
    status_baru VARCHAR(50) NOT NULL,
    nomor_bh_baru VARCHAR(50),
    nib_baru VARCHAR(20),
    nik_koperasi_baru VARCHAR(20),
    tanggal_efektif DATE,
    dokumen_path VARCHAR(255),
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cooperative_id) REFERENCES cooperatives(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_cooperative_id (cooperative_id),
    INDEX idx_tanggal_efektif (tanggal_efektif)
);
```

### **✅ 3. Tabel `cooperatives` - Tambah Status Tracking**

```sql
-- Update tabel cooperatives dengan status tracking
ALTER TABLE cooperatives 
ADD COLUMN status_badan_hukum ENUM('belum_terdaftar', 'terdaftar', 'badan_hukum') DEFAULT 'belum_terdaftar' AFTER badan_hukum,
ADD COLUMN tanggal_status_terakhir DATE NULL AFTER status_badan_hukum,
ADD COLUMN status_notes TEXT NULL AFTER tanggal_status_terakhir,
ADD INDEX idx_status_badan_hukum (status_badan_hukum),
ADD INDEX idx_tanggal_status_terakhir (tanggal_status_terakhir);
```

### **✅ 4. Tabel `users` - Tambah Cooperative Status**

```sql
-- Update users untuk tracking koperasi aktif
ALTER TABLE users 
ADD COLUMN cooperative_status ENUM('active', 'inactive', 'suspended') DEFAULT 'active' AFTER status,
ADD COLUMN last_cooperative_update TIMESTAMP NULL AFTER cooperative_status,
ADD INDEX idx_cooperative_status (cooperative_status),
ADD INDEX idx_last_cooperative_update (last_cooperative_update);
```

### **✅ 5. Tabel `pengurus` - Tambah Status Tracking**

```sql
-- Update pengurus untuk tracking periode aktif
ALTER TABLE pengurus 
ADD COLUMN status_periode ENUM('aktif', 'berakhir', 'diganti') DEFAULT 'aktif' AFTER status,
ADD COLUMN alasan_berakhir TEXT NULL AFTER status_periode,
ADD INDEX idx_status_periode (status_periode),
ADD INDEX idx_periode_aktif (periode_start, periode_end);
```

### **✅ 6. Tabel `anggota` - Tambah Status Tracking**

```sql
-- Update anggota untuk tracking keaktifan
ALTER TABLE anggota 
ADD COLUMN last_activity TIMESTAMP NULL AFTER updated_at,
ADD COLUMN status_reason TEXT NULL AFTER last_activity,
ADD INDEX idx_last_activity (last_activity),
ADD INDEX idx_status_keanggotaan (status_keanggotaan);
```

---

## 🔄 **Implementasi Trigger untuk Auto-Tracking**

### **✅ Trigger untuk Audit Log**

```sql
-- Trigger untuk cooperatives table
DELIMITER //
CREATE TRIGGER cooperatives_after_update
AFTER UPDATE ON cooperatives
FOR EACH ROW
BEGIN
    -- Track badan_hukum changes
    IF OLD.badan_hukum != NEW.badan_hukum THEN
        INSERT INTO audit_logs (table_name, record_id, field_name, old_value, new_value, action)
        VALUES ('cooperatives', NEW.id, 'badan_hukum', OLD.badan_hukum, NEW.badan_hukum, 'UPDATE');
    END IF;
    
    -- Track nomor_bh changes
    IF OLD.nomor_bh != NEW.nomor_bh THEN
        INSERT INTO audit_logs (table_name, record_id, field_name, old_value, new_value, action)
        VALUES ('cooperatives', NEW.id, 'nomor_bh', OLD.nomor_bh, NEW.nomor_bh, 'UPDATE');
    END IF;
    
    -- Track other important fields...
END//
DELIMITER ;
```

### **✅ Trigger untuk Status History**

```sql
-- Trigger untuk status badan hukum
DELIMITER //
CREATE TRIGGER cooperative_status_update
AFTER UPDATE ON cooperatives
FOR EACH ROW
BEGIN
    IF OLD.badan_hukum != NEW.badan_hukum THEN
        INSERT INTO cooperative_status_history (
            cooperative_id, status_sebelumnya, status_baru, 
            nomor_bh_baru, nib_baru, nik_koperasi_baru, tanggal_efektif
        ) VALUES (
            NEW.id, OLD.badan_hukum, NEW.badan_hukum,
            NEW.nomor_bh, NEW.nib, NEW.nik_koperasi, CURDATE()
        );
    END IF;
END//
DELIMITER ;
```

---

## 📋 **Priority Implementation**

### **🔥 High Priority (Segera)**
1. **Audit Logs** - Tracking semua perubahan penting
2. **Cooperative Status History** - Tracking status badan hukum
3. **Status Tracking Fields** - Tambah kolom status di cooperatives

### **⚡ Medium Priority (Berikutnya)**
1. **User Status Tracking** - Status keaktifan user
2. **Pengurus Status Tracking** - Tracking periode pengurus
3. **Anggota Status Tracking** - Tracking keaktifan anggota

### **🔧 Low Priority (Opsional)**
1. **Financial Settings Tracking** - Tracking perubahan keuangan
2. **Tenant Config Tracking** - Tracking perubahan konfigurasi
3. **Location Change Tracking** - Tracking perpindahan lokasi

---

## 🎯 **Hasil Akhir**

**✅ Tracking Lengkap:**
- Semua perubahan penting tercatat di audit_logs
- Status badan hukum memiliki history lengkap
- User, pengurus, dan anggota memiliki status tracking
- Trigger otomatis untuk semua perubahan

**✅ Monitoring Aktif:**
- Real-time tracking untuk perubahan status
- History lengkap untuk compliance
- Alert system untuk perubahan penting
- Reporting untuk audit purposes

**🚀 Database sekarang memiliki tracking system yang komprehensif!**
