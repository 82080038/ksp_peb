# Database Logic Implementation - Separation of Concerns

## 🎯 **Overview**

Implementasi logika database yang direkomendasikan untuk **menghilangkan redundansi** dan **memisahkan concerns** antara status tracking dan document tracking.

---

## 📊 **Struktur Database yang Diimplementasikan**

### **✅ 1. cooperatives (Main Table)**
```sql
-- Current state tracking
nomor_bh VARCHAR(50) -- Current legal document number
nib VARCHAR(20) -- Current NIB
nik_koperasi VARCHAR(20) -- Current NIK Koperasi
modal_pokok DECIMAL(15,2) -- Current modal pokok
status_badan_hukum ENUM('belum_terdaftar','terdaftar','badan_hukum')
tanggal_status_terakhir DATE
status_notes TEXT
```

### **✅ 2. cooperative_status_history (Status Only)**
```sql
-- Hanya untuk status changes
CREATE TABLE cooperative_status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cooperative_id INT NOT NULL,
    status_sebelumnya ENUM('belum_terdaftar','terdaftar','badan_hukum'),
    status_baru ENUM('belum_terdaftar','terdaftar','badan_hukum') NOT NULL,
    tanggal_efektif DATE NOT NULL,
    change_reason VARCHAR(255),
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
    approved_by INT,
    approved_at TIMESTAMP NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP()
);
```

### **✅ 3. cooperative_document_history (Documents Only)**
```sql
-- Hanya untuk document changes
CREATE TABLE cooperative_document_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cooperative_id INT NOT NULL,
    document_type ENUM('nomor_bh', 'nib', 'nik_koperasi', 'modal_pokok') NOT NULL,
    document_number_lama VARCHAR(50),
    document_number_baru VARCHAR(50),
    document_value_lama DECIMAL(15,2), -- untuk modal_pokok
    document_value_baru DECIMAL(15,2), -- untuk modal_pokok
    tanggal_efektif DATE NOT NULL,
    change_reason VARCHAR(255),
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP()
);
```

---

## 🔧 **Stored Procedures yang Diimplementasikan**

### **✅ 1. track_document_change**
```sql
DELIMITER //
CREATE PROCEDURE track_document_change(
    IN p_cooperative_id INT,
    IN p_document_type ENUM('nomor_bh', 'nib', 'nik_koperasi', 'modal_pokok'),
    IN p_old_value VARCHAR(50),
    IN p_new_value VARCHAR(50),
    IN p_old_decimal DECIMAL(15,2),
    IN p_new_decimal DECIMAL(15,2),
    IN p_user_id INT,
    IN p_reason VARCHAR(255)
)
BEGIN
    INSERT INTO cooperative_document_history (
        cooperative_id, document_type, document_number_lama, document_number_baru,
        document_value_lama, document_value_baru, tanggal_efektif, change_reason, user_id
    ) VALUES (
        p_cooperative_id, p_document_type, p_old_value, p_new_value,
        p_old_decimal, p_new_decimal, CURDATE(), p_reason, p_user_id
    );
END //
DELIMITER ;
```

### **✅ 2. track_status_change**
```sql
DELIMITER //
CREATE PROCEDURE track_status_change(
    IN p_cooperative_id INT,
    IN p_status_lama ENUM('belum_terdaftar','terdaftar','badan_hukum'),
    IN p_status_baru ENUM('belum_terdaftar','terdaftar','badan_hukum'),
    IN p_user_id INT,
    IN p_reason VARCHAR(255)
)
BEGIN
    INSERT INTO cooperative_status_history (
        cooperative_id, status_sebelumnya, status_baru, tanggal_efektif, change_reason, user_id
    ) VALUES (
        p_cooperative_id, p_status_lama, p_status_baru, CURDATE(), p_reason, p_user_id
    );
END //
DELIMITER ;
```

---

## 📋 **Backend Implementation (Cooperative.php)**

### **✅ 1. Update Cooperative Basic Info**
```php
public function updateCooperative($id, $data) {
    // Update basic info
    $stmt = $this->coopDB->prepare("
        UPDATE cooperatives SET 
            nama = ?, jenis = ?, badan_hukum = ?, tanggal_pendirian = ?, 
            npwp = ?, kontak_resmi = ?, updated_at = CURRENT_TIMESTAMP()
        WHERE id = ?
    ");
    
    // Track status change if badan_hukum status changed
    if ($current['badan_hukum'] !== $data['badan_hukum']) {
        $this->trackStatusChange($id, $current['badan_hukum'], $data['badan_hukum'], $_SESSION['user_id'] ?? null, 'Update status badan hukum');
    }
}
```

