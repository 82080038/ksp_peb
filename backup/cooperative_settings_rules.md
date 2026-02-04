# Cooperative Settings - Aturan dan Proses

## 📋 **Overview**

Halaman **Cooperative Settings** adalah interface admin untuk mengelola informasi lengkap koperasi, termasuk data-data yang dipindahkan dari form registrasi awal.

---

## 🎯 **Tujuan dan Fungsi**

### **✅ Primary Functions:**
1. **Update Informasi Dasar** - Nama, jenis, status badan hukum
2. **Manage Legal Documents** - SABH, NIB, NIK Koperasi, Modal Pokok
3. **Track Status Changes** - History perubahan status badan hukum
4. **Compliance Management** - Dokumen legal sesuai regulasi

### **✅ User Roles:**
- **Super Admin** - Full access ke semua settings
- **Admin Koperasi** - Update informasi dasar dan legal
- **Viewer** - Read-only access

---

## 📊 **Struktur Halaman**

### **🏢 Section 1: Informasi Dasar**
```html
Fields:
- Nama Koperasi (required)
- Jenis Koperasi (required, PP 7/2021)
- Status Badan Hukum (required, UU 25/1992 & UU 6/2023)
- Tanggal Pendirian (required)
- NPWP (required, PMK 112/2022)
- Kontak Resmi (required)
```

### **🛡️ Section 2: Informasi Legal**
```html
Fields:
- Nomor Badan Hukum (SABH) - optional
- NIB (OSS) - optional
- NIK Koperasi - optional
- Modal Pokok - optional, currency format
- Catatan Status - optional, textarea
```

### **📈 Section 3: Riwayat Status**
```html
Table Columns:
- Tanggal Perubahan
- Status Sebelumnya
- Status Baru
- Nomor BH
- NIB
- NIK Koperasi
- Diubah Oleh
```

---

## 🔄 **Proses Bisnis**

### **📝 Workflow Update Informasi Dasar:**
```
Admin Login → Navigate Settings → Edit Basic Info → Validate → Save → Record History
```

### **📋 Workflow Update Legal:**
```
Admin Login → Navigate Settings → Edit Legal Info → Validate → Save → Record History
```

### **📊 Workflow Status Change:**
```
Belum Terdaftar → Terdaftar (SABH) → Badan Hukum (SABH)
    ↓                    ↓                     ↓
No Documents        SABH Number          Complete Legal
```

---

## 🛡️ **Aturan Validasi**

### **✅ Informasi Dasar:**
```php
// Nama Koperasi
- Required: Yes
- Max Length: 255 chars
- Unique: Yes

// Jenis Koperasi
- Required: Yes
- Source: cooperative_types table
- Format: Code (KSP, KK, KP, etc.)

// Status Badan Hukum
- Required: Yes
- Options: belum_terdaftar, terdaftar, badan_hukum
- Validation: Dropdown selection

// Tanggal Pendirian
- Required: Yes
- Format: YYYY-MM-DD
- Validation: Valid date

// NPWP
- Required: Yes
- Format: 15 atau 16 digit
- Validation: Regex /^[0-9]{15,16}$/

// Kontak Resmi
- Required: Yes
- Format: 08[0-9]{9,12}
- Validation: Regex /^08[0-9]{9,12}$/
```

### **✅ Informasi Legal:**
```php
// Nomor Badan Hukum (SABH)
- Required: No
- Format: AHU-XXXXXXXX.AH.01.01.TAHUN
- Validation: Regex /^AHU-[0-9]{8}\.[A-Z]{2}\.[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/

// NIB
- Required: No
- Format: 13 digit
- Validation: Regex /^[0-9]{13}$/

// NIK Koperasi
- Required: No
- Format: 16 digit
- Validation: Regex /^[0-9]{16}$/

// Modal Pokok
- Required: No
- Format: Decimal
- Validation: is_numeric() && >= 0
- Display: Rupiah format

// Catatan Status
- Required: No
- Format: Text
- Max Length: 1000 chars
```

---

## 🔐 **Security & Access Control**

### **✅ Authentication:**
```php
// Session Validation
if (!isset($_SESSION['user_id']) || !isset($_SESSION['cooperative_id'])) {
    header('Location: ../login.php');
    exit;
}
```

### **✅ Authorization:**
```php
// Role-based Access
$allowedRoles = ['super_admin', 'admin'];
if (!in_array($_SESSION['user_role'], $allowedRoles)) {
    // Redirect or show error
}
```

### **✅ Data Validation:**
```php
// Server-side validation
$cooperative = new Cooperative();
$result = $cooperative->updateCooperative($id, $data);

// Transaction rollback on error
try {
    $this->coopDB->beginTransaction();
    // Operations
    $this->coopDB->commit();
} catch (Exception $e) {
    $this->coopDB->rollBack();
    return error response;
}
```

---

## 📊 **Audit Trail & History**

