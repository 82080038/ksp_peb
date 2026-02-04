# NPWP Format Update - Peraturan PMK No. 112/2022

## 📋 **Format NPWP Terbaru (Standar 16 Digit)**

### 🇮🇩 **Format 16 Digit Tanpa Separator:**
```
3201234567890001 (16 digit)
```

**Struktur:**
- **9 Digit Pertama:** Kode unik identitas Wajib Pajak
- **3 Digit Setelah Strip:** Kode Kantor Pelayanan Pajak (KPP)
- **3 Digit Terakhir:** Kode status pusat/cabang (000 = Pusat)

---

## 📊 **Perbandingan Format Lama vs Baru**

### **🆕 Format Lama (15 Digit):**
```
01.234.567.8-012.000
```
- **Struktur:** XX.XXX.XXX.X-XXX.XXX
- **Separator:** Titik (.) dan strip (-)
- **Total:** 15 digit + 2 separator = 17 karakter

### **🆕 Format Baru (16 Digit):**
```
3201234567890001
```
- **Struktur:** XXXXXXXXXXXXXXXXXX
- **Separator:** Tidak ada separator
- **Total:** 16 digit murni

---

## 🎯 **Rekomendasi Update Berdasarkan PMK 112/2022**

### **✅ STORAGE FORMAT: Clean 16 Digit (RECOMMENDED)**
**Database:** `3201234567890001`
**Frontend Display:** `3201234567890001` (tanpa formatting)

**Keuntungan:**
- ✅ **Standar Pemerintah** - Sesuai PMK 112/2022
- ✅ **Universal** - Tidak ada karakter tambahan
- **Validation Mudah** - Regex `/^[0-9]{16}$/`
- **Storage Efficient** - 16 digit murni
- **Future-Proof** - Siap untuk perubahan format
- **No Separator Issues** - Tidak ada parsing complexity

### **⚠️ Legacy Support (Optional)**
Untuk backward compatibility dengan format lama:
```javascript
// Detect format and handle accordingly
function detectNPWPFormat(npwp) {
    const clean = npwp.replace(/[^0-9]/g, '');
    if (clean.length === 16) {
        return '16_digit'; // Format baru
    } else if (clean.length === 15) {
        return '15_digit'; // Format lama
    }
    return 'unknown';
}

// Display formatting (if needed)
function formatNPWPDisplay(npwp, format) {
    if (format === '15_digit' && npwp.length === 16) {
        // Convert 16 digit to 15 digit format
        return npwp.slice(0, 2) + '.' + 
               npwp.slice(2, 5) + '.' + 
               npwp.slice(5, 9) + '.' + 
               npwp.slice(9, 12) + '-' + 
               npwp.slice(12, 15) + '.' + 
               npwp.slice(15);
    }
    return npwp; // 16 digit, no formatting
}
```

---

## 🛠️ **Implementasi Terbaru**

### **1. Frontend (16 Digit Clean):**
```javascript
// NPWP 16 digit formatting
function setupNPWPFormatting(fieldId) {
    const element = document.getElementById(fieldId);
    if (element) {
        element.addEventListener('input', () => {
            // Remove all non-digits and limit to 16 digits
            let value = element.value.replace(/[^0-9]/g, '').slice(0, 16);
            element.value = value;
        });
        
        element.addEventListener('blur', () => {
            // Ensure clean format on blur
            let value = element.value.replace(/[^0-9]/g, '').slice(0, 16);
            element.value = value;
        });
    }
}

// Usage in register_cooperative.php
FormHelper.setupNPWPFormatting('npwp');
```

### **2. Backend Validation:**
```php
// NPWP validation for 16 digit format
$npwpClean = preg_replace('/[^0-9]/', '', $data['npwp']);

if (strlen($npwpClean) === 16) {
    // Format 16 digit (baru)
    if (!preg_match('/^[0-9]{16}$/', $npwpClean)) {
        return ['success' => false, 'message' => 'Format NPWP 16 digit tidak valid'];
    }
} elseif (strlen($npwpClean) === 15) {
    // Format 15 digit (lama) - optional legacy support
    if (!preg_match('/^[0-9]{15}$/', $npwpClean)) {
        return ['success' => false, 'message' => 'Format NPWP 15 digit tidak valid'];
    }
} else {
    return ['success' => false, 'message' => 'Format NPWP harus 15 atau 16 digit'];
}
```

### **3. Database Storage:**
```php
// Store clean NPWP number (tanpa formatting)
$stmt->execute([$npwpClean]);
```

---

## 🔄 **Data Flow Terbaru:**

```
Input → 3201234567890001
  ↓
Frontend → 3201234567890001 (clean input)
  ↓
API → 3201234567890001 (clean validation)
  ↓
Database → 3201234567890001 (clean storage)
  ↓
Display → 3201234567890001 (clean display)
```

---

## 📋 **Update Validation Rules:**

### **✅ NPWP Validation Rules:**
```javascript
// Form validation rules
'npwp': {
    label: 'NPWP',
    required: false, // NPWP tidak wajib
    elementId: 'npwp',
    validate: (value) => {
        const clean = value.replace(/[^0-9]/g, '');
        if (clean.length === 0) return true; // Optional field
        if (clean.length === 16) {
            return clean.length === 16 || 'NPWP harus 16 digit';
        } else if (clean.length === 15) {
            return clean.length === 15 || 'NPWP harus 15 digit';
        }
        return 'NPWP harus 15 atau 16 digit';
    }
}
```

---

## 🎯 **Final Recommendation Update**

### **✅ STORAGE FORMAT: Clean 16 Digit**
```
Database: 3201234567890001 (16 digit murni)
Frontend: 3201234567890001 (tanpa formatting)
```

### **📋 Alasan Update:**
1. ✅ **Standar Pemerintah** - Sesuai PMK 112/2022
2. ✅ **Future-Proof** - Siap untuk perubahan format
3. ✅ **Simplicity** - Tidak ada parsing complexity
4. ✅ **Universal** - Tidak ada karakter khusus Indonesia
5. ✅ **Efficiency** - Storage optimal

### **🔄 Legacy Support:**
- **Optional:** Bisa handle format 15 digit jika diperlukan
- **Migration:** Mudah convert dari 15 ke 16 digit
- **Validation:** Bisa detect dan handle kedua format

**🚀 Update ke format 16 digit clean numbers sesuai standar PMK 112/2022!** 🎯