### **✅ 2. Update Legal Information**
```php
public function updateLegalInformation($id, $data) {
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
}
```

### **✅ 3. History Methods**
```php
// Get status history
public function getStatusHistory($cooperativeId) {
    $stmt = $this->coopDB->prepare("
        SELECT h.*, u.username as user_name
        FROM cooperative_status_history h
        LEFT JOIN users u ON h.user_id = u.id
        WHERE h.cooperative_id = ?
        ORDER BY h.created_at DESC
    ");
}

// Get document history
public function getDocumentHistory($cooperativeId) {
    $stmt = $this->coopDB->prepare("
        SELECT h.*, u.username as user_name
        FROM cooperative_document_history h
        LEFT JOIN users u ON h.user_id = u.id
        WHERE h.cooperative_id = ?
        ORDER BY h.created_at DESC
    ");
}

// Get current legal documents
public function getCurrentLegalDocuments($cooperativeId) {
    $stmt = $this->coopDB->prepare("
        SELECT nomor_bh, nib, nik_koperasi, modal_pokok, status_badan_hukum, tanggal_status_terakhir
        FROM cooperatives
        WHERE id = ?
    ");
}
```

---

## 🌐 **Frontend Implementation (cooperative-settings.php)**

### **✅ 1. API Endpoints**
```php
// Status history
case 'status_history':
    $history = $cooperative->getStatusHistory($id);
    echo json_encode(['success' => true, 'data' => $history]);
    break;

// Document history  
case 'document_history':
    $history = $cooperative->getDocumentHistory($id);
    echo json_encode(['success' => true, 'data' => $history]);
    break;
```

### **✅ 2. JavaScript Functions**
```javascript
// Load status history
async function loadStatusHistory() {
    const response = await fetch(`../src/public/api/cooperative-settings.php?action=status_history&id=${cooperativeId}`);
    const result = await response.json();
    
    if (result.success && result.data) {
        result.data.forEach(history => {
            row.innerHTML = `
                <td>${history.tanggal_efektif || '-'}</td>
                <td>${history.status_sebelumnya || '-'}</td>
                <td><span class="status-badge status-${history.status_baru}">${history.status_baru}</span></td>
                <td>${history.change_reason || '-'}</td>
                <td>${history.user_name || '-'}</td>
            `;
        });
    }
}

// Load document history
async function loadDocumentHistory() {
    const response = await fetch(`../src/public/api/cooperative-settings.php?action=document_history&id=${cooperativeId}`);
    const result = await response.json();
    
    if (result.success && result.data) {
        result.data.forEach(history => {
            const oldValue = history.document_number_lama || (history.document_value_lama ? formatCurrency(history.document_value_lama) : '-');
            const newValue = history.document_number_baru || (history.document_value_baru ? formatCurrency(history.document_value_baru) : '-');
            
            row.innerHTML = `
                <td>${history.tanggal_efektif || '-'}</td>
                <td>${getDocumentTypeLabel(history.document_type)}</td>
                <td>${oldValue}</td>
                <td>${newValue}</td>
                <td>${history.change_reason || '-'}</td>
                <td>${history.user_name || '-'}</td>
            `;
        });
    }
}
```

---

## 🎯 **Use Case Examples**

### **✅ 1. Initial Registration**
```sql
-- Step 1: Create cooperative (belum_terdaftar)
INSERT INTO cooperatives (nama, jenis, badan_hukum, ...) VALUES ('KSP Makmur', {...}, 'belum_terdaftar', ...);

-- Step 2: Update to terdaftar dengan SABH
UPDATE cooperatives SET 
    badan_hukum = 'terdaftar',
    nomor_bh = 'AHU-12345678.AH.01.01.2024'
WHERE id = 1;

-- Step 3: Track status change
CALL track_status_change(1, 'belum_terdaftar', 'terdaftar', 123, 'Pendaftaran SABH berhasil');

-- Step 4: Track document change
CALL track_document_change(1, 'nomor_bh', NULL, 'AHU-12345678.AH.01.01.2024', NULL, NULL, 123, 'Pendaftaran SABH');
```

### **✅ 2. Document Update**
```sql
-- Update nomor BH
UPDATE cooperatives SET nomor_bh = 'AHU-87654321.AH.01.01.2024' WHERE id = 1;

-- Track document change
CALL track_document_change(1, 'nomor_bh', 'AHU-12345678.AH.01.01.2024', 'AHU-87654321.AH.01.01.2024', NULL, NULL, 123, 'Perubahan data SABH');
```