### **✅ Automatic Recording:**
```php
// Status Changes
recordStatusHistory($cooperativeId, $oldStatus, $newStatus);

// Legal Document Updates
recordLegalHistory($cooperativeId, $data);

// User Tracking
$user_id = $_SESSION['user_id'];
$timestamp = CURRENT_TIMESTAMP;
```

### **✅ History Table Structure:**
```sql
cooperative_status_history:
- cooperative_id
- status_sebelumnya
- status_baru
- nomor_bh_baru
- nib_baru
- nik_koperasi_baru
- tanggal_efektif
- user_id
- created_at
```

---

## 🎯 **Business Rules**

### **✅ Status Progression:**
1. **Belum Terdaftar** → Koperasi baru dibuat
2. **Terdaftar (SABH)** → Submit ke SABH
3. **Badan Hukum (SABH)** → SABH disetujui

### **✅ Document Requirements:**
- **SABH Number** → Required untuk status "Terdaftar" dan "Badan Hukum"
- **NIB** → Required untuk operasional bisnis
- **NIK Koperasi** → Required untuk identifikasi resmi
- **Modal Pokok** → Required untuk financial reporting

### **✅ Validation Rules:**
- **NPWP** → 16 digit (PMK 112/2022) atau 15 digit (legacy)
- **Phone** → Format Indonesia (08xxxxxxxx)
- **Currency** → Rupiah format untuk display, clean number untuk storage

---

## 🔄 **API Endpoints**

### **✅ Update Basic Info:**
```http
POST /api/cooperative-settings.php?action=update&id={cooperative_id}
Content-Type: application/json

{
    "nama_koperasi": "Koperasi Baru",
    "jenis": "KSP",
    "badan_hukum": "terdaftar",
    "tanggal_pendirian": "2024-01-01",
    "npwp": "3201234567890001",
    "kontak_resmi": "08123456789"
}
```

### **✅ Update Legal Info:**
```http
POST /api/cooperative-settings.php?action=update_legal&id={cooperative_id}
Content-Type: application/json

{
    "nomor_bh": "AHU-12345678.AH.01.01.2024",
    "nib": "1234567890123",
    "nik_koperasi": "3201234567890001",
    "modal_pokok": "100000000",
    "status_notes": "Status updated dengan SABH"
}
```

### **✅ Get Status History:**
```http
GET /api/cooperative-settings.php?action=status_history&id={cooperative_id}
```

---

## 🎨 **UI/UX Guidelines**

### **✅ Form Layout:**
- **Two-column layout** untuk desktop
- **Responsive design** untuk mobile
- **Clear section separation** dengan headers
- **Status badges** untuk visual indicators

### **✅ User Feedback:**
- **Success messages** untuk completed actions
- **Error messages** dengan specific details
- **Loading states** untuk async operations
- **Confirmation dialogs** untuk critical changes

### **✅ Accessibility:**
- **Proper labels** untuk semua form fields
- **ARIA labels** untuk screen readers
- **Keyboard navigation** support
- **Color contrast** compliance

---

## 📱 **Mobile Responsiveness**

### **✅ Breakpoints:**
- **Desktop** (>1200px) - Two columns
- **Tablet** (768px-1200px) - Single column, wider inputs
- **Mobile** (<768px) - Single column, stacked inputs

### **✅ Touch Optimization:**
- **Larger touch targets** (44px minimum)
- **Proper spacing** between elements
- **Swipe gestures** untuk navigation
- **Native mobile keyboards** untuk input types

---

## 🎉 **Success Metrics**

### **✅ KPIs:**
- **Form Completion Rate** > 95%
- **Validation Error Rate** < 5%
- **Page Load Time** < 2 seconds
- **User Satisfaction** > 4.5/5

### **✅ Monitoring:**
- **Error tracking** dengan logging
- **Performance monitoring** dengan metrics
- **User behavior analytics**
- **A/B testing** untuk improvements

---

## 🔄 **Future Enhancements**

### **✅ Planned Features:**
1. **Document Upload** - Upload SABH, NIB documents
2. **Digital Signatures** - Electronic signing capability
3. **Automated Validation** - API integration with government systems
4. **Workflow Automation** - Auto-approval for certain changes
5. **Reporting Dashboard** - Analytics and compliance reports

### **✅ Integration Points:**
- **SABH API** - Direct integration with AHU system
- **OSS API** - Real-time NIB validation
- **DJP API** - NPWP verification
- **Kemenkop API** - NIK Koperasi validation

---

## 🎯 **Conclusion**

Halaman **Cooperative Settings** menyediakan interface yang komprehensif untuk mengelola informasi koperasi dengan:

✅ **Complete Data Management** - Semua field yang dipindahkan dari registrasi
✅ **Compliance Ready** - Sesuai regulasi terbaru
✅ **Audit Trail** - Complete history tracking
✅ **User-Friendly** - Intuitive interface dengan proper validation
✅ **Secure** - Proper authentication dan authorization
✅ **Scalable** - Ready untuk future enhancements

**🚀 Data koperasi sekarang aman dan terkelola dengan proper system!**