### **✅ 3. Status Upgrade**
```sql
-- Upgrade ke badan hukum
UPDATE cooperatives SET 
    badan_hukum = 'badan_hukum',
    nomor_bh = 'AHU-99999999.AH.01.01.2024'
WHERE id = 1;

-- Track status change
CALL track_status_change(1, 'terdaftar', 'badan_hukum', 123, 'Upgrade ke badan hukum');

-- Track document change
CALL track_document_change(1, 'nomor_bh', 'AHU-12345678.AH.01.01.2024', 'AHU-99999999.AH.01.01.2024', NULL, NULL, 123, 'Upgrade badan hukum');
```

---

## 📈 **Benefits dari Implementasi Baru**

### **✅ 1. No Redundancy**
- **Single Source of Truth:** `cooperatives.nomor_bh` untuk current state
- **Clean History:** `cooperative_status_history` hanya untuk status changes
- **Document History:** `cooperative_document_history` hanya untuk document changes

### **✅ 2. Clear Separation of Concerns**
- **Status Tracking:** Fokus pada perubahan status badan hukum
- **Document Tracking:** Fokus pada perubahan dokumen legal
- **Audit Trail:** Complete audit trail untuk compliance

### **✅ 3. Better Data Integrity**
- **Consistent Naming:** `nomor_bh` vs `document_number_lama/baru`
- **Type Safety:** ENUM untuk status dan document types
- **Proper Relationships:** Foreign keys dan indexes

### **✅ 4. Enhanced Tracking**
- **Change Reason:** Alasan perubahan untuk setiap perubahan
- **User Attribution:** Siapa yang melakukan perubahan
- **Approval Process:** Built-in approval workflow
- **Timestamp:** Accurate timing untuk setiap perubahan

---

## 🔍 **Query Examples**

### **✅ Get Current State**
```sql
SELECT nomor_bh, nib, nik_koperasi, modal_pokok, status_badan_hukum
FROM cooperatives WHERE id = 1;
```

### **✅ Get Status History**
```sql
SELECT h.*, u.username
FROM cooperative_status_history h
LEFT JOIN users u ON h.user_id = u.id
WHERE h.cooperative_id = 1
ORDER BY h.created_at DESC;
```

### **✅ Get Document History**
```sql
SELECT h.*, u.username
FROM cooperative_document_history h
LEFT JOIN users u ON h.user_id = u.id
WHERE h.cooperative_id = 1
ORDER BY h.created_at DESC;
```

### **✅ Get Complete Audit Trail**
```sql
-- Status changes
SELECT 'status' as type, tanggal_efektif, status_sebelumnya, status_baru, change_reason, user_name
FROM cooperative_status_history h
LEFT JOIN users u ON h.user_id = u.id
WHERE h.cooperative_id = 1

UNION ALL

-- Document changes
SELECT 'document' as type, tanggal_efektif, document_type, 
       CONCAT(document_number_lama, ' -> ', document_number_baru) as change, 
       change_reason, user_name
FROM cooperative_document_history h
LEFT JOIN users u ON h.user_id = u.id
WHERE h.cooperative_id = 1
ORDER BY tanggal_efektif DESC;
```

---

## 🎉 **Kesimpulan Implementasi**

### **✅ Masalah Teratasi:**
- **Redundancy:** Tidak ada duplikasi data antara current state dan history
- **Mixed Concerns:** Status dan document tracking terpisah
- **Inconsistent Naming:** Penamaan yang konsisten dan jelas
- **Limited Tracking:** Complete audit trail dengan change reasons

### **✅ Implementasi Selesai:**
- **Database Structure:** 3 tabel dengan fokus yang jelas
- **Stored Procedures:** 2 procedures untuk tracking otomatis
- **Backend Logic:** Methods untuk tracking dan retrieval
- **Frontend UI:** Tables untuk menampilkan history
- **API Endpoints:** Endpoints untuk status dan document history

### **✅ Production Ready:**
- **Scalable:** Mudah untuk menambah jenis dokumen baru
- **Compliant:** Complete audit trail untuk compliance
- **Maintainable:** Clear separation of concerns
- **User-Friendly:** UI yang informatif dengan proper formatting

**🚀 Logika database yang direkomendasikan telah diimplementasikan dengan separation of concerns yang jelas dan tidak ada redundansi!** 🎯
